<?php
/**
 * AdminController.php — Admin Panel Controller
 * Monet Nails MVC Application
 */

require_once __DIR__ . '/../config/db.php';

class AdminController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Dashboard & Atelier Overview
     */
    public function dashboard(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verify admin access
        if (!isset($_SESSION['admin_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
            $_SESSION['redirect_after_login'] = 'admin';
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&redirect=admin');
            exit;
        }

        $adminId   = (int)($_SESSION['admin_id'] ?? 0);
        $adminName = $_SESSION['admin_name'] ?? 'Damika Rajithuru';
        $adminParts = preg_split('/\s+/', trim($adminName));
        $adminFirstName = $adminParts[0] ?? 'Damika';
        $adminInitials  = strtoupper(substr($adminFirstName, 0, 1) . substr($adminParts[1] ?? '', 0, 1));
        if (empty($adminInitials)) {
            $adminInitials = 'MA';
        }

        // 1. Determine Week Period (Monday to Sunday)
        $today     = date("Y-m-d");
        $dow       = (int)date("N"); // 1=Mon .. 7=Sun
        $weekStart = date("Y-m-d", strtotime("-" . ($dow - 1) . " days"));
        $weekEnd   = date("Y-m-d", strtotime("+" . (7 - $dow) . " days"));
        $prevWeekStart = date("Y-m-d", strtotime("$weekStart -7 days"));
        $prevWeekEnd   = date("Y-m-d", strtotime("$weekEnd -7 days"));

        // Helper closures for clean querying
        $getVal = function (string $sql, array $params = []) {
            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                $val = $stmt->fetchColumn();
                return $val !== false ? $val : 0;
            } catch (Exception $e) {
                return 0;
            }
        };

        // 2. Total sales this week (Orders + Appointments)
        $salesOrders = (float)$getVal(
            "SELECT IFNULL(SUM(total_amount), 0) FROM orders
             WHERE created_at BETWEEN ? AND ? AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$weekStart . ' 00:00:00', $weekEnd . ' 23:59:59']
        );

        $salesAppts = (float)$getVal(
            "SELECT IFNULL(SUM(s.price), 0) FROM appointment a
             JOIN service s ON a.service_id = s.service_id
             WHERE a.appointment_date BETWEEN ? AND ? AND LOWER(a.status) NOT IN ('cancelled', 'canceled')",
            [$weekStart, $weekEnd]
        );
        $totalSales = $salesOrders + $salesAppts;

        // Previous-week sales for comparison
        $prevSalesOrders = (float)$getVal(
            "SELECT IFNULL(SUM(total_amount), 0) FROM orders
             WHERE created_at BETWEEN ? AND ? AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$prevWeekStart . ' 00:00:00', $prevWeekEnd . ' 23:59:59']
        );
        $prevSalesAppts = (float)$getVal(
            "SELECT IFNULL(SUM(s.price), 0) FROM appointment a
             JOIN service s ON a.service_id = s.service_id
             WHERE a.appointment_date BETWEEN ? AND ? AND LOWER(a.status) NOT IN ('cancelled', 'canceled')",
            [$prevWeekStart, $prevWeekEnd]
        );
        $prevTotalSales = $prevSalesOrders + $prevSalesAppts;
        $salesPct = $prevTotalSales > 0
            ? round(($totalSales - $prevTotalSales) / $prevTotalSales * 100, 1)
            : ($totalSales > 0 ? 100.0 : 0.0);

        // 3. Orders Count & Pending Count
        $ordersCount = (int)$getVal(
            "SELECT COUNT(*) FROM orders
             WHERE created_at BETWEEN ? AND ? AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$weekStart . ' 00:00:00', $weekEnd . ' 23:59:59']
        );
        $prevOrders = (int)$getVal(
            "SELECT COUNT(*) FROM orders
             WHERE created_at BETWEEN ? AND ? AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$prevWeekStart . ' 00:00:00', $prevWeekEnd . ' 23:59:59']
        );
        $ordersDiff = $ordersCount - $prevOrders;
        $pendingCount = (int)$getVal(
            "SELECT COUNT(*) FROM orders WHERE LOWER(status) IN ('pending', 'processing')"
        );

        // 4. Appointments Count
        $apptsCount = (int)$getVal(
            "SELECT COUNT(*) FROM appointment
             WHERE appointment_date BETWEEN ? AND ? AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$weekStart, $weekEnd]
        );
        $prevAppts = (int)$getVal(
            "SELECT COUNT(*) FROM appointment
             WHERE appointment_date BETWEEN ? AND ? AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$prevWeekStart, $prevWeekEnd]
        );
        $apptsDiff = $apptsCount - $prevAppts;
        $apptsToday = (int)$getVal(
            "SELECT COUNT(*) FROM appointment
             WHERE appointment_date = ? AND LOWER(status) NOT IN ('cancelled', 'canceled')",
            [$today]
        );

        // 5. Average Order Value (AOV)
        $aov = $ordersCount > 0 ? $totalSales / $ordersCount : 0;
        $prevAov = $prevOrders > 0 ? $prevTotalSales / $prevOrders : 0;
        $aovPct = $prevAov > 0 ? round(($aov - $prevAov) / $prevAov * 100, 1) : 0;

        // 6. 7-Day Daily Breakdown for Chart
        $daily = [];
        $maxDaily = 0;
        for ($i = 0; $i < 7; $i++) {
            $d = date("Y-m-d", strtotime("$weekStart +$i days"));
            $lbl = substr(date("D", strtotime($d)), 0, 3);

            $o = (float)$getVal(
                "SELECT IFNULL(SUM(total_amount), 0) FROM orders
                 WHERE DATE(created_at) = ? AND LOWER(status) NOT IN ('cancelled', 'canceled')",
                [$d]
            );
            $a = (float)$getVal(
                "SELECT IFNULL(SUM(s.price), 0) FROM appointment app
                 JOIN service s ON app.service_id = s.service_id
                 WHERE app.appointment_date = ? AND LOWER(app.status) NOT IN ('cancelled', 'canceled')",
                [$d]
            );
            $daily[] = ["label" => $lbl, "press_on" => $o, "nail_bar" => $a];
            $maxDaily = max($maxDaily, $o + $a);
        }
        $chartMax = $maxDaily > 0 ? $maxDaily : 1000;

        // 7. Upcoming Appointments
        $upcoming = [];
        try {
            $stmt = $this->pdo->prepare(
                "SELECT a.appointment_time, a.appointment_date,
                        c.f_name, c.l_name,
                        s.name AS service_name, s.duration AS duration_minutes
                 FROM appointment a
                 JOIN service s ON a.service_id = s.service_id
                 JOIN customer c ON a.customer_id = c.customer_id
                 WHERE a.appointment_date >= ?
                   AND LOWER(a.status) NOT IN ('cancelled', 'canceled')
                 ORDER BY a.appointment_date ASC, a.appointment_time ASC
                 LIMIT 4"
            );
            $stmt->execute([$today]);
            $upcoming = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $upcoming = [];
        }

        // 8. Recent Orders
        $recentOrders = [];
        try {
            $stmt = $this->pdo->query(
                "SELECT o.order_id, o.total_amount, o.status,
                        CONCAT(o.first_name, ' ', o.last_name) AS customer_name,
                        COALESCE(
                            (SELECT p.product_name FROM order_items oi
                             JOIN products p ON oi.product_id = p.product_id
                             WHERE oi.order_id = o.order_id LIMIT 1),
                            'Signature Press-On Set'
                        ) AS product_name
                 FROM orders o
                 ORDER BY o.order_id DESC
                 LIMIT 5"
            );
            $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $recentOrders = [];
        }

        // 9. Low Stock Products
        $lowStock = [];
        $lowStockCount = 0;
        try {
            $stmt = $this->pdo->query(
                "SELECT product_name, stock_quantity
                 FROM products
                 WHERE is_active = 1 AND stock_quantity <= 5
                 ORDER BY stock_quantity ASC
                 LIMIT 3"
            );
            $lowStock = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $lowStockCount = (int)$getVal(
                "SELECT COUNT(*) FROM products WHERE is_active = 1 AND stock_quantity <= 5"
            );
        } catch (Exception $e) {
            $lowStock = [];
            $lowStockCount = 0;
        }

        // Pass variables to view
        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function manageServices() {
        // CRUD for nail services
        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/admin/services.php');
        exit;
    }

    public function manageAppointments() {
        // Manage booking schedules
        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/admin/appointments.php');
        exit;
    }

    public function manageOrders() {
        // View and update customer orders
        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/admin/orders.php');
        exit;
    }

    public function manageUsers() {
        // CRUD for users and staff members
        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/admin/users.php');
        exit;
    }
}
