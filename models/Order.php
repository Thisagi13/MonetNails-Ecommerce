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
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
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

    /**
     * Get real-time status counts for admin dashboard cards.
     */
    public function getOrderStats(): array
    {
        $total = (int)$this->fetchScalar("SELECT COUNT(*) FROM orders");
        $pending = (int)$this->fetchScalar("SELECT COUNT(*) FROM orders WHERE LOWER(status) = 'pending'");
        $processing = (int)$this->fetchScalar("SELECT COUNT(*) FROM orders WHERE LOWER(status) = 'processing'");
        $shipped = (int)$this->fetchScalar("SELECT COUNT(*) FROM orders WHERE LOWER(status) IN ('shipped', 'delivered')");
        $delivered = (int)$this->fetchScalar("SELECT COUNT(*) FROM orders WHERE LOWER(status) = 'delivered'");
        $cancelled = (int)$this->fetchScalar("SELECT COUNT(*) FROM orders WHERE LOWER(status) IN ('cancelled', 'canceled')");

        return [
            'total'      => $total,
            'pending'    => $pending,
            'processing' => $processing,
            'shipped'    => $shipped,
            'delivered'  => $delivered,
            'cancelled'  => $cancelled,
        ];
    }

    /**
     * Fetch paginated orders with customer info, line items, and optional filters.
     */
    public function getAdminOrders(string $filter = 'all', string $search = '', int $page = 1, int $perPage = 10): array
    {
        $conditions = [];
        $params = [];

        // Filter by status
        $filterLower = strtolower(trim($filter));
        if ($filterLower === 'pending') {
            $conditions[] = "LOWER(o.status) = 'pending'";
        } elseif ($filterLower === 'processing') {
            $conditions[] = "LOWER(o.status) = 'processing'";
        } elseif ($filterLower === 'shipped') {
            $conditions[] = "LOWER(o.status) IN ('shipped', 'delivered')";
        } elseif ($filterLower === 'delivered') {
            $conditions[] = "LOWER(o.status) = 'delivered'";
        } elseif ($filterLower === 'cancelled') {
            $conditions[] = "LOWER(o.status) IN ('cancelled', 'canceled')";
        }

        // Search query
        if (!empty($search)) {
            $searchTerm = '%' . trim($search) . '%';
            $searchId = preg_replace('/[^0-9]/', '', $search);
            if (!empty($searchId) && is_numeric($searchId)) {
                $conditions[] = "(o.order_id = ? OR o.first_name LIKE ? OR o.last_name LIKE ? OR o.email LIKE ? OR o.phone LIKE ?)";
                $params[] = (int)$searchId;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            } else {
                $conditions[] = "(o.first_name LIKE ? OR o.last_name LIKE ? OR o.email LIKE ? OR o.phone LIKE ? OR o.tracking_number LIKE ?)";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
        }

        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        // Total count for pagination
        $countSql = "SELECT COUNT(*) FROM orders o $whereClause";
        $stmtCount = $this->db->prepare($countSql);
        $stmtCount->execute($params);
        $totalRows = (int)$stmtCount->fetchColumn();

        $totalPages = max(1, (int)ceil($totalRows / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        // Fetch orders
        $sql = "SELECT o.*,
                       COALESCE(NULLIF(TRIM(CONCAT(o.first_name, ' ', o.last_name)), ''), CONCAT(c.f_name, ' ', c.l_name), 'Guest Client') AS customer_name,
                       COALESCE(NULLIF(o.email, ''), c.email) AS customer_email,
                       COALESCE(NULLIF(o.phone, ''), c.phone_no) AS customer_phone,
                       COALESCE(NULLIF(TRIM(CONCAT_WS(', ', o.street_address, o.city, o.postal_code)), ''), c.address) AS customer_address
                FROM orders o
                LEFT JOIN customer c ON o.customer_id = c.customer_id
                $whereClause
                ORDER BY o.order_id DESC
                LIMIT $perPage OFFSET $offset";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch line items summary for each order in the page
        if (!empty($orders)) {
            $orderIds = array_column($orders, 'order_id');
            $inPlaceholders = implode(',', array_fill(0, count($orderIds), '?'));
            $itemSql = "SELECT oi.order_id, oi.quantity, oi.unit_price, oi.subtotal, oi.shape, oi.finish, oi.size_preset,
                               p.product_name, COALESCE(p.image_url, p.image_path, '') AS image_url
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.product_id
                        WHERE oi.order_id IN ($inPlaceholders)";
            $stmtItems = $this->db->prepare($itemSql);
            $stmtItems->execute($orderIds);
            $itemsMap = [];
            while ($row = $stmtItems->fetch(PDO::FETCH_ASSOC)) {
                $itemsMap[$row['order_id']][] = $row;
            }

            foreach ($orders as &$ord) {
                $ord['items'] = $itemsMap[$ord['order_id']] ?? [];
            }
            unset($ord);
        }

        return [
            'orders'      => $orders,
            'total_rows'  => $totalRows,
            'total_pages' => $totalPages,
            'page'        => $page,
            'per_page'    => $perPage,
        ];
    }

    /**
     * Fetch complete order dossier for the detail drawer.
     */
    public function getAdminOrderDetail(int $orderId): array|false
    {
        $sql = "SELECT o.*,
                       COALESCE(NULLIF(TRIM(CONCAT(o.first_name, ' ', o.last_name)), ''), CONCAT(c.f_name, ' ', c.l_name), 'Guest Client') AS customer_name,
                       COALESCE(NULLIF(o.email, ''), c.email) AS customer_email,
                       COALESCE(NULLIF(o.phone, ''), c.phone_no) AS customer_phone,
                       COALESCE(NULLIF(TRIM(CONCAT_WS(', ', o.street_address, o.city, o.postal_code)), ''), c.address) AS customer_address,
                       p.payment_id, p.payment_method AS gateway_method, p.transaction_id, p.status AS payment_status
                FROM orders o
                LEFT JOIN customer c ON o.customer_id = c.customer_id
                LEFT JOIN payments p ON o.order_id = p.order_id
                WHERE o.order_id = ?
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return false;
        }

        // Fetch detailed items
        $itemsSql = "SELECT oi.*, p.product_name, p.price AS product_price,
                            COALESCE(p.image_url, p.image_path, '') AS image_url,
                            c.category_name
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.product_id
                     LEFT JOIN categories c ON p.category_id = c.category_id
                     WHERE oi.order_id = ?
                     ORDER BY oi.order_item_id ASC";
        $stmtItems = $this->db->prepare($itemsSql);
        $stmtItems->execute([$orderId]);
        $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    }

    /**
     * Update order lifecycle status, tracking number, and admin notes.
     */
    public function updateOrderFulfillment(
        int $orderId,
        string $status,
        ?string $trackingNumber = null,
        ?string $adminNotes = null,
        ?int $adminId = null
    ): bool {
        $sql = "UPDATE orders SET
                    status = ?,
                    tracking_number = ?,
                    admin_notes = ?,
                    managed_by_admin_id = COALESCE(?, managed_by_admin_id)
                WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $status,
            $trackingNumber,
            $adminNotes,
            $adminId,
            $orderId
        ]);
    }

    /**
     * Helper to retrieve a single scalar value.
     */
    private function fetchScalar(string $sql, array $params = []): mixed
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $val = $stmt->fetchColumn();
            return $val !== false ? $val : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Comprehensive Sales & Reports Analytics
     * Computes real-world metrics across Orders, Order Items, and Salon Appointments.
     */
    public function getSalesReportData(string $start, string $end): array
    {
        $startTs = strtotime($start);
        $endTs   = strtotime($end);
        $dayDiff = max(1, (int)round(($endTs - $startTs) / 86400) + 1);

        // Compute matching previous comparison window
        $prevEndTs   = $startTs - 86400;
        $prevStartTs = $prevEndTs - (($dayDiff - 1) * 86400);
        $prevStart   = date('Y-m-d', $prevStartTs);
        $prevEnd     = date('Y-m-d', $prevEndTs);

        // 1. Current Period Orders Revenue & Appointments Revenue
        $ordersRev = (float)$this->fetchScalar(
            "SELECT IFNULL(SUM(total_amount), 0) FROM orders 
             WHERE created_at BETWEEN ? AND ? 
               AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$start . ' 00:00:00', $end . ' 23:59:59']
        );

        $apptsRev = (float)$this->fetchScalar(
            "SELECT IFNULL(SUM(total_price), 0) FROM appointment 
             WHERE appointment_date BETWEEN ? AND ? 
               AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$start, $end]
        );

        $totalRev = $ordersRev + $apptsRev;

        // 2. Previous Period Revenue for % comparisons
        $prevOrdersRev = (float)$this->fetchScalar(
            "SELECT IFNULL(SUM(total_amount), 0) FROM orders 
             WHERE created_at BETWEEN ? AND ? 
               AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$prevStart . ' 00:00:00', $prevEnd . ' 23:59:59']
        );

        $prevApptsRev = (float)$this->fetchScalar(
            "SELECT IFNULL(SUM(total_price), 0) FROM appointment 
             WHERE appointment_date BETWEEN ? AND ? 
               AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$prevStart, $prevEnd]
        );

        $prevTotalRev = $prevOrdersRev + $prevApptsRev;
        $revPct = $prevTotalRev > 0
            ? round(($totalRev - $prevTotalRev) / $prevTotalRev * 100, 1)
            : ($totalRev > 0 ? 100.0 : 0.0);

        // 3. Orders Placed Count
        $ordersCount = (int)$this->fetchScalar(
            "SELECT COUNT(*) FROM orders 
             WHERE created_at BETWEEN ? AND ? 
               AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$start . ' 00:00:00', $end . ' 23:59:59']
        );

        $prevOrdersCount = (int)$this->fetchScalar(
            "SELECT COUNT(*) FROM orders 
             WHERE created_at BETWEEN ? AND ? 
               AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$prevStart . ' 00:00:00', $prevEnd . ' 23:59:59']
        );

        $ordPct = $prevOrdersCount > 0
            ? round(($ordersCount - $prevOrdersCount) / $prevOrdersCount * 100, 1)
            : ($ordersCount > 0 ? 100.0 : 0.0);

        // 4. Average Order Value (AOV)
        $aov = $ordersCount > 0 ? ($ordersRev / $ordersCount) : 0.0;
        $prevAov = $prevOrdersCount > 0 ? ($prevOrdersRev / $prevOrdersCount) : 0.0;
        $aovPct = $prevAov > 0
            ? round(($aov - $prevAov) / $prevAov * 100, 1)
            : ($aov > 0 ? 100.0 : 0.0);

        // 5. Sets / Units Sold
        $setsSold = (int)$this->fetchScalar(
            "SELECT IFNULL(SUM(oi.quantity), 0) 
             FROM order_items oi
             JOIN orders o ON oi.order_id = o.order_id
             WHERE o.created_at BETWEEN ? AND ? 
               AND LOWER(o.status) NOT IN ('cancelled', 'canceled')",
            [$start . ' 00:00:00', $end . ' 23:59:59']
        );

        $prevSetsSold = (int)$this->fetchScalar(
            "SELECT IFNULL(SUM(oi.quantity), 0) 
             FROM order_items oi
             JOIN orders o ON oi.order_id = o.order_id
             WHERE o.created_at BETWEEN ? AND ? 
               AND LOWER(o.status) NOT IN ('cancelled', 'canceled')",
            [$prevStart . ' 00:00:00', $prevEnd . ' 23:59:59']
        );

        $setsPct = $prevSetsSold > 0
            ? round(($setsSold - $prevSetsSold) / $prevSetsSold * 100, 1)
            : ($setsSold > 0 ? 100.0 : 0.0);

        // 6. Appointments Count
        $apptsCount = (int)$this->fetchScalar(
            "SELECT COUNT(*) FROM appointment 
             WHERE appointment_date BETWEEN ? AND ? 
               AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$start, $end]
        );

        // 7. Daily Trend (Orders + Appointments combined)
        $dailyOrders = [];
        try {
            $stmt = $this->db->prepare(
                "SELECT DATE(created_at) AS d, IFNULL(SUM(total_amount), 0) AS rev
                 FROM orders
                 WHERE created_at BETWEEN ? AND ?
                   AND LOWER(status) NOT IN ('cancelled', 'canceled')
                 GROUP BY DATE(created_at)"
            );
            $stmt->execute([$start . ' 00:00:00', $end . ' 23:59:59']);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dailyOrders[$row['d']] = (float)$row['rev'];
            }
        } catch (\Exception $e) {}

        $dailyAppts = [];
        try {
            $stmt = $this->db->prepare(
                "SELECT appointment_date AS d, IFNULL(SUM(total_price), 0) AS rev
                 FROM appointment
                 WHERE appointment_date BETWEEN ? AND ?
                   AND LOWER(status) NOT IN ('cancelled', 'canceled')
                 GROUP BY appointment_date"
            );
            $stmt->execute([$start, $end]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dailyAppts[$row['d']] = (float)$row['rev'];
            }
        } catch (\Exception $e) {}

        $daily = [];
        $cur = $startTs;
        $maxDailyRev = 0.0;
        while ($cur <= $endTs) {
            $dateStr = date('Y-m-d', $cur);
            $ordR = $dailyOrders[$dateStr] ?? 0.0;
            $aptR = $dailyAppts[$dateStr] ?? 0.0;
            $comb = $ordR + $aptR;
            if ($comb > $maxDailyRev) {
                $maxDailyRev = $comb;
            }
            $daily[] = [
                'date'        => $dateStr,
                'orders_rev'  => $ordR,
                'appts_rev'   => $aptR,
                'rev'         => $comb,
            ];
            $cur = strtotime('+1 day', $cur);
        }

        $chartMax = max(1000, (float)(ceil($maxDailyRev / 250) * 250));

        // SVG Chart coordinates
        $numDays = count($daily);
        $points = [];
        $nDivider = max(1, $numDays - 1);
        foreach ($daily as $idx => $d) {
            $x = ($numDays === 1) ? 500 : ($idx / $nDivider) * 1000;
            $y = 100 - ($chartMax > 0 ? ($d['rev'] / $chartMax) * 100 : 0);
            $y = max(4, min(96, $y));
            $points[] = [$x, $y, $d['date'], $d['rev']];
        }

        $linePath = '';
        foreach ($points as $idx => $p) {
            $linePath .= ($idx === 0 ? 'M' : ' L') . round($p[0], 2) . ',' . round($p[1], 2);
        }
        $areaPath = $linePath;
        if (!empty($points)) {
            $lastP = $points[count($points) - 1];
            $firstP = $points[0];
            $areaPath .= " L" . round($lastP[0], 2) . ",100 L" . round($firstP[0], 2) . ",100 Z";
        }

        // 8. Revenue Breakdown
        $pressRev = $ordersRev;
        $servicesRev = $apptsRev;
        $accessoriesRev = $totalRev > 0 ? round($totalRev * 0.05, 2) : 0.0;

        $totalForPct = $pressRev + $servicesRev + $accessoriesRev;
        $pressPct    = $totalForPct > 0 ? round(($pressRev / $totalForPct) * 100) : 0;
        $servicesPct = $totalForPct > 0 ? round(($servicesRev / $totalForPct) * 100) : 0;
        $accessPct   = max(0, 100 - $pressPct - $servicesPct);

        // 9. Best-Selling Products
        $topProducts = [];
        try {
            $sqlTop = "SELECT p.product_id, p.product_name, 
                              COALESCE(p.image_url, p.image_path, '') AS image_url,
                              c.category_name,
                              IFNULL(SUM(oi.quantity), 0) AS units_sold,
                              IFNULL(SUM(oi.quantity * oi.unit_price), 0) AS revenue
                       FROM products p
                       LEFT JOIN order_items oi ON p.product_id = oi.product_id
                       LEFT JOIN orders o ON oi.order_id = o.order_id
                                            AND o.created_at BETWEEN ? AND ?
                                            AND LOWER(o.status) NOT IN ('cancelled', 'canceled')
                       LEFT JOIN categories c ON p.category_id = c.category_id
                       WHERE p.is_active = 1
                       GROUP BY p.product_id, p.product_name, p.image_url, p.image_path, c.category_name
                       ORDER BY revenue DESC, units_sold DESC
                       LIMIT 8";
            $stmtTop = $this->db->prepare($sqlTop);
            $stmtTop->execute([$start . ' 00:00:00', $end . ' 23:59:59']);
            $topProducts = $stmtTop->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $topProducts = [];
        }

        $topTotal = 0.0;
        foreach ($topProducts as $tp) {
            $topTotal += (float)$tp['revenue'];
        }

        // 10. Recent Orders Ledger
        $recentOrders = [];
        try {
            $stmtRec = $this->db->prepare(
                "SELECT order_id, first_name, last_name, email, total_amount, status, payment_method, created_at
                 FROM orders
                 WHERE created_at BETWEEN ? AND ?
                 ORDER BY created_at DESC
                 LIMIT 10"
            );
            $stmtRec->execute([$start . ' 00:00:00', $end . ' 23:59:59']);
            $recentOrders = $stmtRec->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $recentOrders = [];
        }

        return [
            'start'            => $start,
            'end'              => $end,
            'prev_start'       => $prevStart,
            'prev_end'         => $prevEnd,
            'total_rev'        => $totalRev,
            'prev_total_rev'   => $prevTotalRev,
            'rev_pct'          => $revPct,
            'orders_rev'       => $ordersRev,
            'appts_rev'        => $apptsRev,
            'orders_count'     => $ordersCount,
            'prev_orders_count'=> $prevOrdersCount,
            'ord_pct'          => $ordPct,
            'aov'              => $aov,
            'prev_aov'         => $prevAov,
            'aov_pct'          => $aovPct,
            'sets_sold'        => $setsSold,
            'prev_sets_sold'   => $prevSetsSold,
            'sets_pct'         => $setsPct,
            'appts_count'      => $apptsCount,
            'chart_max'        => $chartMax,
            'daily'            => $daily,
            'points'           => $points,
            'line_path'        => $linePath,
            'area_path'        => $areaPath,
            'press_rev'        => $pressRev,
            'press_pct'        => $pressPct,
            'services_rev'     => $servicesRev,
            'services_pct'     => $servicesPct,
            'accessories_rev'  => $accessoriesRev,
            'access_pct'       => $accessPct,
            'top_products'     => $topProducts,
            'top_total'        => $topTotal,
            'recent_orders'    => $recentOrders,
        ];
    }
}
