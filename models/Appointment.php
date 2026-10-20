<?php
/**
 * Appointment.php — Appointment Model
 * Monet Nails MVC Application
 *
 * Manages appointment records, booking validations, and slot availability.
 */

class Appointment
{
    private PDO $pdo;

    public const ALL_SLOTS = ['10:00', '11:00', '12:00', '13:30', '14:30', '16:00'];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Checks if a specific date and time slot is already booked.
     */
    public function isSlotTaken(string $date, string $time): bool
    {
        try {
            $timeValue = (strlen($time) === 5) ? ($time . ':00') : $time;
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM appointment 
                 WHERE appointment_date = ? AND appointment_time = ? AND LOWER(status) NOT IN ('cancelled', 'canceled')"
            );
            $stmt->execute([$date, $timeValue]);
            return ((int)$stmt->fetchColumn()) > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Retrieves availability for all standard slots on a given date.
     * Returns an array of: [['time' => '10:00', 'label' => '10:00 AM', 'available' => true], ...]
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
     * Creates a new appointment booking.
     * Supports both registered client bookings and guest bookings.
     */
    public function createAppointment(array $data): int
    {
        $sql = "INSERT INTO appointment 
                (service_id, customer_id, addon_ids, appointment_date, appointment_time,
                 first_name, last_name, email, phone, special_requests, total_price, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

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
                "SELECT a.*, s.name AS service_name, s.duration AS service_duration 
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

            // Also update appointment status if payment completed
            if ($paymentStatus === 'completed') {
                $stmt2 = $this->pdo->prepare(
                    "UPDATE appointment SET status = 'booked' WHERE appointment_id = ? AND status = 'pending'"
                );
                $stmt2->execute([$appointmentId]);
            }
        } catch (Exception $e) {
            // Log silently
        }
    }
}
