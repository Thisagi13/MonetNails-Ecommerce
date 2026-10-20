<?php
/**
 * Appointment.php — Appointment Model
 * Monet Nails MVC Application
 *
 * Manages appointment records, booking validations, slot availability,
 * client dossiers, and comprehensive admin appointment management.
 */

class Appointment
{
    private PDO $pdo;

    public const ALL_SLOTS = ['10:00', '11:00', '11:30', '12:00', '13:15', '13:30', '14:30', '16:00'];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // =========================================================================
    // CUSTOMER-FACING / BOOKING LOGIC
    // =========================================================================

    /**
     * Checks if a specific date and time slot is already booked.
     */
    public function isSlotTaken(string $date, string $time, int $excludeId = 0): bool
    {
        try {
            $timeValue = (strlen($time) === 5) ? ($time . ':00') : $time;
            $sql = "SELECT COUNT(*) FROM appointment 
                    WHERE appointment_date = ? 
                      AND appointment_time = ? 
                      AND LOWER(status) NOT IN ('cancelled', 'canceled')";
            $params = [$date, $timeValue];

            if ($excludeId > 0) {
                $sql .= " AND appointment_id != ?";
                $params[] = $excludeId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return ((int)$stmt->fetchColumn()) > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Retrieves availability for all standard slots on a given date.
     */
    public function getSlotsForDate(string $date): array
    {
        $booked = [];
        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $stmt = $this->pdo->prepare(
                    "SELECT appointment_time FROM appointment 
                     WHERE appointment_date = ? AND LOWER(status) NOT IN ('cancelled', 'canceled')"
                );
                $stmt->execute([$date]);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $booked[] = substr($row['appointment_time'], 0, 5); // "13:30:00" -> "13:30"
                }
            }
        } catch (Exception $e) {
            $booked = [];
        }

        $slots = [];
        foreach (self::ALL_SLOTS as $slot) {
            $slots[] = [
                'time'      => $slot,
                'label'     => date('g:i A', strtotime($slot)),
                'available' => !in_array($slot, $booked, true)
            ];
        }

        return $slots;
    }

