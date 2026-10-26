<?php
/**
 * Service.php — Service Model
 * Monet Nails MVC Application
 *
 * Handles database operations for nail bar and salon services
 * stored in the `service` table. Supports both client-facing catalog
 * displays and admin management dashboard CRUD operations.
 */

class Service
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieve services ordered by sort_order / service_id
     *
     * @param bool $onlyActive If true, only returns active/visible services for clients
     */
    public function getAllServices(bool $onlyActive = true): array
    {
        try {
            $sql = "SELECT service_id, name, name AS service_name, description, price,
                           duration, duration AS duration_minutes, duration AS duration_min,
                           image_url, category, tag, icon_url, is_addon, is_active, sort_order,
                           created_at, updated_at
                    FROM service";
            if ($onlyActive) {
                $sql .= " WHERE is_active = 1";
            }
            $sql .= " ORDER BY sort_order ASC, service_id ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Retrieve all services for Admin management dashboard with optional search filtering
     */
    public function getAllServicesForAdmin(?string $search = null): array
    {
        try {
            $sql = "SELECT service_id, name, name AS service_name, description, price,
                           duration, duration AS duration_minutes, duration AS duration_min,
                           image_url, category, tag, icon_url, is_addon, is_active,
                           (is_active = 1) AS is_visible,
                           sort_order, managed_by_admin_id, created_at, updated_at
                    FROM service";
            $params = [];

            if ($search !== null && trim($search) !== '') {
                $term = '%' . trim(strtolower($search)) . '%';
                $sql .= " WHERE LOWER(name) LIKE ? OR LOWER(description) LIKE ? OR LOWER(category) LIKE ?";
                $params = [$term, $term, $term];
            }

            $sql .= " ORDER BY sort_order ASC, service_id ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Retrieve summary statistics for services
     */
    public function getServiceStats(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT
                    COUNT(*) AS total_count,
                    IFNULL(SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END), 0) AS visible_count,
                    IFNULL(SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END), 0) AS hidden_count,
                    IFNULL(AVG(price), 0) AS avg_price
                 FROM service"
            );
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'total'   => (int)($res['total_count'] ?? 0),
                'visible' => (int)($res['visible_count'] ?? 0),
                'hidden'  => (int)($res['hidden_count'] ?? 0),
                'avg_price' => (float)($res['avg_price'] ?? 0.0),
            ];
        } catch (Exception $e) {
            return ['total' => 0, 'visible' => 0, 'hidden' => 0, 'avg_price' => 0.0];
        }
    }

    /**
     * Retrieve services filtered by category (Manicure, Pedicure, Specialty)
     */
    public function getByCategory(string $category, bool $onlyActive = true): array
    {
        try {
            $sql = "SELECT * FROM service WHERE category = ?";
            if ($onlyActive) {
                $sql .= " AND is_active = 1";
            }
            $sql .= " ORDER BY sort_order ASC, service_id ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$category]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Retrieve all active services grouped into categories for client services page
     * Returns ['Manicure' => [...], 'Pedicure' => [...], 'Specialty' => [...]]
     */
    public function getAllGroupedByCategory(bool $onlyActive = true): array
    {
        $all = $this->getAllServices($onlyActive);
        $grouped = [
            'Manicure'  => [],
            'Pedicure'  => [],
            'Specialty' => []
        ];

        foreach ($all as $service) {
            $cat = ucfirst(strtolower(trim($service['category'] ?? 'Manicure')));
            if (!isset($grouped[$cat])) {
                $grouped[$cat] = [];
            }
            $grouped[$cat][] = $service;
        }

        return $grouped;
    }

    /**
     * Retrieve active services formatted for the appointment booking workflow.
     * Grouped by category (e.g. MANICURES, ENHANCEMENTS & ART).
     */
    public function getBookingCategories(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT service_id, category, name AS service_name, name, description,
                        price, duration AS duration_minutes, duration, is_addon, sort_order
                 FROM service
                 WHERE is_active = 1
                 ORDER BY is_addon ASC, sort_order ASC, service_id ASC"
            );
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $categories = [];
            foreach ($rows as $row) {
                $catName = strtoupper(trim($row['category'] ?? 'MANICURES'));
                if (!isset($categories[$catName])) {
                    $categories[$catName] = [
                        'name'     => $catName,
                        'services' => []
                    ];
                }
                $categories[$catName]['services'][] = $row;
            }

            return $categories;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Calculate server-side total price and duration for an appointment
     */
    public function calculateBookingSummary(int $mainServiceId, array $addonIds): array
    {
        $total = 0.0;
        $totalDuration = 0;
        $mainService = null;
        $addons = [];

        if ($mainServiceId > 0) {
            $mainService = $this->getServiceById($mainServiceId);
            if ($mainService) {
                $total += (float)$mainService['price'];
                $totalDuration += (int)$mainService['duration'];
            }
        }

        if (!empty($addonIds)) {
            $placeholders = implode(',', array_fill(0, count($addonIds), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM service WHERE service_id IN ($placeholders) AND is_addon = 1");
            $stmt->execute(array_values($addonIds));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $addons[] = $row;
                $total += (float)$row['price'];
                $totalDuration += (int)$row['duration'];
            }
        }

        return [
            'total'         => $total,
            'totalDuration' => $totalDuration,
            'mainService'   => $mainService,
            'addons'        => $addons
        ];
    }

    /**
     * Retrieve a single service by ID
     */
    public function getServiceById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT service_id, name, name AS service_name, description, price,
                        duration, duration AS duration_minutes, duration AS duration_min,
                        image_url, category, tag, icon_url, is_addon, is_active,
                        (is_active = 1) AS is_visible,
                        sort_order, managed_by_admin_id, created_at, updated_at
                 FROM service
                 WHERE service_id = ?
                 LIMIT 1"
            );
            $stmt->execute([$id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Create a new service (Admin CRUD)
     */
    public function createService(array $data): int
    {
        $name        = trim($data['service_name'] ?? $data['name'] ?? '');
        $description = trim($data['description'] ?? '');
        $duration    = max(1, (int)($data['duration'] ?? $data['duration_minutes'] ?? $data['duration_min'] ?? 30));
        $price       = max(0.0, (float)($data['price'] ?? 0.0));
        $category    = trim($data['category'] ?? '');
        if (empty($category)) {
            // Auto detect from name or default to Manicure
            $lName = strtolower($name);
            if (str_contains($lName, 'pedicure') || str_contains($lName, 'feet') || str_contains($lName, 'soak')) {
                $category = 'Pedicure';
            } elseif (str_contains($lName, 'art') || str_contains($lName, 'removal') || str_contains($lName, 'consult') || str_contains($lName, 'repair') || str_contains($lName, 'extension')) {
                $category = 'Specialty';
            } else {
                $category = 'Manicure';
            }
        }

        $isActive = isset($data['is_visible'])
            ? ($data['is_visible'] ? 1 : 0)
            : (isset($data['is_active']) ? ((int)$data['is_active'] ? 1 : 0) : 1);

        $isAddon   = (int)($data['is_addon'] ?? 0);
        $tag       = !empty($data['tag']) ? trim($data['tag']) : null;
        $adminId   = !empty($data['admin_id']) ? (int)$data['admin_id'] : null;

        // Determine next sort_order
        $sortOrder = (int)($data['sort_order'] ?? 0);
        if ($sortOrder <= 0) {
            $maxOrder = (int)$this->pdo->query("SELECT IFNULL(MAX(sort_order), 0) FROM service")->fetchColumn();
            $sortOrder = $maxOrder + 1;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO service
                (name, description, duration, price, category, tag, is_addon, is_active, sort_order, managed_by_admin_id)
             VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $name,
            $description,
            $duration,
            $price,
            $category,
            $tag,
            $isAddon,
            $isActive,
            $sortOrder,
            $adminId
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update an existing service (Admin CRUD)
     */
    public function updateService(int $id, array $data): bool
    {
        $existing = $this->getServiceById($id);
        if (!$existing) {
            return false;
        }

        $name        = trim($data['service_name'] ?? $data['name'] ?? $existing['name']);
        $description = isset($data['description']) ? trim($data['description']) : $existing['description'];
        $duration    = isset($data['duration']) ? max(1, (int)$data['duration']) : (int)$existing['duration'];
        $price       = isset($data['price']) ? max(0.0, (float)$data['price']) : (float)$existing['price'];
        $category    = !empty($data['category']) ? trim($data['category']) : $existing['category'];

        $isActive = isset($data['is_visible'])
            ? ($data['is_visible'] ? 1 : 0)
            : (isset($data['is_active']) ? ((int)$data['is_active'] ? 1 : 0) : (int)$existing['is_active']);

        $tag = isset($data['tag']) ? trim($data['tag']) : $existing['tag'];

        $stmt = $this->pdo->prepare(
            "UPDATE service
             SET name = ?, description = ?, duration = ?, price = ?,
                 category = ?, tag = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
             WHERE service_id = ?"
        );
        return $stmt->execute([
            $name,
            $description,
            $duration,
            $price,
            $category,
            $tag,
            $isActive,
            $id
        ]);
    }

    /**
     * Toggle visibility (is_active) for a service
     */
    public function toggleVisibility(int $id, ?int $newStatus = null): int
    {
        if ($newStatus === null) {
            $stmt = $this->pdo->prepare("UPDATE service SET is_active = IF(is_active = 1, 0, 1), updated_at = CURRENT_TIMESTAMP WHERE service_id = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE service SET is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE service_id = ?");
            $stmt->execute([$newStatus ? 1 : 0, $id]);
        }

        $row = $this->getServiceById($id);
        return (int)($row['is_active'] ?? 0);
    }

    /**
     * Delete or archive a service with foreign key safety guard
     */
    public function deleteService(int $id): array
    {
        $service = $this->getServiceById($id);
        if (!$service) {
            return ['success' => false, 'message' => 'Service not found.'];
        }

        // Check if referenced by existing appointments
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM appointment WHERE service_id = ?");
            $stmt->execute([$id]);
            $hasAppointments = ((int)$stmt->fetchColumn()) > 0;
        } catch (Exception $e) {
            $hasAppointments = false;
        }

        if ($hasAppointments) {
            // Cannot hard delete due to foreign key integrity; soft delete instead
            $this->pdo->prepare("UPDATE service SET is_active = 0, updated_at = CURRENT_TIMESTAMP WHERE service_id = ?")->execute([$id]);
            return [
                'success'  => true,
                'archived' => true,
                'message'  => 'Service has appointment history so it was hidden/archived from client display instead of deleted.'
            ];
        }

        try {
            $stmt = $this->pdo->prepare("DELETE FROM service WHERE service_id = ?");
            $stmt->execute([$id]);
            return [
                'success'  => true,
                'archived' => false,
                'message'  => 'Service "' . ($service['name'] ?? '') . '" was successfully deleted.'
            ];
        } catch (PDOException $e) {
            // Fallback soft delete
            $this->pdo->prepare("UPDATE service SET is_active = 0 WHERE service_id = ?")->execute([$id]);
            return [
                'success'  => true,
                'archived' => true,
                'message'  => 'Service was archived to preserve database relations.'
            ];
        }
    }
}
