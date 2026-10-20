<?php
/**
 * CheckoutController.php
 * Handles the checkout process, shipping information, order placement,
 * and integration with PayHere Payment Gateway.
 *
 * MVC Layer: Controller
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Order.php';

class CheckoutController
{
    private PDO $pdo;
    private Cart $cartModel;
    private Customer $customerModel;
    private Order $orderModel;
    private array $payhereConfig;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->cartModel     = new Cart($pdo);
        $this->customerModel = new Customer($pdo);
        $this->orderModel    = new Order($pdo);
        $this->payhereConfig = require __DIR__ . '/../config/payhere.php';
    }

    /**
     * Main checkout page (GET: display form / POST: place order)
     */
    public function index(): void
    {
        $userId     = $_SESSION['customer_id'] ?? $_SESSION['user_id'] ?? null;
        $sessionKey = ($userId === null) ? session_id() : '';
        $isLoggedIn = ($userId !== null);

        // 1. Handle "Buy Now" direct navigation with product ID
        $buyNowProductId = isset($_GET['buy_now_product_id']) ? (int)$_GET['buy_now_product_id'] : 0;
        if ($buyNowProductId > 0) {
            $this->cartModel->addItem($userId, $sessionKey, $buyNowProductId, 1);
            // Clean URL after adding
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=checkout');
            exit;
        }

        // 2. Load Cart Items
        $cartItems = $this->cartModel->getItems($userId, $sessionKey);

        // If cart is empty and user is not logged in, provide sample preview or fallback
        $cart = [];
        $isDemo = false;
        if (empty($cartItems)) {
            if (!$isLoggedIn) {
                // Show demo sets as seen in CheckOut.php design
                $isDemo = true;
                $cart = [
                    [
                        "product_id" => 1,
                        "name"       => "Cherry Blossom Artisan Set",
                        "variant"    => "Almond · Glossy Gel · Preset S",
                        "price"      => 1450.00,
                        "quantity"   => 1,
                        "image_path" => "/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/checkout/product-image-64.png"
                    ],
                    [
                        "product_id" => 2,
                        "name"       => "Azure Velvet Hearts Press-On Set",
                        "variant"    => "Coffin · Velvet Matte · Preset M",
                        "price"      => 950.00,
                        "quantity"   => 1,
                        "image_path" => "/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/checkout/product-image-70.png"
                    ],
                    [
                        "product_id" => 3,
                        "name"       => "Soft Blush Ribbon & 3D Pearl Set",
                        "variant"    => "Soft Oval · Custom Sizing",
                        "price"      => 450.00,
                        "quantity"   => 1,
                        "image_path" => "/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/checkout/product-image-76.png"
                    ],
                ];
            }
        } else {
            foreach ($cartItems as $item) {
                $variantParts = array_filter([
                    $item['shape'] ?? '',
                    $item['finish'] ?? '',
                    $item['size_preset'] ?? ''
                ]);
                $variant = !empty($variantParts) ? implode(' · ', $variantParts) : '';

                // Handle product image path
                $img = !empty($item['image_url']) ? $item['image_url'] : '/MonetNails-Ecommerce2/MonetNails-Ecommerce/assets/images/checkout/product-image-64.png';
                if (!str_starts_with($img, '/') && !str_starts_with($img, 'http')) {
                    $img = '/MonetNails-Ecommerce2/MonetNails-Ecommerce/' . $img;
                }

                $cart[] = [
                    'product_id' => (int)$item['product_id'],
                    'name'       => $item['product_name'],
                    'variant'    => $variant,
                    'price'      => (float)$item['price'],
                    'quantity'   => (int)$item['quantity'],
                    'image_path' => $img
                ];
            }
        }

        // Calculate Totals
        $shippingFee = 150.00;
        $subtotal    = 0.0;
        $itemCount   = 0;
        foreach ($cart as $it) {
            $q = max(1, (int)$it['quantity']);
            $itemCount += $q;
            $subtotal  += (float)$it['price'] * $q;
        }
        $total = $subtotal > 0 ? ($subtotal + $shippingFee) : 0.0;

        // 3. Prefill Shipping Details
        $ship = [
            'first_name'     => '',
            'last_name'      => '',
            'email'          => '',
            'phone'          => '',
            'street_address' => '',
            'city'           => '',
            'postal_code'    => '',
            'payment_method' => 'payhere'
        ];

        $customer_name   = 'Client';
        $customer_email  = '';
        $customer_avatar = '';
        $avatar_html     = '';
        $headerCartCount = $itemCount;

        if ($isLoggedIn) {
            $customer = $this->customerModel->findById((int)$userId);
            if ($customer) {
                $customer_name   = trim(($customer['f_name'] ?? '') . ' ' . ($customer['l_name'] ?? ''));
                $customer_email  = $customer['email'] ?? '';
                $customer_avatar = $customer['profile_image'] ?? '';
                $ship['first_name']     = $customer['f_name'] ?? '';
                $ship['last_name']      = $customer['l_name'] ?? '';
                $ship['email']          = $customer['email'] ?? '';
                // Get additional customer details
                $stmt = $this->pdo->prepare("SELECT phone_no, address, city, postal_code FROM Customer WHERE customer_id = ? LIMIT 1");
                $stmt->execute([(int)$userId]);
                $fullCust = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($fullCust) {
                    $ship['phone']          = $fullCust['phone_no'] ?? '';
                    $ship['street_address'] = $fullCust['address'] ?? '';
                    $ship['city']           = $fullCust['city'] ?? '';
                    $ship['postal_code']    = $fullCust['postal_code'] ?? '';
                }
            }

            if (!empty($customer_avatar) && file_exists(__DIR__ . '/../' . ltrim($customer_avatar, '/'))) {
                $avatar_html = '<img src="' . htmlspecialchars($customer_avatar) . '" alt="avatar">';
            } else {
                $initials = strtoupper(substr($customer_name ?: 'M', 0, 1));
                $avatar_html = '<span class="avatar-initials">' . htmlspecialchars($initials ?: 'M') . '</span>';
            }
        }

        $errors = [];
        $payhereData = null;

        // 4. Handle POST Order Placement
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Requirement: If unsigned user tries to proceed checkout, ask to sign in first!
            if (!$isLoggedIn) {
                $_SESSION['redirect_after_login'] = 'checkout';
                header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&redirect=checkout');
                exit;
            }

            // Read form inputs
            $ship['first_name']     = trim($_POST['first_name'] ?? '');
            $ship['last_name']      = trim($_POST['last_name'] ?? '');
            $ship['email']          = trim($_POST['email'] ?? '');
            $ship['phone']          = trim($_POST['phone'] ?? '');
            $ship['street_address'] = trim($_POST['street_address'] ?? '');
            $ship['city']           = trim($_POST['city'] ?? '');
            $ship['postal_code']    = trim($_POST['postal_code'] ?? '');
            $ship['payment_method'] = trim($_POST['payment_method'] ?? 'payhere');

            // Validation
            if ($ship['first_name'] === '')     { $errors[] = 'First name is required.'; }
            if ($ship['last_name'] === '')      { $errors[] = 'Last name is required.'; }
            if ($ship['email'] === '')          { $errors[] = 'Email address is required.'; }
            elseif (!filter_var($ship['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            }
            if ($ship['phone'] === '')          { $errors[] = 'Phone number is required.'; }
            if ($ship['street_address'] === '') { $errors[] = 'Street address is required.'; }
            if ($ship['city'] === '')           { $errors[] = 'City is required.'; }
            if ($ship['postal_code'] === '')    { $errors[] = 'Postal code is required.'; }

            if (empty($cart) || $isDemo) {
                $errors[] = 'Your cart is empty. Please add products before placing an order.';
            }

            if (empty($errors)) {
                // Determine payment method label
                $paymentMethodLabel = ($ship['payment_method'] === 'cod') ? 'Cash on Delivery' : 'PayHere';

                // Create Order
                $orderId = $this->orderModel->createOrder(
                    (int)$userId,
                    $ship['first_name'],
                    $ship['last_name'],
                    $ship['email'],
                    $ship['phone'],
                    $ship['street_address'],
                    $ship['city'],
                    $ship['postal_code'],
                    $subtotal,
                    $shippingFee,
                    $total,
                    $paymentMethodLabel,
                    'Pending'
                );

                if ($orderId !== false) {
                    // Save items
                    foreach ($cart as $item) {
                        $this->orderModel->addOrderItem(
                            $orderId,
                            (int)$item['product_id'],
                            (int)$item['quantity'],
                            (float)$item['price']
                        );
                    }

                    // Save initial payment record
                    $this->orderModel->addPayment(
                        $orderId,
                        $total,
                        $paymentMethodLabel,
                        null,
                        'Pending'
                    );

                    // Clear Cart
                    $this->cartModel->clearCart((int)$userId, $sessionKey);

                    // Payment Flow:
                    if ($ship['payment_method'] === 'cod') {
                        // Cash on Delivery -> directly to success
                        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=order_success&order_id=' . $orderId);
                        exit;
                    } else {
                        // PayHere Gateway
                        $merchantId     = $this->payhereConfig['merchant_id'];
                        $merchantSecret = $this->payhereConfig['merchant_secret'];
                        $currency       = $this->payhereConfig['currency'];
                        $formattedTotal = number_format($total, 2, '.', '');

                        // PayHere Hash generation:
                        // strtoupper(md5(merchant_id + order_id + amount + currency + strtoupper(md5(merchant_secret))))
                        $hash = strtoupper(md5($merchantId . $orderId . $formattedTotal . $currency . strtoupper(md5($merchantSecret))));

                        $returnUrl = $this->payhereConfig['return_url'] . '&order_id=' . $orderId;

                        $payhereData = [
                            'sandbox'       => $this->payhereConfig['is_sandbox'],
                            'merchant_id'   => $merchantId,
                            'return_url'    => $returnUrl,
                            'cancel_url'    => $this->payhereConfig['cancel_url'],
                            'notify_url'    => $this->payhereConfig['notify_url'],
                            'order_id'      => (string)$orderId,
                            'items'         => 'Monet Nails Order #' . $orderId,
                            'currency'      => $currency,
                            'amount'        => $formattedTotal,
                            'first_name'    => $ship['first_name'],
                            'last_name'     => $ship['last_name'],
                            'email'         => $ship['email'],
                            'phone'         => $ship['phone'],
                            'address'       => $ship['street_address'],
                            'city'          => $ship['city'],
                            'country'       => 'Sri Lanka',
                            'hash'          => $hash,
                        ];
                    }
                } else {
                    $errors[] = 'Failed to create order. Please try again.';
                }
            }
        }

        // Pass variables to view
        $payhereConfig = $this->payhereConfig;
        require __DIR__ . '/../views/checkout/checkout.php';
    }

    /**
     * Order success page
     */
    public function success(): void
    {
        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        $order = null;
        $orderItems = [];

        if ($orderId > 0) {
            $order = $this->orderModel->getOrderById($orderId);
            if ($order) {
                $orderItems = $this->orderModel->getOrderItems($orderId);
            }
        }

        $isLoggedIn = isset($_SESSION['customer_id']) || isset($_SESSION['user_id']);
        $userId     = $_SESSION['customer_id'] ?? $_SESSION['user_id'] ?? null;
        $customer_name   = 'Client';
        $customer_email  = '';
        $customer_avatar = '';
        $avatar_html     = '';
        $headerCartCount = 0;

        if ($isLoggedIn && $userId) {
            $customer = $this->customerModel->findById((int)$userId);
            if ($customer) {
                $customer_name   = trim(($customer['f_name'] ?? '') . ' ' . ($customer['l_name'] ?? ''));
                $customer_email  = $customer['email'] ?? '';
                $customer_avatar = $customer['profile_image'] ?? '';
            }
            if (!empty($customer_avatar) && file_exists(__DIR__ . '/../' . ltrim($customer_avatar, '/'))) {
                $avatar_html = '<img src="' . htmlspecialchars($customer_avatar) . '" alt="avatar">';
            } else {
                $initials = strtoupper(substr($customer_name ?: 'M', 0, 1));
                $avatar_html = '<span class="avatar-initials">' . htmlspecialchars($initials ?: 'M') . '</span>';
            }
        }

        require __DIR__ . '/../views/checkout/order_success.php';
    }

    /**
     * PayHere IPN notification callback
     */
    public function notify(): void
    {
        $merchantId     = $_POST['merchant_id'] ?? '';
        $orderId        = (int)($_POST['order_id'] ?? 0);
        $payhereAmount  = $_POST['payhere_amount'] ?? '';
        $payhereCurrency= $_POST['payhere_currency'] ?? '';
        $statusCode     = $_POST['status_code'] ?? '';
        $md5sig         = $_POST['md5sig'] ?? '';
        $paymentId      = $_POST['payment_id'] ?? '';

        $merchantSecret = $this->payhereConfig['merchant_secret'];

        // Local hash calculation
        $localMd5sig = strtoupper(md5($merchantId . $orderId . $payhereAmount . $payhereCurrency . $statusCode . strtoupper(md5($merchantSecret))));

        if ($localMd5sig === $md5sig && $statusCode == '2') {
            // Payment success: status_code 2 means SUCCESS
            $this->orderModel->updatePaymentStatus($orderId, 'Completed', $paymentId);
            $this->orderModel->updateOrderStatus($orderId, 'Processing');
            http_response_code(200);
            echo "OK";
        } else {
            // Payment failed or signature mismatch
            if ($statusCode == '-2') {
                $this->orderModel->updatePaymentStatus($orderId, 'Failed', $paymentId);
            } elseif ($statusCode == '-1') {
                $this->orderModel->updatePaymentStatus($orderId, 'Cancelled', $paymentId);
            }
            http_response_code(400);
            echo "FAILED";
        }
        exit;
    }
}
