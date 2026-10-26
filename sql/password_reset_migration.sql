-- ============================================================
-- password_reset_migration.sql
-- Password Reset Tokens Table for Monet Nails
-- ============================================================

USE `monet_nails_db`;

CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(100) NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `user_type` ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    `expires_at` DATETIME NOT NULL,
    `used_at` DATETIME NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_token` (`token`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
