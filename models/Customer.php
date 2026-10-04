<?php
/**
 * Customer.php — Model
 * ---------------------
 * Handles all DB operations for the Customer entity.
 * Uses PDO (from config/db.php).
 *
 * MVC Layer : Model
 * DB table  : customers  (lowercase, per final schema)
 */
class Customer
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Find a customer by email address.
     * Returns the row as an associative array, or false if not found.
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT customer_id, f_name, l_name, email, password, profile_image, is_verified
             FROM Customer WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Check whether an email already exists in the Customer table.
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM Customer WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Insert a new customer row.
     * Returns the new customer_id on success, or false on failure.
     */
    public function create(string $f_name, string $l_name, string $email,
                           string $password_hash, string $phone = ''): int|false
    {
        $stmt = $this->db->prepare(
            'INSERT INTO Customer (f_name, l_name, email, password, phone_no)
             VALUES (?, ?, ?, ?, ?)'
        );
        $ok = $stmt->execute([$f_name, $l_name, $email, $password_hash, $phone]);
        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    /**
     * Fetch minimal session data for a newly created / logged-in customer.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT customer_id, f_name, l_name, email, profile_image
             FROM Customer WHERE customer_id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
