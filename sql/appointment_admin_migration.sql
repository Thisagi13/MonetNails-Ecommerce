-- ============================================================================
-- appointment_admin_migration.sql
-- Monet Nails — Admin Appointment & Salon Schedule Feature Migration
-- ============================================================================

USE monet_nails_db;

-- 1) Ensure client_tier exists on the appointment base table
SET @col := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'appointment' AND COLUMN_NAME = 'client_tier');
SET @sql := IF(@col = 0,
    'ALTER TABLE appointment ADD COLUMN client_tier VARCHAR(50) NULL DEFAULT NULL AFTER email',
    'SELECT "client_tier already exists" AS msg');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 2) Update appointments VIEW to project client_tier
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

-- 3) Ensure helpful indexes for salon schedule queries
CREATE INDEX IF NOT EXISTS idx_appointment_date_time ON appointment (appointment_date, appointment_time);
CREATE INDEX IF NOT EXISTS idx_appointment_status    ON appointment (status);
CREATE INDEX IF NOT EXISTS idx_appointment_customer  ON appointment (customer_id);
CREATE INDEX IF NOT EXISTS idx_appointment_service   ON appointment (service_id);
