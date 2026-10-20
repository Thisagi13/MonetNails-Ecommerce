<?php
/**
 * BookingController.php — Appointment Booking Controller
 * Monet Nails MVC Application
 *
 * Handles client appointment booking, calendar slot availability via AJAX,
 * server-side price recalculation, appointment confirmations, and
 * PayHere payment gateway integration.
 *
 * Related views:
 *   views/booking/booking.php
 *   views/booking/booking_success.php
 * Related models:
 *   models/Appointment.php
 *   models/Service.php
 *   models/Customer.php
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Customer.php';

class BookingController
{
    private ?PDO $pdo = null;
    private ?Appointment $appointmentModel = null;
    private ?Service $serviceModel = null;
    private ?Customer $customerModel = null;
    private array $payhereConfig = [];

    public function __construct(?PDO $pdo = null)
    {
        global $pdo;
        $this->pdo = $pdo ?? ($GLOBALS['pdo'] ?? null);
        if ($this->pdo !== null) {
            $this->appointmentModel = new Appointment($this->pdo);
            $this->serviceModel     = new Service($this->pdo);
            $this->customerModel    = new Customer($this->pdo);
        }
        $configFile = __DIR__ . '/../config/payhere.php';
        if (file_exists($configFile)) {
            $this->payhereConfig = require $configFile;
        }
    }

    /**
     * Primary action for booking page + AJAX slots + form submission
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. AJAX Endpoint for slot availability
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'slots') {
            header('Content-Type: application/json');
            $date = $_GET['date'] ?? date('Y-m-d');
            $slots = [];
            if ($this->appointmentModel !== null) {
                $slots = $this->appointmentModel->getSlotsForDate($date);
            } else {
                foreach (Appointment::ALL_SLOTS as $slot) {
                    $slots[] = [
                        'time'      => $slot,
                        'label'     => date('g:i A', strtotime($slot)),
                        'available' => true
                    ];
                }
            }
            echo json_encode($slots);
            exit;
        }

        // 2. Resolve Customer Info for pre-filling
        $isLoggedIn = isset($_SESSION['customer_id']) || isset($_SESSION['user_id']);
        $customerId = $isLoggedIn ? (int)($_SESSION['customer_id'] ?? $_SESSION['user_id']) : 0;
        $customerUser = null;

        if ($customerId > 0 && $this->customerModel !== null) {
            $customerUser = $this->customerModel->findById($customerId);
        }

        // 3. Load services grouped for booking
        $categories = [];
        if ($this->serviceModel !== null) {
            $categories = $this->serviceModel->getBookingCategories();
        }

        // Fallback demo categories if DB has no booking services
        if (empty($categories)) {
            $categories = [
                'MANICURES' => [
                    'name'     => 'MANICURES',
                    'services' => [
                        [
                            'service_id'       => 1,
                            'service_name'     => 'Signature Gel Manicure',
                            'description'      => 'Detailed cuticle care, shaping, and premium gel polish application.',
                            'price'            => 3500.00,
                            'duration_minutes' => 60,
                            'is_addon'         => 0
                        ],
                        [
                            'service_id'       => 2,
                            'service_name'     => 'Classic Polish Manicure',
                            'description'      => 'Essential care with traditional polish finish.',
                            'price'            => 4650.00,
                            'duration_minutes' => 45,
                            'is_addon'         => 0
                        ]
                    ]
                ],
                'ENHANCEMENTS & ART' => [
                    'name'     => 'ENHANCEMENTS & ART',
                    'services' => [
                        [
                            'service_id'       => 10,
                            'service_name'     => 'Impressionist Art Tier 1',
                            'description'      => 'Minimalist line art, negative space, or simple abstract designs on 2-4 nails.',
                            'price'            => 250.00,
                            'duration_minutes' => 15,
                            'is_addon'         => 1
                        ]
                    ]
                ]
            ];
        }

        // 4. Default form state (pre-filled with customer data if available)
        $old = [
            'service_id'       => $_POST['service_id'] ?? '',
            'addons'           => isset($_POST['addons']) && is_array($_POST['addons']) ? $_POST['addons'] : [],
            'date'             => $_POST['date'] ?? '',
            'time'             => $_POST['time'] ?? '',
            'first_name'       => trim($_POST['first_name'] ?? ($customerUser['f_name'] ?? '')),
            'last_name'        => trim($_POST['last_name'] ?? ($customerUser['l_name'] ?? '')),
            'email'            => trim($_POST['email'] ?? ($customerUser['email'] ?? '')),
            'phone'            => trim($_POST['phone'] ?? ($customerUser['phone_no'] ?? '')),
            'special_requests' => trim($_POST['special_requests'] ?? '')
        ];

        $success_msg = '';
        $error_msg   = '';
        $payhereData = null;

        // 5. Handle Form Submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // LOGIN GUARD: Require login to confirm appointment (like checkout)
            if (!$isLoggedIn) {
                $_SESSION['redirect_after_login'] = 'booking';
                header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&redirect=booking');
                exit;
            }

            $serviceId = (int)$old['service_id'];
            $addonIds  = array_map('intval', array_filter($old['addons'], 'is_numeric'));
            $date      = $old['date'];
            $time      = $old['time'];
            $firstName = $old['first_name'];
            $lastName  = $old['last_name'];
            $email     = $old['email'];
            $phone     = $old['phone'];
            $requests  = $old['special_requests'];

            // Validation
            if ($serviceId <= 0) {
                $error_msg = 'Please select a service first.';
            } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $error_msg = 'Please choose a date.';
            } elseif (!in_array($time, Appointment::ALL_SLOTS, true)) {
                $error_msg = 'Please choose an available appointment time.';
            } elseif ($firstName === '' || $lastName === '') {
                $error_msg = 'Please enter your first and last name.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_msg = 'Please enter a valid email address.';
            } elseif ($phone === '') {
                $error_msg = 'Please enter your contact phone number.';
            } else {
                // Check if slot is taken
                if ($this->appointmentModel !== null && $this->appointmentModel->isSlotTaken($date, $time)) {
                    $error_msg = 'Sorry, that time was just reserved. Please pick another available time slot.';
                } else {
                    // Calculate server-side total
                    $summary = ['total' => 0.0];
                    if ($this->serviceModel !== null) {
                        $summary = $this->serviceModel->calculateBookingSummary($serviceId, $addonIds);
                    }

                    // Save appointment
                    try {
                        if ($this->appointmentModel !== null) {
                            $newId = $this->appointmentModel->createAppointment([
                                'service_id'       => $serviceId,
                                'customer_id'      => $customerId > 0 ? $customerId : null,
                                'addon_ids'        => !empty($addonIds) ? implode(',', $addonIds) : null,
                                'appointment_date' => $date,
                                'appointment_time' => $time,
                                'first_name'       => $firstName,
                                'last_name'        => $lastName,
                                'email'            => $email,
                                'phone'            => $phone,
                                'special_requests' => $requests,
                                'total_price'      => $summary['total']
                            ]);

                            // Reset form fields while preserving customer contact details
                            $old['service_id']       = '';
                            $old['addons']           = [];
                            $old['date']             = '';
                            $old['time']             = '';
                            $old['special_requests'] = '';

                            // Build PayHere payment data
                            if (!empty($this->payhereConfig) && $summary['total'] > 0) {
                                $merchantId     = $this->payhereConfig['merchant_id'];
                                $merchantSecret = $this->payhereConfig['merchant_secret'];
                                $currency       = $this->payhereConfig['currency'];
                                $formattedTotal = number_format((float)$summary['total'], 2, '.', '');

                                // PayHere hash: MD5(merchant_id + order_id + amount + currency + MD5(secret))
                                $hash = strtoupper(md5(
                                    $merchantId . $newId . $formattedTotal . $currency .
                                    strtoupper(md5($merchantSecret))
                                ));

                                $returnUrl = 'http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=booking_success&appointment_id=' . $newId;
                                $cancelUrl = 'http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=booking';
                                $notifyUrl = 'http://localhost/MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=booking_notify';

                                $payhereData = [
                                    'sandbox'     => $this->payhereConfig['is_sandbox'],
                                    'merchant_id' => $merchantId,
                                    'return_url'  => $returnUrl,
                                    'cancel_url'  => $cancelUrl,
                                    'notify_url'  => $notifyUrl,
                                    'order_id'    => (string)$newId,
                                    'items'       => 'Monet Nails Appointment #' . $newId,
                                    'currency'    => $currency,
                                    'amount'      => $formattedTotal,
                                    'first_name'  => $firstName,
                                    'last_name'   => $lastName,
                                    'email'       => $email,
                                    'phone'       => $phone,
                                    'address'     => 'N/A',
                                    'city'        => 'Yakkala',
                                    'country'     => 'Sri Lanka',
                                    'hash'        => $hash,
                                ];
                            } else {
                                // If total is 0 or PayHere not configured, redirect directly to success
                                header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=booking_success&appointment_id=' . $newId);
                                exit;
                            }
                        }
                    } catch (Exception $e) {
                        $error_msg = 'An unexpected error occurred while saving your appointment. Please try again.';
                    }
                }
            }
        }

        // Navigation state for common header
        $activeNav = 'booking';

        // Render view
        require __DIR__ . '/../views/booking/booking.php';
    }

    /**
     * Appointment booking success page (shown after PayHere return)
     */
    public function success(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $appointmentId = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;
        $appointment   = null;

        if ($appointmentId > 0 && $this->appointmentModel !== null) {
            $appointment = $this->appointmentModel->getById($appointmentId);
        }

        $isLoggedIn = isset($_SESSION['customer_id']) || isset($_SESSION['user_id']);
        $activeNav  = 'booking';

        require __DIR__ . '/../views/booking/booking_success.php';
    }

    /**
     * PayHere IPN callback for appointment payments
     */
    public function notify(): void
    {
        $merchantId      = $_POST['merchant_id'] ?? '';
        $appointmentId   = (int)($_POST['order_id'] ?? 0);
        $payhereAmount   = $_POST['payhere_amount'] ?? '';
        $payhereCurrency = $_POST['payhere_currency'] ?? '';
        $statusCode      = $_POST['status_code'] ?? '';
        $md5sig          = $_POST['md5sig'] ?? '';
        $paymentId       = $_POST['payment_id'] ?? null;

        $merchantSecret = $this->payhereConfig['merchant_secret'] ?? '';

        // Verify hash
        $localMd5sig = strtoupper(md5(
            $merchantId . $appointmentId . $payhereAmount . $payhereCurrency .
            $statusCode . strtoupper(md5($merchantSecret))
        ));

        if ($localMd5sig === $md5sig) {
            if ($statusCode == '2') {
                // SUCCESS
                if ($this->appointmentModel !== null) {
                    $this->appointmentModel->updatePaymentStatus($appointmentId, 'completed', $paymentId);
                }
                http_response_code(200);
                echo "OK";
            } elseif ($statusCode == '-2') {
                if ($this->appointmentModel !== null) {
                    $this->appointmentModel->updatePaymentStatus($appointmentId, 'failed', $paymentId);
                }
                http_response_code(200);
                echo "OK";
            } elseif ($statusCode == '-1') {
                if ($this->appointmentModel !== null) {
                    $this->appointmentModel->updatePaymentStatus($appointmentId, 'cancelled', $paymentId);
                }
                http_response_code(200);
                echo "OK";
            } else {
                http_response_code(400);
                echo "UNHANDLED";
            }
        } else {
            http_response_code(400);
            echo "INVALID_SIGNATURE";
        }
        exit;
    }
}
