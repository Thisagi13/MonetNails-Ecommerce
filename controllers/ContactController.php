<?php
/**
 * ContactController.php — Contact Us Controller
 * Monet Nails MVC Application
 *
 * Handles the Contact Us page display and form submission.
 * Pre-fills the form for logged-in customers and stores
 * messages in the contact_messages table.
 *
 * Related views:
 *   views/contact/contact.php
 * Related models:
 *   models/ContactMessage.php
 *   models/Customer.php
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ContactMessage.php';
require_once __DIR__ . '/../models/Customer.php';

class ContactController
{
    private ?PDO $pdo = null;
    private ?ContactMessage $contactModel = null;
    private ?Customer $customerModel = null;

    public function __construct(?PDO $pdo = null)
    {
        global $pdo;
        $this->pdo = $pdo ?? ($GLOBALS['pdo'] ?? null);
        if ($this->pdo !== null) {
            $this->contactModel  = new ContactMessage($this->pdo);
            $this->customerModel = new Customer($this->pdo);
        }
    }

    /**
     * Main contact page — displays form and handles POST submission.
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Detect login state and pre-fill customer data
        $isLoggedIn = isset($_SESSION['customer_id']) || isset($_SESSION['user_id']);
        $customerId = $isLoggedIn ? (int)($_SESSION['customer_id'] ?? $_SESSION['user_id']) : null;
        $customerUser = null;

        if ($customerId && $this->customerModel !== null) {
            $customerUser = $this->customerModel->findById($customerId);
        }

        // Default form field values (pre-filled for logged-in customers)
        $fields = [
            'name'    => trim($customerUser
                ? (($customerUser['f_name'] ?? '') . ' ' . ($customerUser['l_name'] ?? ''))
                : ''),
            'email'   => $customerUser['email'] ?? '',
            'subject' => '',
            'message' => '',
        ];

        $sent      = false;
        $error_msg = '';

        // Handle POST submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fields['name']    = trim($_POST['name']    ?? '');
            $fields['email']   = trim($_POST['email']   ?? '');
            $fields['subject'] = trim($_POST['subject'] ?? '');
            $fields['message'] = trim($_POST['message'] ?? '');

            // Server-side validation
            if ($fields['name'] === '' || $fields['email'] === '' || $fields['subject'] === '' || $fields['message'] === '') {
                $error_msg = 'Please fill in all the fields.';
            } elseif (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
                $error_msg = 'Please enter a valid email address.';
            } else {
                // Save to DB
                if ($this->contactModel !== null) {
                    $saved = $this->contactModel->save(
                        $fields['name'],
                        $fields['email'],
                        $fields['subject'],
                        $fields['message'],
                        $customerId
                    );
                    if ($saved) {
                        $sent = true;

                        // Send email notification to admin
                        try {
                            require_once __DIR__ . '/../services/MailService.php';
                            MailService::sendContactNotification($fields['name'], $fields['email'], $fields['subject'], $fields['message']);
                        } catch (\Throwable $mailEx) {
                            error_log("Failed to send contact email to admin: " . $mailEx->getMessage());
                        }

                        // Clear message / subject after success; keep name/email for convenience
                        $fields['subject'] = '';
                        $fields['message'] = '';
                    } else {
                        $error_msg = 'Sorry, your message could not be sent. Please try again.';
                    }
                } else {
                    $error_msg = 'Database connection unavailable. Please try again later.';
                }
            }
        }

        // Navigation state for common header
        $activeNav = 'contact';

        // Render view
        require __DIR__ . '/../views/contact/contact.php';
    }
}
