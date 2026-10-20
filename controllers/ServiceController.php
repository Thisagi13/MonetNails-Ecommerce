<?php
/**
 * ServiceController.php — Controller
 * Monet Nails MVC Application
 *
 * Handles routing and display of customer atelier services
 * (Manicures, Pedicures, Specialty Enhancements).
 *
 * Related views:
 *   views/services/services.php
 * Related models:
 *   models/Service.php
 *   models/Customer.php
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Customer.php';

class ServiceController
{
    private ?PDO $pdo = null;
    private ?Service $serviceModel = null;
    private ?Customer $customerModel = null;

    public function __construct(?PDO $pdo = null)
    {
        global $pdo;
        $this->pdo = $pdo ?? ($GLOBALS['pdo'] ?? null);
        if ($this->pdo !== null) {
            $this->serviceModel  = new Service($this->pdo);
            $this->customerModel = new Customer($this->pdo);
        }
    }

    /**
     * Display public/customer atelier services
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Fetch services grouped by category
        $grouped = [];
        if ($this->serviceModel !== null) {
            $grouped = $this->serviceModel->getAllGroupedByCategory();
        }

        $manicures = $grouped['Manicure'] ?? [];
        $pedicures = $grouped['Pedicure'] ?? [];
        $specials  = $grouped['Specialty'] ?? [];

        // Check customer authentication for common header state
        $isLoggedIn = isset($_SESSION['customer_id']) || isset($_SESSION['user_id']);
        $customer_name   = '';
        $customer_email  = '';
        $customer_avatar = '';

        if ($isLoggedIn && $this->customerModel !== null) {
            $cid = (int)($_SESSION['customer_id'] ?? $_SESSION['user_id'] ?? 0);
            $customer = $this->customerModel->findById($cid);
            if ($customer) {
                $customer_name   = trim(($customer['f_name'] ?? '') . ' ' . ($customer['l_name'] ?? ''));
                $customer_email  = $customer['email'] ?? '';
                $customer_avatar = $customer['profile_image'] ?? '';
            }
        }

        // Active navigation indicator
        $activeNav = 'services';

        // Render the services view
        require __DIR__ . '/../views/services/services.php';
    }
}
