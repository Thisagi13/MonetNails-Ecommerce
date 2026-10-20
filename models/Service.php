<?php
/**
 * Service.php — Service Model
 * Monet Nails MVC Application
 *
 * Handles database operations for nail bar and salon services
 * stored in the `service` table.
 */

class Service
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieve all services ordered by service_id
     */
    public function getAllServices(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM service ORDER BY service_id ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Retrieve services filtered by category (Manicure, Pedicure, Specialty)
     */
    public function getByCategory(string $category): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM service WHERE category = ? ORDER BY service_id ASC");
            $stmt->execute([$category]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Retrieve all services grouped into categories
     * Returns ['Manicure' => [...], 'Pedicure' => [...], 'Specialty' => [...]]
     */
    public function getAllGroupedByCategory(): array
    {
        $all = $this->getAllServices();
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
            $stmt = $this->pdo->prepare("SELECT * FROM service WHERE service_id = ? LIMIT 1");
            $stmt->execute([$id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res ?: null;
        } catch (Exception $e) {
            return null;
        }
    }
}
