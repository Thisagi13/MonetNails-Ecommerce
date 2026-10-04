-- ============================================================
--  verification_schema.sql
--  Run this once in phpMyAdmin or MySQL CLI to add the
--  email-verification feature to monet_nails_db.
-- ============================================================

USE `monet_nails_db`;

-- 1) Mark verified customers
ALTER TABLE `Customer`
    ADD COLUMN IF NOT EXISTS `is_verified` TINYINT(1) NOT NULL DEFAULT 0;

-- 2) Table that stores 6-digit codes (10-min TTL, single-use)
CREATE TABLE IF NOT EXISTS `verification_codes` (
    `verify_id`   INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `code`        VARCHAR(6) NOT NULL,
    `expires_at`  DATETIME NOT NULL,
    `used_at`     DATETIME NULL DEFAULT NULL,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_verify_customer`
        FOREIGN KEY (`customer_id`) REFERENCES `Customer`(`customer_id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
