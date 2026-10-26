<?php
/**
 * GalleryController.php — Controller
 * ----------------------------------
 * Handles routing and data preparation for The Gallery & Shop.
 *
 * MVC Layer : Controller
 * Related views:
 *   views/gallery/unregistered.php
 *   views/gallery/registered.php
 * Related models:
 *   models/Category.php
 *   models/Product.php
 *   models/Customer.php
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Customer.php';

class GalleryController
{
    private ?PDO $pdo = null;
    private ?Category $categoryModel = null;
    private ?Product $productModel = null;
    private ?Customer $customerModel = null;

    public function __construct(?PDO $pdo = null)
    {
        global $pdo;
        $this->pdo = $pdo ?? ($GLOBALS['pdo'] ?? null);
        if ($this->pdo !== null) {
            $this->categoryModel = new Category($this->pdo);
            $this->productModel  = new Product($this->pdo);
            $this->customerModel = new Customer($this->pdo);
        }
    }

    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Read category filter choices from URL
        $selected_categories = [];
        if (isset($_GET['category']) && is_array($_GET['category'])) {
            foreach ($_GET['category'] as $value) {
                $val = (int) $value;
                if ($val > 0) {
                    $selected_categories[] = $val;
                }
            }
        }

        // 2. Load categories with product counts
        $categories = [];
        if ($this->categoryModel !== null) {
            $categories = $this->categoryModel->getAllWithProductCounts();
        }

        // 3. Load filtered products
        $products = [];
        if ($this->productModel !== null) {
            $products = $this->productModel->getFiltered($selected_categories);
        }

        // 4. Session check -> load registered vs unregistered view
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

            require __DIR__ . '/../views/gallery/registered.php';
        } else {
            require __DIR__ . '/../views/gallery/unregistered.php';
        }
    }
}
