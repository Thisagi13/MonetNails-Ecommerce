-- ============================================================================
-- order_history_migration.sql
-- Monet Nails — Customer Order History Feature
--
-- Adds a `reference_no` column to orders (for friendly display ref like #MN-ORD-XXXXX)
-- and ensures all columns required by the order history view exist.
-- Safe to run multiple times (uses IF NOT EXISTS guards).
-- ============================================================================

USE monet_nails_db;

-- 1. Add reference_no to orders (human-readable order reference)
SET @dbname = DATABASE();
SET @tablename = 'orders';
SET @columnname = 'reference_no';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME   = @tablename
      AND COLUMN_NAME  = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE orders ADD COLUMN reference_no VARCHAR(30) NULL AFTER order_id'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Backfill reference_no for all existing orders that don't have one
UPDATE orders
SET reference_no = CONCAT('#MN-ORD-', LPAD(order_id, 5, '0'))
WHERE reference_no IS NULL OR reference_no = '';

-- 3. Ensure shipping_address column exists (some schemas use street_address + city separately)
SET @columnname = 'shipping_address';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME   = @tablename
      AND COLUMN_NAME  = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE orders ADD COLUMN shipping_address TEXT NULL AFTER postal_code'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 4. Backfill shipping_address from street_address + city + postal_code
UPDATE orders
SET shipping_address = TRIM(CONCAT_WS(', ',
    NULLIF(TRIM(COALESCE(street_address, '')), ''),
    NULLIF(TRIM(COALESCE(city, '')), ''),
    NULLIF(TRIM(COALESCE(postal_code, '')), '')
))
WHERE (shipping_address IS NULL OR shipping_address = '')
  AND (street_address IS NOT NULL OR city IS NOT NULL);

-- 5. Ensure variant fields exist on order_items for display
SET @tablename = 'order_items';

SET @columnname = 'shape';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE order_items ADD COLUMN shape VARCHAR(60) NULL AFTER size_preset'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @columnname = 'finish';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE order_items ADD COLUMN finish VARCHAR(60) NULL AFTER shape'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 6. Add index on orders.customer_id for fast per-customer lookups (if not already indexed)
SET @indexname = 'idx_orders_customer_id';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'orders' AND INDEX_NAME = @indexname) > 0,
  'SELECT 1',
  'ALTER TABLE orders ADD INDEX idx_orders_customer_id (customer_id)'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Done
SELECT 'order_history_migration.sql applied successfully.' AS result;
