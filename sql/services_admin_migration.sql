-- =======================================================================
-- sql/services_admin_migration.sql
-- Monet Nails MVC - Services Admin Feature & Schema Update
-- =======================================================================

-- 1. Insert services from the feature specification if missing
INSERT INTO `service` (`name`, `category`, `description`, `price`, `duration`, `is_addon`, `is_active`, `sort_order`, `tag`)
SELECT 'Signature Manicure', 'Manicure', 'Nail shaping, cuticle care & a classic polish finish.', 45.00, 45, 0, 1, 1, 'Essential'
WHERE NOT EXISTS (SELECT 1 FROM `service` WHERE LOWER(`name`) = 'signature manicure');

INSERT INTO `service` (`name`, `category`, `description`, `price`, `duration`, `is_addon`, `is_active`, `sort_order`, `tag`)
SELECT 'Gel Manicure', 'Manicure', 'A glossy, long-lasting gel finish for everyday wear.', 60.00, 60, 0, 1, 2, 'Long-lasting'
WHERE NOT EXISTS (SELECT 1 FROM `service` WHERE LOWER(`name`) = 'gel manicure');

INSERT INTO `service` (`name`, `category`, `description`, `price`, `duration`, `is_addon`, `is_active`, `sort_order`, `tag`)
SELECT 'Spa Pedicure', 'Pedicure', 'A soothing soak, exfoliation & polished toes.', 65.00, 60, 0, 1, 3, 'Relaxing'
WHERE NOT EXISTS (SELECT 1 FROM `service` WHERE LOWER(`name`) = 'spa pedicure');

INSERT INTO `service` (`name`, `category`, `description`, `price`, `duration`, `is_addon`, `is_active`, `sort_order`, `tag`)
SELECT 'Nail Art Session', 'Specialty', 'Hand-painted details, thoughtfully made for you.', 25.00, 30, 0, 1, 4, 'Bespoke'
WHERE NOT EXISTS (SELECT 1 FROM `service` WHERE LOWER(`name`) = 'nail art session');

INSERT INTO `service` (`name`, `category`, `description`, `price`, `duration`, `is_addon`, `is_active`, `sort_order`, `tag`)
SELECT 'Gel Removal', 'Specialty', 'Gentle gel removal & nourishing nail care.', 15.00, 20, 0, 1, 5, 'Care'
WHERE NOT EXISTS (SELECT 1 FROM `service` WHERE LOWER(`name`) = 'gel removal');

INSERT INTO `service` (`name`, `category`, `description`, `price`, `duration`, `is_addon`, `is_active`, `sort_order`, `tag`)
SELECT 'Bridal Nail Consultation', 'Specialty', 'A personal consultation for your wedding-day nails.', 30.00, 30, 0, 0, 6, 'Consultation'
WHERE NOT EXISTS (SELECT 1 FROM `service` WHERE LOWER(`name`) = 'bridal nail consultation');

-- 2. Ensure the VIEW `services` is up to date and aliases match
CREATE OR REPLACE VIEW `services` AS
SELECT
    `service_id`,
    `category`,
    `name` AS `service_name`,
    `name`,
    `description`,
    `price`,
    `duration` AS `duration_minutes`,
    `duration`,
    `managed_by_admin_id`,
    `is_addon`,
    `is_active`,
    `sort_order`,
    `tag`,
    `icon_url`,
    `created_at`,
    `updated_at`
FROM `service`;
