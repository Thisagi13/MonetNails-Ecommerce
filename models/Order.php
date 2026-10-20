<?php
/**
 * Order.php — Model
 * Handles all database operations for orders, order_items, and payments.
 * MVC Layer: Model
 */

class Order
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Create a new order row.
     */
    public function createOrder(
        ?int $customerId,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $streetAddress,
        string $city,
        string $postalCode,
        float $subtotal,
        float $shippingFee,
        float $totalAmount,
        string $paymentMethod = 'PayHere',
        string $status = 'Pending'
    ): int|false {
        $sql = "INSERT INTO orders (
                    customer_id, first_name, last_name, email, phone,
                    street_address, city, postal_code,
                    subtotal, shipping_fee, total_amount, payment_method, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            $customerId,
            $firstName,
            $lastName,
            $email,
            $phone,
            $streetAddress,
            $city,
            $postalCode,
            $subtotal,
            $shippingFee,
            $totalAmount,
            $paymentMethod,
            $status
        ]);

        return $ok ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * Insert a line item into order_items.
     */
    public function addOrderItem(
        int $orderId,
        int $productId,
        int $quantity,
        float $unitPrice,
        ?string $shape = null,
        ?string $finish = null,
        ?string $size = null
    ): bool {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, shape, finish, size_preset)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $orderId,
            $productId,
            $quantity,
            $unitPrice,
            $shape,
            $finish,
            $size
        ]);
    }

    /**
     * Create payment record.
     */
    public function addPayment(
        int $orderId,
        float $amount,
        string $paymentMethod = 'PayHere',
        ?string $transactionId = null,
        string $status = 'Pending'
    ): int|false {
        $sql = "INSERT INTO payments (order_id, amount, payment_method, transaction_id, status)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([$orderId, $amount, $paymentMethod, $transactionId, $status]);
        return $ok ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * Get all orders for a customer.
     */
    public function getOrdersByCustomer(int $customerId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update payment status and transaction ID.
     */
    public function updatePaymentStatus(int $orderId, string $status, ?string $transactionId = null): bool
    {
        if ($transactionId !== null) {
            $stmt = $this->db->prepare("UPDATE payments SET status = ?, transaction_id = ? WHERE order_id = ?");
            return $stmt->execute([$status, $transactionId, $orderId]);
        }
        $stmt = $this->db->prepare("UPDATE payments SET status = ? WHERE order_id = ?");
        return $stmt->execute([$status, $orderId]);
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(int $orderId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        return $stmt->execute([$status, $orderId]);
    }

    /**
     * Get order details by order_id.
     */
    public function getOrderById(int $orderId): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_id = ? LIMIT 1");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get order items with product details.
     */
    public function getOrderItems(int $orderId): array
    {
        $sql = "SELECT oi.*, p.product_name, COALESCE(p.image_url, p.image_path, '') AS image_url
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                WHERE oi.order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
