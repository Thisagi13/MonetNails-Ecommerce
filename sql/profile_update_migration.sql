-- ============================================================================
-- profile_update_migration.sql — Monet Nails Customer Profile Migration
-- ============================================================================

-- Ensure profile_image exists on Customer table
SET @exist_col = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'Customer'
      AND COLUMN_NAME = 'profile_image'
);

SET @stmt = IF(
    @exist_col = 0,
    'ALTER TABLE `Customer` ADD COLUMN `profile_image` VARCHAR(255) NULL AFTER `address`',
    'SELECT "Customer.profile_image already exists"'
);
PREPARE exec_sql FROM @stmt;
EXECUTE exec_sql;
DEALLOCATE PREPARE exec_sql;

-- Ensure phone_no & address exist on Customer table
SET @exist_phone = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'Customer'
      AND COLUMN_NAME = 'phone_no'
);

SET @stmt2 = IF(
    @exist_phone = 0,
    'ALTER TABLE `Customer` ADD COLUMN `phone_no` VARCHAR(20) NULL AFTER `password`',
    'SELECT "Customer.phone_no already exists"'
);
PREPARE exec_sql2 FROM @stmt2;
EXECUTE exec_sql2;
DEALLOCATE PREPARE exec_sql2;

SET @exist_addr = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'Customer'
      AND COLUMN_NAME = 'address'
);

SET @stmt3 = IF(
    @exist_addr = 0,
    'ALTER TABLE `Customer` ADD COLUMN `address` VARCHAR(255) NULL AFTER `phone_no`',
    'SELECT "Customer.address already exists"'
);
PREPARE exec_sql3 FROM @stmt3;
EXECUTE exec_sql3;
DEALLOCATE PREPARE exec_sql3;
