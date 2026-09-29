<?php
/**
 * CartController.php
 * Handles Shopping Cart view, item removal, and quantity updates.
 */

require_once 'models/Cart.php';
require_once 'models/Customer.php';

class CartController
{
    private PDO $db;
    private Cart $cartModel;
    private Customer $customerModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->cartModel = new Cart($db);
        $this->customerModel = new Customer($db);
    }

    public function index()
    {
        $userId     = $_SESSION['customer_id'] ?? $_SESSION['user_id'] ?? null;
        $sessionKey = ($userId === null) ? session_id() : '';

        // Handle Add to cart (GET or POST)
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : (isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0);

        if (($action === 'add' || $action === 'add_to_cart') && $productId > 0) {
            $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : (isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1);
            $options = [
                'shape'              => $_POST['shape'] ?? $_GET['shape'] ?? null,
                'finish'             => $_POST['finish'] ?? $_GET['finish'] ?? null,
                'size_preset'        => $_POST['size_preset'] ?? $_GET['size_preset'] ?? null,
                'custom_sizing_code' => $_POST['custom_sizing_code'] ?? $_GET['custom_sizing_code'] ?? null,
                'special_features'   => $_POST['special_features'] ?? $_GET['special_features'] ?? null,
            ];
            $this->cartModel->addItem($userId, $sessionKey, $productId, $qty, $options);

            header('Location: index.php?page=cart');
            exit;
        }

        // Handle POST actions (remove / update qty)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartId = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;

            if ($action === 'remove' && $cartId > 0) {
                $this->cartModel->removeItem($cartId, $userId, $sessionKey);
            } elseif ($action === 'update_qty' && $cartId > 0) {
                $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
                $this->cartModel->updateQuantity($cartId, $qty, $userId, $sessionKey);
            }

            // Redirect to prevent form re-submission on refresh
            header('Location: index.php?page=cart');
            exit;
        }

        // Fetch cart items from DB
        $cartItems = $this->cartModel->getItems($userId, $sessionKey);

        // Calculate totals
        $shippingFee = 150.00;
        $subtotal    = 0.0;
        foreach ($cartItems as $item) {
            $subtotal += (float)$item['price'] * (int)$item['quantity'];
        }
        $totalDue = $subtotal + ($subtotal > 0 ? $shippingFee : 0);

        $data = [
            'cartItems'   => $cartItems,
            'subtotal'    => $subtotal,
            'shippingFee' => $shippingFee,
            'totalDue'    => $totalDue,
            'itemCount'   => count($cartItems),
        ];

        if (isset($_SESSION['customer_id']) || isset($_SESSION['user_id'])) {
            $cid = (int)($_SESSION['customer_id'] ?? $_SESSION['user_id']);
            $customer_name   = $_SESSION['customer_name'] ?? 'Client';
            $customer_email  = '';
            $customer_avatar = '';

            $customer = $this->customerModel->findById($cid);
            if ($customer) {
                $customer_name   = trim($customer['f_name'] . ' ' . $customer['l_name']);
                $customer_email  = $customer['email'] ?? '';
                $customer_avatar = $customer['profile_image'] ?? '';
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

            require 'views/cart/registered.php';
        } else {
            require 'views/cart/unregistered.php';
        }
    }
}
