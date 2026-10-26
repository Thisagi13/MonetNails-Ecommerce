<?php
/**
 * AppointmentHistoryController.php — Customer Appointment History Controller
 * Monet Nails MVC Application
 *
 * Handles:
 *   - Displaying upcoming nail care appointments and past salon visits
 *   - Syncing customer profile statistics
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Customer.php';

class AppointmentHistoryController
{
    private PDO $pdo;
    private Appointment $appointmentModel;
    private Customer $customerModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->appointmentModel = new Appointment($pdo);
        $this->customerModel = new Customer($pdo);
    }

    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';

        // 1. Guard check: user must be logged in
        $customerId = (int)($_SESSION['customer_id'] ?? $_SESSION['user_id'] ?? 0);
        if ($customerId <= 0) {
            $_SESSION['redirect_after_login'] = 'my_appointments';
            header('Location: ' . $base_url . '/index.php?page=signin');
            exit;
        }

        // 2. Fetch customer dossier from DB
        $customer = $this->customerModel->findById($customerId);
        if (!$customer) {
            session_destroy();
            header('Location: ' . $base_url . '/index.php?page=signin');
            exit;
        }

        // 3. Customer name & initials
        $fName = $customer['f_name'] ?? '';
        $lName = $customer['l_name'] ?? '';
        $customerFullName = trim($fName . ' ' . $lName);
        if ($customerFullName === '') {
            $customerFullName = $customer['email'] ?? 'Client';
        }

        $initials = strtoupper(substr($fName ?: ($customerFullName ?: 'M'), 0, 1) . substr($lName ?: '', 0, 1));
        if (empty($initials)) {
            $initials = 'M';
        }

        // Customer avatar image if uploaded
        $avatarDiskPath = __DIR__ . '/../' . ltrim($customer['profile_image'] ?? '', '/');
        $hasCustomAvatar = !empty($customer['profile_image']) && file_exists($avatarDiskPath);
        $customerAvatarUrl = $hasCustomAvatar ? ($base_url . '/' . ltrim($customer['profile_image'], '/')) : '';

        // 4. Fetch upcoming and past appointments
        $rawUpcoming = $this->appointmentModel->getUpcomingForCustomer($customerId);
        $rawPast     = $this->appointmentModel->getPastForCustomer($customerId);

        // Format upcoming appointments
        $upcomingAppointments = [];
        foreach ($rawUpcoming as $apt) {
            $dateTimestamp = strtotime($apt['appointment_date']);
            $dateText = date('l, M j, Y', $dateTimestamp);

            $timeStart = !empty($apt['appointment_time']) ? date('g:i A', strtotime($apt['appointment_time'])) : '10:00 AM';
            $durationMinutes = (int)($apt['service_duration'] ?? 45);
            $timeEnd = !empty($apt['appointment_time']) ? date('g:i A', strtotime($apt['appointment_time'] . " +{$durationMinutes} minutes")) : '10:45 AM';
            $timeText = "{$timeStart} – {$timeEnd}";

            // Payment display
            $amount = (float)($apt['amount'] ?? $apt['total_price'] ?? 0);
            $rawPaymentStatus = strtolower(trim($apt['payment_status'] ?? 'unsettled'));
            $paymentStatusLabel = ($rawPaymentStatus === 'completed' || $rawPaymentStatus === 'settled') ? 'Settled' : ucfirst($rawPaymentStatus);

            $upcomingAppointments[] = [
                'appointment_id' => (int)$apt['appointment_id'],
                'reference_no'   => $apt['reference_no'] ?? ('#MN-APT-' . str_pad((string)$apt['appointment_id'], 5, '0', STR_PAD_LEFT)),
                'service_name'   => $apt['service_name'] ?? 'Bespoke Nail Session',
                'description'    => !empty($apt['styling_notes']) ? $apt['styling_notes'] : (!empty($apt['service_description']) ? $apt['service_description'] : 'Full cuticle ritual, organic infusion, and bespoke artisan styling consultation.'),
                'amount'         => $amount,
                'payment_status' => $paymentStatusLabel,
                'date_text'      => $dateText,
                'time_text'      => $timeText,
                'status'         => ucfirst($apt['status'] ?? 'Confirmed'),
            ];
        }

        // Format past appointments
        $pastAppointments = [];
        foreach ($rawPast as $apt) {
            $dateTimestamp = strtotime($apt['appointment_date']);
            $monthAbbr = strtoupper(date('M', $dateTimestamp));
            $day = date('d', $dateTimestamp);

            $amount = (float)($apt['amount'] ?? $apt['total_price'] ?? 0);
            $durationMinutes = (int)($apt['service_duration'] ?? 45);

            $stylistName = trim(($apt['stylist_first_name'] ?? '') . ' ' . ($apt['stylist_last_name'] ?? ''));
            if ($stylistName === '') {
                $stylistName = 'Sarah Chen'; // Atelier Senior Specialist fallback
            }

            $pastAppointments[] = [
                'appointment_id' => (int)$apt['appointment_id'],
                'reference_no'   => $apt['reference_no'] ?? ('#MN-APT-' . str_pad((string)$apt['appointment_id'], 5, '0', STR_PAD_LEFT)),
                'month_abbr'     => $monthAbbr,
                'day'            => $day,
                'service_name'   => $apt['service_name'] ?? 'Artisan Manicure Experience',
                'specialist'     => $stylistName,
                'duration_min'   => $durationMinutes,
                'fee'            => $amount,
                'status'         => ucfirst($apt['status'] ?? 'Completed'),
            ];
        }

        $upcomingCount = count($upcomingAppointments);
        $pastCount     = count($pastAppointments);

        $activeNav = 'my_appointments';

        // Load MVC View
        require __DIR__ . '/../views/appointments/history.php';
    }
}
