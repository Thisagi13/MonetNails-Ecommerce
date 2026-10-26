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
     * Fetch all profile data for a customer.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT customer_id, f_name, l_name, email, password, phone_no, address, city, postal_code, profile_image, is_verified, created_at, updated_at
             FROM Customer WHERE customer_id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Check if an email is already used by another customer.
     */
    public function emailExistsExcept(string $email, int $customerId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM Customer WHERE email = ? AND customer_id <> ? LIMIT 1'
        );
        $stmt->execute([$email, $customerId]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Update customer personal details and optionally profile image.
     */
    public function updateProfile(
        int $customerId,
        string $f_name,
        string $l_name,
        string $email,
        string $phone = '',
        string $address = '',
        ?string $profile_image = null
    ): bool {
        if ($profile_image !== null && $profile_image !== '') {
            $sql = 'UPDATE Customer SET
                        f_name = ?,
                        l_name = ?,
                        email = ?,
                        phone_no = ?,
                        address = ?,
                        profile_image = ?
                    WHERE customer_id = ?';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$f_name, $l_name, $email, $phone, $address, $profile_image, $customerId]);
        } else {
            $sql = 'UPDATE Customer SET
                        f_name = ?,
                        l_name = ?,
                        email = ?,
                        phone_no = ?,
                        address = ?
                    WHERE customer_id = ?';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$f_name, $l_name, $email, $phone, $address, $customerId]);
        }
    }

    /**
     * Update password hash for a customer.
     */
    public function updatePassword(int $customerId, string $passwordHash): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE Customer SET password = ? WHERE customer_id = ?'
        );
        return $stmt->execute([$passwordHash, $customerId]);
    }

    /**
     * Update profile image path only.
     */
    public function updateProfileImage(int $customerId, string $imagePath): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE Customer SET profile_image = ? WHERE customer_id = ?'
        );
        return $stmt->execute([$imagePath, $customerId]);
    }
}

