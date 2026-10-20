<?php
/**
 * ContactMessage.php — Contact Message Model
 * Monet Nails MVC Application
 *
 * Manages contact form submissions stored in the contact_messages table.
 */

class ContactMessage
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Save a new contact message.
     * Links to a customer if they are logged in.
     */
    public function save(string $name, string $email, string $subject, string $message, ?int $customerId = null): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO contact_messages (customer_id, name, email, subject, message)
                 VALUES (?, ?, ?, ?, ?)"
            );
            return $stmt->execute([$customerId, $name, $email, $subject, $message]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Fetch all messages (for admin use).
     */
    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT * FROM contact_messages ORDER BY created_at DESC"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Mark a message as read.
     */
    public function markRead(int $id): void
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
            $stmt->execute([$id]);
        } catch (Exception $e) {
            // Silently fail
        }
    }
}
