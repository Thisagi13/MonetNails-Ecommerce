<?php
/**
 * CartController.php
 * Handles Shopping Cart view, item removal, and quantity updates.
 */

require_once 'models/Cart.php';

class CartController
{
    private PDO $db;
    private Cart $cartModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->cartModel = new Cart($db);
    }

    public function index()
    {
        $userId     = $_SESSION['user_id'] ?? null;
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

        if (isset($_SESSION['user_id'])) {
            require 'views/cart/registered.php';
        } else {
            require 'views/cart/unregistered.php';
        }
    }
}
