-- ============================================================================
-- appointment_history_migration.sql
-- Monet Nails — Appointment History & Service Schema Update
-- ============================================================================

USE monet_nails_db;

-- 1. Service: image_url & category
ALTER TABLE service
    ADD COLUMN IF NOT EXISTS image_url VARCHAR(255) NULL AFTER duration;

-- 2. Testimonial table
CREATE TABLE IF NOT EXISTS Testimonial (
    testimonial_id  INT AUTO_INCREMENT PRIMARY KEY,
    customer_id     INT NULL,
    customer_name   VARCHAR(100) NOT NULL,
    rating          INT NOT NULL DEFAULT 5,
    comment         TEXT NOT NULL,
    is_approved     TINYINT(1) NOT NULL DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_testimonial_customer
        FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3. Appointment table columns for History page
ALTER TABLE appointment
    ADD COLUMN IF NOT EXISTS reference_no     VARCHAR(30)   NULL AFTER appointment_id,
    ADD COLUMN IF NOT EXISTS stylist_admin_id INT           NULL AFTER managed_by_admin_id,
    ADD COLUMN IF NOT EXISTS styling_notes    VARCHAR(255)  NULL AFTER status,
    ADD COLUMN IF NOT EXISTS total_amount     DECIMAL(10,2) NULL AFTER styling_notes;

-- Modify payment_status to support history states
ALTER TABLE appointment
    MODIFY COLUMN payment_status VARCHAR(30) NOT NULL DEFAULT 'unsettled';

-- Backfill reference_no & total_amount for existing appointments
UPDATE appointment
SET reference_no = CONCAT('#MN-APT-', LPAD(appointment_id, 5, '0'))
WHERE reference_no IS NULL OR reference_no = '';

UPDATE appointment
SET total_amount = total_price
WHERE (total_amount IS NULL OR total_amount = 0) AND total_price IS NOT NULL;
