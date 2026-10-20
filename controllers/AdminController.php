<?php
/**
 * AdminController.php — Admin Panel Controller
 * Monet Nails MVC Application
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Order.php';

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

    // =========================================================================
    // PRODUCT CATALOG & INVENTORY  (admin role only)
    // =========================================================================

    public function manageProducts(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Auth guard
        if (!isset($_SESSION['admin_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&redirect=admin_products');
            exit;
        }

        $product = new Product($this->pdo);
        $base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
        $adminName     = $_SESSION['admin_name'] ?? 'Admin';
        $adminParts    = preg_split('/\s+/', trim($adminName));
        $adminInitials = strtoupper(substr($adminParts[0] ?? 'A', 0, 1) . substr($adminParts[1] ?? '', 0, 1));
        if (empty($adminInitials)) $adminInitials = 'MA';

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // ---- POST actions (AJAX or regular form) ----------------------------
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            // AJAX: Quick stock adjustment (+1 button)
            if ($action === 'adjust_stock' && $isAjax) {
                $pid   = (int)($_POST['product_id'] ?? 0);
                $delta = (int)($_POST['delta'] ?? 1);
                $product->adjustStock($pid, $delta);
                header('Content-Type: application/json');
                $row = $product->findById($pid);
                echo json_encode(['ok' => true, 'stock' => $row['stock_quantity'] ?? 0]);
                exit;
            }

            // AJAX: Set stock directly
            if ($action === 'set_stock' && $isAjax) {
                $pid = (int)($_POST['product_id'] ?? 0);
                $qty = (int)($_POST['quantity']   ?? 0);
                $product->setStock($pid, $qty);
                header('Content-Type: application/json');
                echo json_encode(['ok' => true, 'stock' => max(0, $qty)]);
                exit;
            }

            // Add new product
            if ($action === 'add_product') {
                $imageUrl = null;
                if (!empty($_FILES['image']['name'])) {
                    $uploadDir = __DIR__ . '/../uploads/';
                    $ext       = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed   = ['jpg','jpeg','png','gif','webp'];
                    if (in_array($ext, $allowed, true) && $_FILES['image']['size'] < 5 * 1024 * 1024) {
                        $fname    = 'prod_' . time() . '_' . random_int(1000, 9999) . '.' . $ext;
                        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fname);
                        $imageUrl = 'uploads/' . $fname;
                    }
                }
                $data = [
                    'category_id'    => (int)($_POST['category_id']    ?? 1),
                    'sku'            => trim($_POST['sku']              ?? ''),
                    'product_name'   => trim($_POST['product_name']     ?? ''),
                    'description'    => trim($_POST['description']      ?? ''),
                    'price'          => (float)($_POST['price']         ?? 0),
                    'stock_quantity' => (int)($_POST['stock_quantity']  ?? 0),
                    'image_url'      => $imageUrl,
                    'shape'          => trim($_POST['shape']            ?? ''),
                    'finish'         => trim($_POST['finish']           ?? ''),
                    'material_info'  => trim($_POST['material_info']    ?? ''),
                    'includes_info'  => trim($_POST['includes_info']    ?? ''),
                    'shipping_info'  => trim($_POST['shipping_info']    ?? ''),
                    'is_active'      => isset($_POST['is_active']) ? 1 : 0,
                ];
                $product->create($data);
                header('Location: ' . $base_url . '/index.php?page=admin_products&success=created');
                exit;
            }

            // Edit existing product
            if ($action === 'edit_product') {
                $pid      = (int)($_POST['product_id'] ?? 0);
                $existing = $product->findById($pid);
                $imageUrl = $existing['image_url'] ?? null;
                if (!empty($_FILES['image']['name'])) {
                    $uploadDir = __DIR__ . '/../uploads/';
                    $ext       = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed   = ['jpg','jpeg','png','gif','webp'];
                    if (in_array($ext, $allowed, true) && $_FILES['image']['size'] < 5 * 1024 * 1024) {
                        $fname    = 'prod_' . time() . '_' . random_int(1000, 9999) . '.' . $ext;
                        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fname);
                        $imageUrl = 'uploads/' . $fname;
                    }
                }
                $data = [
                    'category_id'    => (int)($_POST['category_id']    ?? 1),
                    'sku'            => trim($_POST['sku']              ?? ''),
                    'product_name'   => trim($_POST['product_name']     ?? ''),
                    'description'    => trim($_POST['description']      ?? ''),
                    'price'          => (float)($_POST['price']         ?? 0),
                    'stock_quantity' => (int)($_POST['stock_quantity']  ?? 0),
                    'image_url'      => $imageUrl,
                    'shape'          => trim($_POST['shape']            ?? ''),
                    'finish'         => trim($_POST['finish']           ?? ''),
                    'material_info'  => trim($_POST['material_info']    ?? ''),
                    'includes_info'  => trim($_POST['includes_info']    ?? ''),
                    'shipping_info'  => trim($_POST['shipping_info']    ?? ''),
                    'is_active'      => isset($_POST['is_active']) ? 1 : 0,
                ];
                $product->update($pid, $data);
                header('Location: ' . $base_url . '/index.php?page=admin_products&success=updated');
                exit;
            }

            // Delete product
            if ($action === 'delete_product') {
                $pid = (int)($_POST['product_id'] ?? 0);
                try {
                    $product->hardDelete($pid);
                } catch (\Exception $e) {
                    // FK violation — fall back to soft-delete
                    $product->softDelete($pid);
                }
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['ok' => true]);
                    exit;
                }
                header('Location: ' . $base_url . '/index.php?page=admin_products&success=deleted');
                exit;
            }
        }

        // ---- GET: fetch list data -------------------------------------------
        $search      = trim($_GET['search']       ?? '');
        $categoryId  = (int)($_GET['category_id'] ?? 0);
        $stockFilter = $_GET['stock']             ?? 'all';
        $statusFilter= $_GET['status']            ?? 'all';
        $perPage     = max(1, (int)($_GET['per_page'] ?? 10));
        $currentPage = max(1, (int)($_GET['pg']       ?? 1));

        // Stats cards
        $total_active    = $product->countActive();
        $low_stock       = $product->countLowStock();
        $out_of_stock    = $product->countOutOfStock();
        $inventory_value = $product->inventoryValue();

        // Product list
        $result   = $product->adminList($search, $categoryId, $stockFilter, $statusFilter, $currentPage, $perPage);
        $products = $result['rows'];
        $totalRows= $result['total'];
        $totalPages = max(1, (int)ceil($totalRows / $perPage));

        // Categories for filter dropdown
        $categories = [];
        try {
            $categories = $this->pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) { $categories = []; }

        // Flash message
        $flashMsg = '';
        switch ($_GET['success'] ?? '') {
            case 'created': $flashMsg = 'Product added successfully.';   break;
            case 'updated': $flashMsg = 'Product updated successfully.'; break;
            case 'deleted': $flashMsg = 'Product deleted successfully.'; break;
        }

        require __DIR__ . '/../views/admin/products.php';
    }

    public function manageServices(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin');
            exit;
        }
        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/admin/services.php');
        exit;
    }

    public function manageAppointments(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Auth guard
        if (!isset($_SESSION['admin_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&redirect=admin_appointments');
            exit;
        }

        $appointmentModel = new Appointment($this->pdo);
        $base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
        $adminName = $_SESSION['admin_name'] ?? 'Sarah Chen';
        $adminParts = preg_split('/\s+/', trim($adminName));
        $adminInitials = strtoupper(substr($adminParts[0] ?? 'S', 0, 1) . substr($adminParts[1] ?? '', 0, 1));
        if (empty($adminInitials)) $adminInitials = 'SC';

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // POST actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            // AJAX: Quick Status change (confirmed, completed, cancelled)
            if ($action === 'update_status') {
                $aptId = (int)($_POST['appointment_id'] ?? 0);
                $status = trim($_POST['status'] ?? 'confirmed');
                $appointmentModel->updateStatus($aptId, $status);
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['ok' => true, 'status' => $status]);
                    exit;
                }
                header('Location: ' . $base_url . '/index.php?page=admin_appointments&success=status_updated');
                exit;
            }

            // AJAX or POST: Reschedule appointment date & slot
            if ($action === 'reschedule') {
                $aptId = (int)($_POST['appointment_id'] ?? 0);
                $date = trim($_POST['appointment_date'] ?? '');
                $time = trim($_POST['appointment_time'] ?? '');
                $appointmentModel->reschedule($aptId, $date, $time);
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['ok' => true]);
                    exit;
                }
                header('Location: ' . $base_url . '/index.php?page=admin_appointments&success=rescheduled');
                exit;
            }

            // Create walk-in / new appointment
            if ($action === 'create_appointment') {
                $data = [
                    'service_id'          => (int)($_POST['service_id'] ?? 1),
                    'customer_id'         => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
                    'managed_by_admin_id' => (int)($_SESSION['admin_id'] ?? 1),
                    'appointment_date'    => trim($_POST['appointment_date'] ?? date('Y-m-d')),
                    'appointment_time'    => trim($_POST['appointment_time'] ?? '10:00:00'),
                    'first_name'          => trim($_POST['first_name'] ?? ''),
                    'last_name'           => trim($_POST['last_name'] ?? ''),
                    'email'               => trim($_POST['email'] ?? ''),
                    'client_tier'         => trim($_POST['client_tier'] ?? 'Patron'),
                    'phone'               => trim($_POST['phone'] ?? ''),
                    'special_requests'    => trim($_POST['special_requests'] ?? ''),
                    'total_price'         => (float)($_POST['total_price'] ?? 0),
                    'payment_status'      => trim($_POST['payment_status'] ?? 'pending'),
                    'status'              => trim($_POST['status'] ?? 'confirmed'),
                ];
                $appointmentModel->createAdminAppointment($data);
                header('Location: ' . $base_url . '/index.php?page=admin_appointments&success=created');
                exit;
            }

            // Edit appointment details
            if ($action === 'edit_appointment') {
                $aptId = (int)($_POST['appointment_id'] ?? 0);
                $data = [
                    'service_id'          => (int)($_POST['service_id'] ?? 1),
                    'appointment_date'    => trim($_POST['appointment_date'] ?? date('Y-m-d')),
                    'appointment_time'    => trim($_POST['appointment_time'] ?? '10:00:00'),
                    'first_name'          => trim($_POST['first_name'] ?? ''),
                    'last_name'           => trim($_POST['last_name'] ?? ''),
                    'email'               => trim($_POST['email'] ?? ''),
                    'client_tier'         => trim($_POST['client_tier'] ?? 'Patron'),
                    'phone'               => trim($_POST['phone'] ?? ''),
                    'special_requests'    => trim($_POST['special_requests'] ?? ''),
                    'total_price'         => (float)($_POST['total_price'] ?? 0),
                    'status'              => trim($_POST['status'] ?? 'confirmed'),
                    'managed_by_admin_id' => !empty($_POST['managed_by_admin_id']) ? (int)$_POST['managed_by_admin_id'] : null,
                ];
                $appointmentModel->updateAppointment($aptId, $data);
                header('Location: ' . $base_url . '/index.php?page=admin_appointments&success=updated');
                exit;
            }

            // Delete appointment
            if ($action === 'delete_appointment') {
                $aptId = (int)($_POST['appointment_id'] ?? 0);
                $appointmentModel->deleteAppointment($aptId);
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['ok' => true]);
                    exit;
                }
                header('Location: ' . $base_url . '/index.php?page=admin_appointments&success=deleted');
                exit;
            }
        }

        // GET: Fetch list & stats
        $search      = trim($_GET['search']    ?? '');
        $category    = trim($_GET['category']  ?? '');
        $status      = trim($_GET['status']    ?? 'all');
        $timeSlot    = trim($_GET['time_slot'] ?? 'all');
        $perPage     = max(1, (int)($_GET['per_page'] ?? 10));
        $currentPage = max(1, (int)($_GET['pg']       ?? 1));

        // 4 KPI Stats
        $stats = $appointmentModel->getAdminStats();

        // Paginated list
        $result      = $appointmentModel->getAdminList($search, $category, $status, $timeSlot, $currentPage, $perPage);
        $appts       = $result['rows'];
        $totalRows   = $result['total'];
        $totalPages  = max(1, (int)ceil($totalRows / $perPage));

        // Active services for modal select
        $services = [];
        try {
            $services = $this->pdo->query("SELECT service_id, name, category, duration, price FROM service WHERE is_active = 1 ORDER BY sort_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) { $services = []; }

        // Categories for filter dropdown
        $categories = [];
        try {
            $categories = $this->pdo->query("SELECT DISTINCT category FROM service WHERE category IS NOT NULL AND category != '' ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);
        } catch (\Exception $e) { $categories = []; }

        // Flash message
        $flashMsg = '';
        switch ($_GET['success'] ?? '') {
            case 'created': $flashMsg = 'Appointment booked successfully.'; break;
            case 'updated': $flashMsg = 'Appointment details updated.'; break;
            case 'status_updated': $flashMsg = 'Appointment status updated.'; break;
            case 'rescheduled': $flashMsg = 'Appointment schedule adjusted.'; break;
            case 'deleted': $flashMsg = 'Appointment removed from salon schedule.'; break;
        }

        require __DIR__ . '/../views/admin/appointments.php';
    }

    public function manageOrders(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin');
            exit;
        }
        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=admin');
        exit;
    }

    public function manageUsers(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin');
            exit;
        }
        header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=admin');
        exit;
    }

    public function salesReports(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verify admin access
        if (!isset($_SESSION['admin_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
            $_SESSION['redirect_after_login'] = 'admin_reports';
            header('Location: /MonetNails-Ecommerce2/MonetNails-Ecommerce/index.php?page=signin&redirect=admin_reports');
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

        // Period filter
        $period = $_GET['period'] ?? '';
        $today = date('Y-m-d');

        if ($period === 'today') {
            $start = $today;
            $end   = $today;
        } elseif ($period === 'this_week') {
            $dow = (int)date('N');
            $start = date('Y-m-d', strtotime('-' . ($dow - 1) . ' days'));
            $end   = date('Y-m-d', strtotime('+' . (7 - $dow) . ' days'));
        } elseif ($period === 'last_month') {
            $start = date('Y-m-01', strtotime('-1 month'));
            $end   = date('Y-m-t',  strtotime('-1 month'));
        } elseif ($period === 'this_year') {
            $start = date('Y-01-01');
            $end   = date('Y-12-31');
        } elseif ($period === 'all_time') {
            $start = '2024-01-01';
            $end   = $today;
        } elseif (!empty($_GET['start']) && !empty($_GET['end'])) {
            $start = substr(trim($_GET['start']), 0, 10);
            $end   = substr(trim($_GET['end']), 0, 10);
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
                $start = date('Y-m-01');
                $end   = date('Y-m-t');
            }
            if ($start > $end) {
                $tmp = $start;
                $start = $end;
                $end = $tmp;
            }
            $period = 'custom';
        } else {
            $start  = date('Y-m-01');
            $end    = date('Y-m-t');
            $period = 'this_month';
        }

        $orderModel = new Order($this->pdo);
        $reportData = $orderModel->getSalesReportData($start, $end);

        // CSV Export Handler
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="monet_atelier_sales_report_' . $start . '_to_' . $end . '.csv"');
            $out = fopen('php://output', 'w');

            fputcsv($out, ['MONET ATELIER — SALES & PERFORMANCE REPORT']);
            fputcsv($out, ['Reporting Period', $start . ' to ' . $end]);
            fputcsv($out, ['Generated At', date('Y-m-d H:i:s')]);
            fputcsv($out, []);

            // 1. Key Metrics
            fputcsv($out, ['KEY METRICS', 'CURRENT PERIOD', 'PREVIOUS PERIOD', 'VARIANCE %']);
            fputcsv($out, ['Total Revenue ($)', number_format($reportData['total_rev'], 2, '.', ''), number_format($reportData['prev_total_rev'], 2, '.', ''), $reportData['rev_pct'] . '%']);
            fputcsv($out, ['Orders Placed', $reportData['orders_count'], $reportData['prev_orders_count'], $reportData['ord_pct'] . '%']);
            fputcsv($out, ['Average Order Value ($)', number_format($reportData['aov'], 2, '.', ''), number_format($reportData['prev_aov'], 2, '.', ''), $reportData['aov_pct'] . '%']);
            fputcsv($out, ['Press-on Sets Sold', $reportData['sets_sold'], $reportData['prev_sets_sold'], $reportData['sets_pct'] . '%']);
            fputcsv($out, ['Atelier Appointments', $reportData['appts_count'], '-', '-']);
            fputcsv($out, []);

            // 2. Revenue Breakdown
            fputcsv($out, ['REVENUE BREAKDOWN', 'AMOUNT ($)', 'SHARE %']);
            fputcsv($out, ['Press-on Sets & E-Commerce', number_format($reportData['press_rev'], 2, '.', ''), $reportData['press_pct'] . '%']);
            fputcsv($out, ['Nail Bar Studio Services', number_format($reportData['services_rev'], 2, '.', ''), $reportData['services_pct'] . '%']);
            fputcsv($out, ['Care & Accessories', number_format($reportData['accessories_rev'], 2, '.', ''), $reportData['access_pct'] . '%']);
            fputcsv($out, []);

            // 3. Best Selling Products
            fputcsv($out, ['BEST SELLING PRODUCTS']);
            fputcsv($out, ['Product ID', 'Product Name', 'Category', 'Units Sold', 'Revenue ($)', 'Product Share %']);
            foreach ($reportData['top_products'] as $tp) {
                $sh = $reportData['top_total'] > 0 ? round(($tp['revenue'] / $reportData['top_total']) * 100, 1) : 0;
                fputcsv($out, [
                    $tp['product_id'],
                    $tp['product_name'],
                    $tp['category_name'] ?? 'Press-on nails',
                    $tp['units_sold'],
                    number_format($tp['revenue'], 2, '.', ''),
                    $sh . '%'
                ]);
            }
            fputcsv($out, []);

            // 4. Daily Trend
            fputcsv($out, ['DAILY REVENUE BREAKDOWN']);
            fputcsv($out, ['Date', 'Orders Revenue ($)', 'Appointments Revenue ($)', 'Total Daily Revenue ($)']);
            foreach ($reportData['daily'] as $d) {
                fputcsv($out, [
                    $d['date'],
                    number_format($d['orders_rev'], 2, '.', ''),
                    number_format($d['appts_rev'], 2, '.', ''),
                    number_format($d['rev'], 2, '.', '')
                ]);
            }

            fclose($out);
            exit;
        }

        $base_url = '/MonetNails-Ecommerce2/MonetNails-Ecommerce';
        require __DIR__ . '/../views/admin/reports.php';
    }
}