    /**
     * Creates a new customer appointment booking.
     */
    public function createAppointment(array $data): int
    {
        $sql = "INSERT INTO appointment 
                (service_id, customer_id, addon_ids, appointment_date, appointment_time,
                 first_name, last_name, email, client_tier, phone, special_requests, total_price, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

        $stmt = $this->pdo->prepare($sql);
        $timeValue = (strlen($data['appointment_time']) === 5) ? ($data['appointment_time'] . ':00') : $data['appointment_time'];

        $stmt->execute([
            (int)$data['service_id'],
            !empty($data['customer_id']) ? (int)$data['customer_id'] : null,
            !empty($data['addon_ids']) ? $data['addon_ids'] : null,
            $data['appointment_date'],
            $timeValue,
            trim($data['first_name'] ?? ''),
            trim($data['last_name'] ?? ''),
            trim($data['email'] ?? ''),
            !empty($data['client_tier']) ? trim($data['client_tier']) : 'Patron',
            trim($data['phone'] ?? ''),
            trim($data['special_requests'] ?? ''),
            (float)($data['total_price'] ?? 0)
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Retrieve appointments by customer ID
     */
    public function getByCustomerId(int $customerId): array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT a.*, s.name AS service_name, s.duration AS service_duration 
                 FROM appointment a
                 LEFT JOIN service s ON a.service_id = s.service_id
                 WHERE a.customer_id = ?
                 ORDER BY a.appointment_date DESC, a.appointment_time DESC"
            );
            $stmt->execute([$customerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Retrieve appointment by ID
     */
    public function getById(int $appointmentId): ?array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT a.*, s.name AS service_name, s.category AS service_category,
                        s.duration AS service_duration, s.price AS service_price
                 FROM appointment a
                 LEFT JOIN service s ON a.service_id = s.service_id
                 WHERE a.appointment_id = ? LIMIT 1"
            );
            $stmt->execute([$appointmentId]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Update payment status and optional PayHere payment ID after gateway callback.
     */
    public function updatePaymentStatus(int $appointmentId, string $paymentStatus, ?string $payherePaymentId = null): void
    {
        try {
            if ($payherePaymentId !== null) {
                $stmt = $this->pdo->prepare(
                    "UPDATE appointment SET payment_status = ?, payhere_payment_id = ? WHERE appointment_id = ?"
                );
                $stmt->execute([$paymentStatus, $payherePaymentId, $appointmentId]);
            } else {
                $stmt = $this->pdo->prepare(
                    "UPDATE appointment SET payment_status = ? WHERE appointment_id = ?"
                );
                $stmt->execute([$paymentStatus, $appointmentId]);
            }

            if ($paymentStatus === 'completed') {
                $stmt2 = $this->pdo->prepare(
                    "UPDATE appointment SET status = 'confirmed' WHERE appointment_id = ? AND status = 'pending'"
                );
                $stmt2->execute([$appointmentId]);
            }
        } catch (Exception $e) {
            // Silently handled
        }
    }

    // =========================================================================
    // ADMIN — Statistics & Dashboard Overview
    // =========================================================================

    /**
     * Calculates the 4 high-level salon KPIs:
     *   1. Active Bookings (confirmed/booked, >= today)
     *   2. Pending Intake (status = pending)
     *   3. Schedule Overlaps (collisions on same date & time)
     *   4. Chair Utilization percentage
     */
    public function getAdminStats(): array
    {
        try {
            // 1. Active bookings (upcoming confirmed/booked)
            $activeStmt = $this->pdo->query(
                "SELECT COUNT(*) FROM appointment 
                 WHERE status IN ('confirmed', 'booked') AND appointment_date >= CURDATE()"
            );
            $activeCount = (int)$activeStmt->fetchColumn();

            // 2. Pending intake
            $pendingStmt = $this->pdo->query(
                "SELECT COUNT(*) FROM appointment WHERE status = 'pending'"
            );
            $pendingCount = (int)$pendingStmt->fetchColumn();

            // 3. Schedule Overlaps
            $conflictStmt = $this->pdo->query(
                "SELECT COUNT(*) FROM (
                    SELECT appointment_date, appointment_time
                    FROM appointment
                    WHERE status IN ('pending', 'confirmed', 'booked')
                    GROUP BY appointment_date, appointment_time
                    HAVING COUNT(*) > 1
                 ) AS overlaps"
            );
            $conflictCount = (int)$conflictStmt->fetchColumn();

            // 4. Completed this month
            $completedStmt = $this->pdo->query(
                "SELECT COUNT(*) FROM appointment 
                 WHERE status = 'completed' 
                   AND MONTH(appointment_date) = MONTH(CURDATE()) 
                   AND YEAR(appointment_date) = YEAR(CURDATE())"
            );
            $completedMonth = (int)$completedStmt->fetchColumn();

            // Chair utilization calculation based on floor occupancy / monthly completion
            $denom = $completedMonth + $pendingCount + $activeCount;
            if ($completedMonth > 0) {
                $utilPct = (int)round(($completedMonth / max(1, $denom)) * 100);
            } elseif ($denom > 0) {
                $utilPct = (int)round((min(14, $activeCount + $pendingCount) / 14) * 100);
            } else {
                $utilPct = 0;
            }
            $utilPct = min(100, max(0, $utilPct));

            return [
                'active_count'    => $activeCount,
                'pending_count'   => $pendingCount,
                'conflict_count'  => $conflictCount,
                'completed_month' => $completedMonth,
                'util_pct'        => $utilPct
            ];
        } catch (Exception $e) {
            return [
                'active_count'    => 0,
                'pending_count'   => 0,
                'conflict_count'  => 0,
                'completed_month' => 0,
                'util_pct'        => 0
            ];
        }
    }

    // =========================================================================
    // ADMIN — Paginated / Filtered List
    // =========================================================================

    /**
     * Retrieves paginated, filtered appointment list with service and customer details.
     */
    public function getAdminList(
        string $search = '',
        string $category = '',
        string $status = 'all',
        string $timeSlot = 'all',
        int    $page = 1,
        int    $perPage = 10
    ): array {
        $where  = ['1=1'];
        $params = [];

        if ($search !== '') {
            $where[] = '(a.first_name LIKE ? OR a.last_name LIKE ? OR a.email LIKE ? OR a.phone LIKE ? OR s.name LIKE ? OR CONCAT("#APT-", LPAD(a.appointment_id, 4, "0")) LIKE ?)';
            $term = "%$search%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if ($category !== '' && $category !== 'all') {
            $where[] = 'LOWER(s.category) LIKE ?';
            $params[] = '%' . strtolower($category) . '%';
        }

        if ($status !== '' && $status !== 'all') {
            if ($status === 'confirmed') {
                $where[] = "a.status IN ('confirmed', 'booked')";
            } else {
                $where[] = 'a.status = ?';
                $params[] = $status;
            }
        }

        if ($timeSlot !== '' && $timeSlot !== 'all') {
            switch ($timeSlot) {
                case 'morning':
                    $where[] = "a.appointment_time < '12:00:00'";
                    break;
                case 'afternoon':
                    $where[] = "a.appointment_time >= '12:00:00' AND a.appointment_time < '16:00:00'";
                    break;
                case 'evening':
                    $where[] = "a.appointment_time >= '16:00:00'";
                    break;
            }
        }

        $whereClause = implode(' AND ', $where);

        // Count total matching
        $countSql = "SELECT COUNT(*) 
                     FROM appointment a
                     LEFT JOIN service s ON a.service_id = s.service_id
                     LEFT JOIN customer c ON a.customer_id = c.customer_id
                     WHERE $whereClause";
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Paginated rows
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT a.appointment_id, a.customer_id, a.service_id, a.managed_by_admin_id,
                       a.appointment_date, a.appointment_time,
                       a.first_name, a.last_name, a.email, a.phone,
                       a.client_tier, a.special_requests, a.total_price,
                       a.payment_status, a.status, a.created_at,
                       s.name AS service_name, s.category AS category,
                       s.duration AS duration_minutes, s.price AS service_price,
                       c.customer_id AS reg_customer_id
                FROM appointment a
                LEFT JOIN service s ON a.service_id = s.service_id
                LEFT JOIN customer c ON a.customer_id = c.customer_id
                WHERE $whereClause
                ORDER BY a.appointment_date DESC, a.appointment_time DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->pdo->prepare($sql);
        $idx = 1;
        foreach ($params as $p) {
            $stmt->bindValue($idx++, $p);
        }
        $stmt->bindValue($idx++, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue($idx++, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'rows'  => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total
        ];
    }

    // =========================================================================
    // ADMIN — CRUD & Lifecycle Mutations
    // =========================================================================

    /**
     * Admin manual appointment booking (walk-ins, phone reservations).
     */
    public function createAdminAppointment(array $data): int
    {
        $sql = "INSERT INTO appointment 
                (service_id, customer_id, managed_by_admin_id, appointment_date, appointment_time,
                 first_name, last_name, email, client_tier, phone, special_requests, total_price,
                 payment_status, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $timeValue = (strlen($data['appointment_time']) === 5) ? ($data['appointment_time'] . ':00') : $data['appointment_time'];

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            (int)($data['service_id'] ?? 1),
            !empty($data['customer_id']) ? (int)$data['customer_id'] : null,
            !empty($data['managed_by_admin_id']) ? (int)$data['managed_by_admin_id'] : null,
            $data['appointment_date'],
            $timeValue,
            trim($data['first_name'] ?? ''),
            trim($data['last_name'] ?? ''),
            trim($data['email'] ?? ''),
            !empty($data['client_tier']) ? trim($data['client_tier']) : 'Patron',
            trim($data['phone'] ?? ''),
            trim($data['special_requests'] ?? ''),
            (float)($data['total_price'] ?? 0),
            $data['payment_status'] ?? 'pending',
            $data['status'] ?? 'confirmed'
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Full update of an existing appointment.
     */
    public function updateAppointment(int $id, array $data): bool
    {
        $timeValue = (strlen($data['appointment_time']) === 5) ? ($data['appointment_time'] . ':00') : $data['appointment_time'];

        $sql = "UPDATE appointment SET
                    service_id          = ?,
                    appointment_date    = ?,
                    appointment_time    = ?,
                    first_name          = ?,
                    last_name           = ?,
                    email               = ?,
                    client_tier         = ?,
                    phone               = ?,
                    special_requests    = ?,
                    total_price         = ?,
                    status              = ?,
                    managed_by_admin_id = ?
                WHERE appointment_id = ?";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            (int)($data['service_id'] ?? 1),
            $data['appointment_date'],
            $timeValue,
            trim($data['first_name'] ?? ''),
            trim($data['last_name'] ?? ''),
            trim($data['email'] ?? ''),
            trim($data['client_tier'] ?? 'Patron'),
            trim($data['phone'] ?? ''),
            trim($data['special_requests'] ?? ''),
            (float)($data['total_price'] ?? 0),
            $data['status'] ?? 'pending',
            !empty($data['managed_by_admin_id']) ? (int)$data['managed_by_admin_id'] : null,
            $id
        ]);
    }

    /**
     * Quick status transition (confirmed, completed, cancelled, pending).
     */
    public function updateStatus(int $id, string $status): bool
    {
        $validStatuses = ['pending', 'confirmed', 'booked', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses, true)) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE appointment SET status = ? WHERE appointment_id = ?");
        return $stmt->execute([$status, $id]);
    }

    /**
     * Reschedules an appointment to a new date and time slot.
     */
    public function reschedule(int $id, string $date, string $time): bool
    {
        $timeValue = (strlen($time) === 5) ? ($time . ':00') : $time;
        $stmt = $this->pdo->prepare(
            "UPDATE appointment SET appointment_date = ?, appointment_time = ? WHERE appointment_id = ?"
        );
        return $stmt->execute([$date, $timeValue, $id]);
    }

    /**
     * Delete an appointment record.
     */
    public function deleteAppointment(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM appointment WHERE appointment_id = ?");
        return $stmt->execute([$id]);
    }
}
