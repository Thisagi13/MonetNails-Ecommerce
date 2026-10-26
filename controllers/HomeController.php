<?php
/**
 * HomeController.php
 * ------------------
 * Handles routing for the homepage.
 * - If a customer session exists → prepare customer data and load the registered home view.
 * - Otherwise                   → load the unregistered (public) home view.
 *
 * MVC Layer : Controller
 * Related views:
 *   views/home/unregistered.php
 *   views/home/registered.php
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Customer.php';

class HomeController
{
    private ?PDO $pdo = null;
    private ?Customer $customerModel = null;

    public function __construct(?PDO $pdo = null)
    {
        global $pdo;
        $this->pdo = $pdo ?? ($GLOBALS['pdo'] ?? null);
        if ($this->pdo !== null) {
            $this->customerModel = new Customer($this->pdo);
        }
    }

    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['customer_id'])) {
            $cid             = (int) $_SESSION['customer_id'];
            $customer_name   = $_SESSION['customer_name'] ?? 'Client';
            $customer_email  = '';
            $customer_avatar = '';

            if ($this->customerModel !== null) {
                $customer = $this->customerModel->findById($cid);
                if ($customer) {
                    $customer_name   = trim($customer['f_name'] . ' ' . $customer['l_name']);
                    $customer_email  = $customer['email'] ?? '';
                    $customer_avatar = $customer['profile_image'] ?? '';
                }
            }

            // Build avatar HTML
            if (!empty($customer_avatar) && file_exists(__DIR__ . '/../' . $customer_avatar)) {
                $avatar_html = '<img src="' . htmlspecialchars($customer_avatar) . '" alt="avatar">';
            } else {
                $initials = strtoupper(substr($customer_name, 0, 1));
                if ($initials === '') {
                    $initials = 'M';
                }
                $avatar_html = '<span class="avatar-initials">' . htmlspecialchars($initials) . '</span>';
            }

            require __DIR__ . '/../views/home/registered.php';
        } else {
            require __DIR__ . '/../views/home/unregistered.php';
        }
    }
}
