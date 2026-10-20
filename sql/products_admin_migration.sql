-- ============================================================================
-- products_admin_migration.sql
-- Monet Nails — Admin Product Management & Schema Synchronization
-- ============================================================================

USE monet_nails_db;

-- 1) Ensure `sku` column exists in `products` table
SET @col := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'sku');
SET @sql := IF(@col = 0,
    'ALTER TABLE products ADD COLUMN sku VARCHAR(50) NULL DEFAULT NULL AFTER product_id',
    'SELECT "sku already exists" AS msg');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- Ensure `shape` and `finish` exist
SET @col := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'shape');
SET @sql := IF(@col = 0,
    'ALTER TABLE products ADD COLUMN shape VARCHAR(100) NULL DEFAULT NULL AFTER image_url',
    'SELECT "shape already exists" AS msg');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @col := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'finish');
SET @sql := IF(@col = 0,
    'ALTER TABLE products ADD COLUMN finish VARCHAR(100) NULL DEFAULT NULL AFTER shape',
    'SELECT "finish already exists" AS msg');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- Back-fill auto-SKUs for existing rows that have none
UPDATE products SET sku = CONCAT('#MN-', LPAD(product_id, 3, '0')) WHERE sku IS NULL OR sku = '';

-- 2) Ensure `client_tier` exists in `appointment` table
SET @col := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'appointment' AND COLUMN_NAME = 'client_tier');
SET @sql := IF(@col = 0,
    'ALTER TABLE appointment ADD COLUMN client_tier VARCHAR(50) NULL DEFAULT NULL AFTER email',
    'SELECT "client_tier already exists" AS msg');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- Update `appointments` view to include `client_tier`
CREATE OR REPLACE VIEW appointments AS 
SELECT 
    appointment_id,
    customer_id,
    service_id,
    managed_by_admin_id,
    addon_ids,
    appointment_date,
    appointment_time,
    first_name,
    last_name,
    email,
    client_tier,
    phone,
    special_requests,
    total_price,
    payment_status,
    payhere_payment_id,
    status,
    created_at
FROM appointment;

-- 3) Indexes for performance
CREATE INDEX IF NOT EXISTS idx_products_is_active ON products (is_active);
CREATE INDEX IF NOT EXISTS idx_products_stock     ON products (stock_quantity);
CREATE INDEX IF NOT EXISTS idx_products_category  ON products (category_id);
