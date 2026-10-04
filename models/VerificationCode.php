<?php
/**
 * VerificationCode.php — Model
 * ----------------------------
 * All DB operations for email-verification codes.
 *
 * MVC Layer : Model
 * DB table  : verification_codes
 */
class VerificationCode
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // ----------------------------------------------------------------
    // Create a new 6-digit code (valid 10 minutes) for $customerId.
    // Returns the generated code string on success, false on failure.
    // ----------------------------------------------------------------
    public function create(int $customerId): string|false
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $stmt = $this->db->prepare(
            'INSERT INTO verification_codes (customer_id, code, expires_at)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))'
        );
        $ok = $stmt->execute([$customerId, $code]);
        return $ok ? $code : false;
    }

    // ----------------------------------------------------------------
    // Check whether $code is a valid (unused, not expired) code for
    // $customerId.  Returns the verify_id on match, false otherwise.
    // ----------------------------------------------------------------
    public function verify(int $customerId, string $code): int|false
    {
        $stmt = $this->db->prepare(
            'SELECT verify_id FROM verification_codes
              WHERE customer_id = ?
                AND code        = ?
                AND used_at IS NULL
                AND expires_at  > NOW()
              ORDER BY verify_id DESC
              LIMIT 1'
        );
        $stmt->execute([$customerId, $code]);
        $row = $stmt->fetch();
        return $row ? (int) $row['verify_id'] : false;
    }

    // ----------------------------------------------------------------
    // Mark a code as used (one-time only).
    // ----------------------------------------------------------------
    public function markUsed(int $verifyId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE verification_codes SET used_at = NOW() WHERE verify_id = ?'
        );
        $stmt->execute([$verifyId]);
    }

    // ----------------------------------------------------------------
    // Check whether there is already a fresh (unused, not expired)
    // code for $customerId — so we don't create duplicates on reload.
    // ----------------------------------------------------------------
    public function hasFreshCode(int $customerId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM verification_codes
              WHERE customer_id = ?
                AND used_at IS NULL
                AND expires_at  > NOW()
              LIMIT 1'
        );
        $stmt->execute([$customerId]);
        return (bool) $stmt->fetchColumn();
    }
}
