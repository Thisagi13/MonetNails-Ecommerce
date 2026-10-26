<?php
/**
 * OrderHistoryController.php — Customer Order History Controller
 * Monet Nails MVC Application
 *
 * Handles:
 *   - Displaying a logged-in customer's full order history
 *   - Filter tabs by status (All / Pending / Processing / Shipped / Delivered / Cancelled)
 *   - Loads order items (products) and payment info per order
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Customer.php';

class OrderHistoryController
{
    private PDO $pdo;
    private Order $orderModel;
    private Customer $customerModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo           = $pdo;
        $this->orderModel    = new Order($pdo);
        $this->customerModel = new Customer($pdo);
    }

    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';

        // 1. Auth guard
        $customerId = (int)($_SESSION['customer_id'] ?? $_SESSION['user_id'] ?? 0);
        if ($customerId <= 0) {
            $_SESSION['redirect_after_login'] = 'order_history';
            header('Location: ' . $base_url . '/index.php?page=signin');
            exit;
        }

        // 2. Fetch customer from DB
        $customer = $this->customerModel->findById($customerId);
        if (!$customer) {
            session_destroy();
            header('Location: ' . $base_url . '/index.php?page=signin');
            exit;
        }

        // 3. Status filter from GET param
        $allowedFilters = ['all', 'pending', 'processing', 'shipped', 'delivered', 'cancelled', 'accepted'];
        $statusFilter   = strtolower(trim($_GET['status'] ?? 'all'));
        if (!in_array($statusFilter, $allowedFilters, true)) {
            $statusFilter = 'all';
        }

        // 4. Fetch orders with items and payment
        $orders = $this->orderModel->getOrderHistoryForCustomer($customerId, $statusFilter);

        // 5. Fetch status counts for filter tabs
        $statusCounts = $this->orderModel->getOrderStatusCountsForCustomer($customerId);

        // 6. Format each order for display
        $formattedOrders = [];
        foreach ($orders as $order) {
            // Friendly date
            $date = !empty($order['order_date'])
                ? date('M j, Y', strtotime($order['order_date']))
                : '—';

            // Status pill colour
            $statusLabel = ucfirst($order['status'] ?? 'Pending');
            $statusStyle = self::statusStyle($order['status'] ?? '');

            // Payment summary line
            $payMethod  = $order['payment_method'] ?? 'PayHere';
            $payStatus  = ucfirst($order['payment_status'] ?? 'Pending');
            $payLine    = "Paid & Settled via {$payMethod}";
            if (strtolower($payStatus) === 'pending') {
                $payLine = "Payment {$payStatus} via {$payMethod}";
            } elseif (strtolower($payStatus) === 'failed') {
                $payLine = "Payment Failed via {$payMethod}";
            }

            // Format items for view
            $formattedItems = [];
            foreach ($order['items'] as $item) {
                // variant desc: use any available fields, fallback to generic label
                $variantParts = array_filter([
                    $item['shape']       ?? '',
                    $item['finish']      ?? '',
                    $item['size_preset'] ?? '',
                ]);

                $variantDesc = !empty($variantParts)
                    ? implode(' · ', $variantParts)
                    : 'Custom Press-On Set';

                // Resolve image URL
                $rawImage = $item['image_url'] ?? '';
                if (!empty($rawImage)) {
                    if (str_starts_with($rawImage, 'http') || str_starts_with($rawImage, '/')) {
                        $imageUrl = $rawImage;
                    } else {
                        $imageUrl = $base_url . '/' . ltrim($rawImage, '/');
                    }
                } else {
                    $imageUrl = ''; // will show placeholder in view
                }

                $formattedItems[] = [
                    'product_id'   => (int)$item['product_id'],
                    'product_name' => $item['product_name'] ?? 'Nail Product',
                    'image_url'    => $imageUrl,
                    'variant_desc' => $variantDesc,
                    'unit_price'   => (float)($item['unit_price'] ?? 0),
                    'quantity'     => (int)($item['quantity'] ?? 1),
                    'subtotal'     => (float)($item['subtotal'] ?? 0),
                ];
            }

            $formattedOrders[] = [
                'order_id'        => (int)$order['order_id'],
                'reference_no'    => $order['reference_no'],
                'status'          => $order['status'],
                'status_label'    => $statusLabel,
                'status_style'    => $statusStyle,
                'placed_on'       => 'Placed on ' . $date,
                'total_amount'    => (float)($order['total_amount'] ?? 0),
                'shipping_address'=> $order['shipping_address'] ?? '—',
                'payment_summary' => $payLine,
                'payment_status'  => $payStatus,
                'items'           => $formattedItems,
            ];
        }

        // 7. Build customer display info
        $customerFullName = trim(($customer['f_name'] ?? '') . ' ' . ($customer['l_name'] ?? ''));
        if ($customerFullName === '') {
            $customerFullName = $customer['email'] ?? 'Client';
        }
        $initials = strtoupper(
            substr($customer['f_name'] ?: 'M', 0, 1) .
            substr($customer['l_name'] ?: '',  0, 1)
        );

        // Avatar
        $avatarDiskPath = __DIR__ . '/../' . ltrim($customer['profile_image'] ?? '', '/');
        $hasAvatar      = !empty($customer['profile_image']) && file_exists($avatarDiskPath);
        $customerAvatarUrl = $hasAvatar
            ? ($base_url . '/' . ltrim($customer['profile_image'], '/'))
            : '';

        // 8. Status tabs definition
        $statusTabs = [
            ['key' => 'all',        'label' => 'ALL ORDERS',  'count' => $statusCounts['all']],
            ['key' => 'pending',    'label' => 'PENDING',     'count' => $statusCounts['pending']],
            ['key' => 'processing', 'label' => 'PROCESSING',  'count' => $statusCounts['processing']],
            ['key' => 'shipped',    'label' => 'SHIPPED',     'count' => $statusCounts['shipped']],
            ['key' => 'delivered',  'label' => 'DELIVERED',   'count' => $statusCounts['delivered']],
            ['key' => 'cancelled',  'label' => 'CANCELLED',   'count' => $statusCounts['cancelled']],
        ];
        // Remove zero-count tabs except 'all'
        $statusTabs = array_filter($statusTabs, fn($t) => $t['key'] === 'all' || $t['count'] > 0);

        $activeNav = 'order_history';

        // 9. Load view
        require __DIR__ . '/../views/orders/history.php';
    }

    /**
     * Returns a CSS class suffix for the status pill colour.
     */
    private static function statusStyle(string $status): string
    {
        return match (strtolower(trim($status))) {
            'delivered'           => 'delivered',
            'shipped'             => 'shipped',
            'processing'          => 'processing',
            'accepted'            => 'accepted',
            'cancelled', 'canceled' => 'cancelled',
            default               => 'pending',
        };
    }
}
