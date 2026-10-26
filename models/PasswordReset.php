<?php
/**
 * PasswordReset.php — Model
 * -------------------------
 * Handles all database operations for secure password reset tokens.
 *
 * MVC Layer : Model
 * DB table  : password_resets
 */
class PasswordReset
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Create a new secure cryptographic reset token (valid for 1 hour).
     * Invalidates any previous pending tokens for this email first.
     */
    public function createToken(string $email, string $userType = 'customer'): string|false
    {
        try {
            // Invalidate any previously issued unused tokens for this email
            $invalidateStmt = $this->db->prepare(
                'UPDATE password_resets SET used_at = NOW() WHERE email = ? AND used_at IS NULL'
            );
            $invalidateStmt->execute([$email]);

            // Generate 64-character cryptographically secure token
            $token = bin2hex(random_bytes(32));

            $stmt = $this->db->prepare(
                'INSERT INTO password_resets (email, token, user_type, expires_at)
                 VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))'
            );
            $success = $stmt->execute([$email, $token, $userType]);

            return $success ? $token : false;
        } catch (\Exception $e) {
            error_log('PasswordReset::createToken Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Look up an active, valid (unused and non-expired) token.
     */
    public function findByToken(string $token): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT id, email, token, user_type, expires_at, created_at
             FROM password_resets
             WHERE token = ?
               AND used_at IS NULL
               AND expires_at > NOW()
             ORDER BY id DESC
             LIMIT 1'
        );
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    /**
     * Check if a token exists but has expired or already been used.
     */
    public function checkTokenStatus(string $token): string
    {
        $stmt = $this->db->prepare(
            'SELECT id, used_at, expires_at
             FROM password_resets
             WHERE token = ?
             ORDER BY id DESC
             LIMIT 1'
        );
        $stmt->execute([$token]);
        $row = $stmt->fetch();

        if (!$row) {
            return 'not_found';
        }

        if ($row['used_at'] !== null) {
            return 'used';
        }

        if (strtotime($row['expires_at']) <= time()) {
            return 'expired';
        }

        return 'valid';
    }

    /**
     * Mark a token as used.
     */
    public function markUsed(string $token): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE password_resets SET used_at = NOW() WHERE token = ?'
        );
        return $stmt->execute([$token]);
    }
}
