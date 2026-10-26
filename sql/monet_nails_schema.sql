-- customer-- ============================================================================
-- Monet Nails Bar - E-Commerce & Appointment Booking System
-- Initial Database Schema (MySQL)
-- Derived from ERD: Customer, Admin, Product, Service, Order, Order_Item,
--                    Payment, Appointment, Cart_Item
-- ============================================================================

CREATE DATABASE IF NOT EXISTS monet_nails_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE monet_nails_db;

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- Drop tables (child -> parent order) for clean re-runs during development
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS Cart_Item;
DROP TABLE IF EXISTS Payment;
DROP TABLE IF EXISTS Order_Item;
DROP TABLE IF EXISTS `Order`;
DROP TABLE IF EXISTS Appointment;
DROP TABLE IF EXISTS Service;
DROP TABLE IF EXISTS Product;
DROP TABLE IF EXISTS Admin;
DROP TABLE IF EXISTS Customer;

SET FOREIGN_KEY_CHECKS = 1;


CREATE TABLE Customer (
    customer_id     INT AUTO_INCREMENT PRIMARY KEY,
    f_name          VARCHAR(50)  NOT NULL,
    l_name          VARCHAR(50)  NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,           
    phone_no        VARCHAR(20),
    address         VARCHAR(255),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================================
-- 2. ADMIN
-- Assumption: Admin manages orders, appointments, and services.
-- ============================================================================
CREATE TABLE Admin (
    admin_id        INT AUTO_INCREMENT PRIMARY KEY,
    f_name          VARCHAR(50)  NOT NULL,
    l_name          VARCHAR(50)  NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,           -- store hashed password only
    phone_no        VARCHAR(20),
    address         VARCHAR(255),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================================
-- 3. PRODUCT
-- Assumption: Product can appear in 0..* order items and 0..* cart items.
-- ============================================================================
CREATE TABLE Product (
    product_id      INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)   NOT NULL,
    description     TEXT,
    price           DECIMAL(10,2)  NOT NULL CHECK (price >= 0),
    stock_quantity  INT            NOT NULL DEFAULT 0 CHECK (stock_quantity >= 0),
    image_url       VARCHAR(255),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================================
-- 4. SERVICE
-- Assumption: Service can be booked in 0..* appointments.
--             Admin can update/remove services (managed_by_admin_id optional).
-- ============================================================================
CREATE TABLE Service (
    service_id          INT AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(100)  NOT NULL,
    description         TEXT,
    price               DECIMAL(10,2) NOT NULL CHECK (price >= 0),
    duration            INT           NOT NULL COMMENT 'duration in minutes',
    managed_by_admin_id INT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_service_admin
        FOREIGN KEY (managed_by_admin_id) REFERENCES Admin(admin_id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 5. ORDER
-- Assumption: Customer places 0..* orders, order belongs to exactly 1 customer.
--             Admin manages 0..* orders, one order managed by 0..1 admin.
-- ============================================================================
CREATE TABLE `Order` (
    order_id            INT AUTO_INCREMENT PRIMARY KEY,
    customer_id         INT NOT NULL,
    managed_by_admin_id INT NULL,
    order_date          DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount        DECIMAL(10,2) NOT NULL DEFAULT 0 CHECK (total_amount >= 0),
    tax                 DECIMAL(10,2) NOT NULL DEFAULT 0 CHECK (tax >= 0),
    shipping_fee        DECIMAL(10,2) NOT NULL DEFAULT 0 CHECK (shipping_fee >= 0),
    shipping_address    VARCHAR(255) NOT NULL,
    status              ENUM('pending','processing','shipped','delivered','cancelled')
                            NOT NULL DEFAULT 'pending',
    CONSTRAINT fk_order_customer
        FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_order_admin
        FOREIGN KEY (managed_by_admin_id) REFERENCES Admin(admin_id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 6. ORDER_ITEM  (weak entity)
-- Assumption: One order has 1..* order items; each item belongs to exactly
--             1 order and references exactly 1 product.
-- ============================================================================
CREATE TABLE Order_Item (
    order_item_id   INT AUTO_INCREMENT PRIMARY KEY,
    order_id        INT NOT NULL,
    product_id      INT NOT NULL,
    quantity        INT NOT NULL CHECK (quantity > 0),
    subtotal        DECIMAL(10,2) NOT NULL CHECK (subtotal >= 0),
    CONSTRAINT fk_orderitem_order
        FOREIGN KEY (order_id) REFERENCES `Order`(order_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_orderitem_product
        FOREIGN KEY (product_id) REFERENCES Product(product_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 7. PAYMENT
-- Assumption: One order has 0..1 payment (initially pending); one payment
--             applies to exactly 1 order -> enforced via UNIQUE(order_id).
-- ============================================================================
CREATE TABLE Payment (
    payment_id      INT AUTO_INCREMENT PRIMARY KEY,
    order_id        INT NOT NULL UNIQUE,
    amount          DECIMAL(10,2) NOT NULL CHECK (amount >= 0),
    payment_method  ENUM('credit_card','debit_card','paypal','stripe','cash')
                        NOT NULL,
    transaction_id  VARCHAR(100),
    payment_status  ENUM('pending','completed','failed','refunded')
                        NOT NULL DEFAULT 'pending',
    payment_date    DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payment_order
        FOREIGN KEY (order_id) REFERENCES `Order`(order_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 8. APPOINTMENT
-- Assumption: One customer books 0..* appointments, appointment belongs to
--             exactly 1 customer and is for exactly 1 service.
--             Admin manages 0..* appointments, one appointment managed by 0..1 admin.
-- ============================================================================
CREATE TABLE Appointment (
    appointment_id      INT AUTO_INCREMENT PRIMARY KEY,
    customer_id         INT NOT NULL,
    service_id          INT NOT NULL,
    managed_by_admin_id INT NULL,
    appointment_date    DATE NOT NULL,
    appointment_time    TIME NOT NULL,
    status              ENUM('booked','confirmed','completed','cancelled')
                            NOT NULL DEFAULT 'booked',
    CONSTRAINT fk_appointment_customer
        FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_appointment_service
        FOREIGN KEY (service_id) REFERENCES Service(service_id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_appointment_admin
        FOREIGN KEY (managed_by_admin_id) REFERENCES Admin(admin_id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 9. CART_ITEM
-- Assumption: One user has 0..* cart items at a given time; each cart item
--             belongs to exactly 1 user and references exactly 1 product.
-- ============================================================================
CREATE TABLE Cart_Item (
    cart_item_id    INT AUTO_INCREMENT PRIMARY KEY,
    customer_id     INT NOT NULL,
    product_id      INT NOT NULL,
    quantity        INT NOT NULL CHECK (quantity > 0),
    date_added      DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cartitem_customer
        FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_cartitem_product
        FOREIGN KEY (product_id) REFERENCES Product(product_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    -- Prevents duplicate rows for the same product in a customer's active cart
    CONSTRAINT uq_cart_customer_product UNIQUE (customer_id, product_id)
) ENGINE=InnoDB;

-- ============================================================================
-- INDEXES (in addition to the ones auto-created for PK / UNIQUE / FK columns)
-- ============================================================================
CREATE INDEX idx_order_status          ON `Order` (status);
CREATE INDEX idx_order_date            ON `Order` (order_date);
CREATE INDEX idx_appointment_date_time ON Appointment (appointment_date, appointment_time);
CREATE INDEX idx_appointment_status    ON Appointment (status);
CREATE INDEX idx_payment_status        ON Payment (payment_status);
CREATE INDEX idx_product_name          ON Product (name);
CREATE INDEX idx_service_name          ON Service (name);

-- ============================================================================
-- End of initial schema
-- ============================================================================

ALTER TABLE Customer ADD COLUMN profile_image VARCHAR(255) NULL;

ALTER TABLE Service
    ADD COLUMN category ENUM('Manicure', 'Pedicure', 'Specialty')
        NOT NULL DEFAULT 'Manicure' AFTER duration,
    ADD COLUMN tag VARCHAR(50) NULL DEFAULT NULL AFTER category,
    ADD COLUMN icon_url VARCHAR(255) NULL DEFAULT NULL AFTER tag;

-- ----------------------------------------------------------------------------
-- 2. Sample data so the page looks exactly like the design.
--    Skip this part if you already inserted your own services
--    (then just fill in category / tag / icon_url for those rows instead).
--    Specialty rows use price 0 -> the page shows "Inquire for pricing".
-- ----------------------------------------------------------------------------
INSERT INTO Service (name, description, price, duration, category, tag, icon_url) VALUES
('Classic',
 'Meticulous cuticle care, shaping, light massage, and finishing with a premium lacquer of your choice.',
 3500.00, 45, 'Manicure', 'Essential', NULL),
('Gel',
 'Our classic manicure elevated with high-gloss, durable gel polish cured under LED for a flawless finish.',
 4500.00, 60, 'Manicure', 'Long-lasting', NULL),
('Monet Signature Art',
 'A personalized canvas. Includes intricate hand-painted designs, line art, or specialized textures curated to your style.',
 5000.00, 90, 'Manicure', 'Bespoke', NULL),
('Botanical Spa',
 'A restorative soak infused with seasonal botanicals, followed by gentle exfoliation, massage, and polish.',
 5000.00, 60, 'Pedicure', 'Relaxing', NULL),
('Luxury Jelly Soak',
 'The ultimate hydration experience. Warm aloe-vera jelly soothes joints and softens skin before full pedicure detailing.',
 5200.00, 75, 'Pedicure', 'Indulgent', NULL),
('Express Refresh',
 'A swift, meticulous tidy-up including shaping, light cuticle care, and fresh polish application.',
 4500.00, 30, 'Pedicure', NULL, NULL),
('Hard Gel Overlays',
 'Adds a layer of protective strength to natural nails, promoting healthy growth without the use of tips.',
 0.00, 45, 'Specialty', NULL, 'images/icon-82.svg'),
('Acrylic Extensions',
 'Sculpted extensions tailored to your desired length and shape, offering maximum durability.',
 0.00, 75, 'Specialty', NULL, 'images/icon-91.svg'),
('Nail Repair',
 'Precision repair using silk wraps or builder gel to restore torn or broken nails seamlessly.',
 0.00, 45, 'Specialty', NULL, 'images/icon-100.svg');

-- ----------------------------------------------------------------------------
-- 3. Helpful index for the category filter the page uses
-- ----------------------------------------------------------------------------
CREATE INDEX idx_service_category ON Service (category);

-- ------------------------------------------------------------
-- TABLE: categories
-- Needed by the category pills and the Filter pop-up.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL
);

-- ------------------------------------------------------------
-- TABLE: products
-- Needed by the product cards (image, name, price) and by the
-- product count numbers shown in the Filter pop-up.
-- The category_id column links each product to one category.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    product_id   INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(150) NOT NULL,
    price        DECIMAL(10,2) NOT NULL,
    image        VARCHAR(255) NOT NULL DEFAULT '',
    category_id  INT NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- ------------------------------------------------------------
-- SAMPLE DATA
-- ------------------------------------------------------------
INSERT INTO categories (category_name) VALUES
('French Tips'),
('3D & Embellished'),
('Seasonal & Themed'),
('Bridal');

INSERT INTO products (product_name, price, image, category_id) VALUES
('Signature Press On Set', 45.00, 'images/node-32.png',               2),
('Azure Hearts',           35.00, 'images/node-48.png',               3),
('Soft Blush Collection',  40.00, 'images/node-64.png',               4),
('Pink Cherry Delight',    45.00, 'images/pink-ribbon-delight-80.png',1),
('Midnight Blue Hearts',   45.00, 'images/midnight-blue-hearts-96.png',2);


SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS admins;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- 1) ADMINS  (from the ERD - manages orders / appointments / services)
-- ============================================================================
CREATE TABLE admins (
    admin_id    INT AUTO_INCREMENT PRIMARY KEY,
    f_name      VARCHAR(50)  NOT NULL,
    l_name      VARCHAR(50)  NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,              -- store password_hash() output only
    phone_no    VARCHAR(20)  NULL,
    address     VARCHAR(255) NULL,
    profile_image VARCHAR(255) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================================
-- 2) CUSTOMERS  (from the ERD - registered users; guests simply have no row)
-- ============================================================================
CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    f_name      VARCHAR(50)  NOT NULL,
    l_name      VARCHAR(50)  NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,              -- store password_hash() output only
    phone_no    VARCHAR(20)  NULL,
    address     VARCHAR(255) NULL,
    profile_image VARCHAR(255) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================================
-- 3) CATEGORIES  (gallery: "French Tips", "3D & Embellished", ...)
--    Drives the category pills and the checkbox list in the Filter pop-up.
-- ============================================================================
CREATE TABLE categories (
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- ============================================================================
-- 4) PRODUCTS
--    Merged from three sources:
--      - gallery   : category_id + product_name + price + picture
--      - details   : description, material_info, includes_info, shipping_info
--      - ERD       : stock_quantity + timestamps
--    image_url holds the file path, e.g. "images/node-32.png"
--    category_id default 1 so old INSERTs without a category still work.
-- ============================================================================
CREATE TABLE products (
    product_id     INT AUTO_INCREMENT PRIMARY KEY,
    category_id    INT           NOT NULL DEFAULT 1,
    product_name   VARCHAR(150)  NOT NULL,
    description    TEXT          NULL,
    price          DECIMAL(10,2) NOT NULL DEFAULT 0 CHECK (price >= 0),
    stock_quantity INT           NOT NULL DEFAULT 0 CHECK (stock_quantity >= 0),
    image_url      VARCHAR(255)  NULL,
    material_info  VARCHAR(255)  NULL,   -- e.g. "Salon-grade gel"
    includes_info  VARCHAR(255)  NULL,   -- e.g. "24 nails, adhesive, prep kit"
    shipping_info  VARCHAR(255)  NULL,   -- shipping note under "Shipping"
    is_active      TINYINT(1)    NOT NULL DEFAULT 1,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_product_category
        FOREIGN KEY (category_id) REFERENCES categories(category_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;



-- ============================================================================
-- 6) APPOINTMENTS  (one row per booking made on Appointment.php)
--    Merged: guest booking fields (first_name, phone...) from database (1).sql
--    + customer_id / managed_by_admin_id links from the ERD.
--      customer_id NULL = booked by a guest
--      addon_ids        = comma separated service ids, e.g. "3" or "3,4"
--      total_price      = stored at booking time (price + add-ons)
-- ============================================================================
CREATE TABLE appointments (
    appointment_id      INT AUTO_INCREMENT PRIMARY KEY,
    service_id          INT           NOT NULL,
    customer_id         INT           NULL DEFAULT NULL,  -- NULL = guest booking
    managed_by_admin_id INT           NULL DEFAULT NULL,
    addon_ids           VARCHAR(100)  NULL DEFAULT NULL,
    appointment_date    DATE          NOT NULL,
    appointment_time    TIME          NOT NULL,           -- stored as "10:00:00"
    first_name          VARCHAR(100)  NOT NULL,
    last_name           VARCHAR(100)  NOT NULL,
    email               VARCHAR(150)  NOT NULL,
    phone               VARCHAR(30)   NOT NULL,
    special_requests    TEXT          NULL,
    total_price         DECIMAL(10,2) NOT NULL DEFAULT 0,
    status              ENUM('pending','confirmed','cancelled','completed')
                        NOT NULL DEFAULT 'pending',
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_appointment_service
        FOREIGN KEY (service_id) REFERENCES services(service_id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_appointment_customer
        FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_appointment_admin
        FOREIGN KEY (managed_by_admin_id) REFERENCES admins(admin_id)
        ON DELETE SET NULL ON UPDATE CASCADE,

    -- makes the "is this slot already booked?" check fast
    INDEX idx_slot (appointment_date, appointment_time),
    INDEX idx_appointment_status (status)
) ENGINE=InnoDB;

-- ============================================================================
-- 7) CART_ITEMS  (one row per product put in the cart)
--      customer_id filled + session_key NULL  -> logged-in user cart
--      customer_id NULL  + session_key filled -> guest cart:
--          WHERE session_key = session_id()
-- ============================================================================
CREATE TABLE cart_items (
    cart_id     INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT           NULL DEFAULT NULL,
    session_key VARCHAR(64)   NULL DEFAULT NULL,
    product_id  INT           NOT NULL,
    quantity    INT           NOT NULL DEFAULT 1 CHECK (quantity > 0),
    added_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_cart_product
        FOREIGN KEY (product_id) REFERENCES products(product_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_cart_customer
        FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 8) ORDERS  (from the ERD - created at checkout; "Order" is a reserved word
--    in MySQL so the table is called "orders")
-- ============================================================================
CREATE TABLE orders (
    order_id            INT AUTO_INCREMENT PRIMARY KEY,
    customer_id         INT           NOT NULL,
    managed_by_admin_id INT           NULL DEFAULT NULL,
    order_date          DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount        DECIMAL(10,2) NOT NULL DEFAULT 0 CHECK (total_amount >= 0),
    tax                 DECIMAL(10,2) NOT NULL DEFAULT 0 CHECK (tax >= 0),
    shipping_fee        DECIMAL(10,2) NOT NULL DEFAULT 0 CHECK (shipping_fee >= 0),
    shipping_address    VARCHAR(255)  NOT NULL,
    status              ENUM('pending','processing','shipped','delivered','cancelled')
                        NOT NULL DEFAULT 'pending',

    CONSTRAINT fk_order_customer
        FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_order_admin
        FOREIGN KEY (managed_by_admin_id) REFERENCES admins(admin_id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 9) ORDER_ITEMS  (one row per product line inside an order)
-- ============================================================================
CREATE TABLE order_items (
    order_item_id   INT AUTO_INCREMENT PRIMARY KEY,
    order_id        INT           NOT NULL,
    product_id      INT           NOT NULL,
    quantity        INT           NOT NULL CHECK (quantity > 0),
    subtotal        DECIMAL(10,2) NOT NULL CHECK (subtotal >= 0),

    CONSTRAINT fk_orderitem_order
        FOREIGN KEY (order_id) REFERENCES orders(order_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_orderitem_product
        FOREIGN KEY (product_id) REFERENCES products(product_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 10) PAYMENTS  (one payment per order -> UNIQUE(order_id))
-- ============================================================================
CREATE TABLE payments (
    payment_id     INT AUTO_INCREMENT PRIMARY KEY,
    order_id       INT           NOT NULL UNIQUE,
    amount         DECIMAL(10,2) NOT NULL CHECK (amount >= 0),
    payment_method ENUM('credit_card','debit_card','paypal','stripe','cash')
                   NOT NULL,
    transaction_id VARCHAR(100)  NULL,
    payment_status ENUM('pending','completed','failed','refunded')
                   NOT NULL DEFAULT 'pending',
    payment_date   DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_payment_order
        FOREIGN KEY (order_id) REFERENCES orders(order_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 11) CONTACT_MESSAGES  (UnregisteredContactUs.php / RegisteredContactUs.php)
--       customer_id NULL = guest message, filled = logged-in user message
-- ============================================================================
CREATE TABLE contact_messages (
    contact_id  INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT           NULL DEFAULT NULL,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(100)  NOT NULL,
    subject     VARCHAR(200)  NOT NULL,
    message     TEXT          NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_contact_customer
        FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- EXTRA INDEXES (speed up the filters / admin lists)
-- ============================================================================
CREATE INDEX idx_order_status   ON orders (status);
CREATE INDEX idx_order_date     ON orders (order_date);
CREATE INDEX idx_payment_status ON payments (payment_status);
CREATE INDEX idx_product_name   ON products (product_name);
CREATE INDEX idx_service_name   ON services (service_name);
CREATE INDEX idx_service_category ON services (category);

-- ============================================================================
-- SAMPLE DATA
-- ============================================================================

-- ---- admins & customers ----------------------------------------------------
-- The password below is just a PLACEHOLDER. In PHP, store passwords like:
--   password_hash('YourPassword', PASSWORD_DEFAULT)
INSERT INTO admins (f_name, l_name, email, password, phone_no, address) VALUES
('Monet', 'Admin', 'admin@monetnails.com', '$2y$10$REPLACE-ME-WITH-A-REAL-HASH', '+94 77 210 5954',
 'No 90/1, 4th Lane, Werellawatta, Yakkala');

INSERT INTO customers (f_name, l_name, email, password, phone_no, address) VALUES
('Test', 'Customer', 'customer@example.com', '$2y$10$REPLACE-ME-WITH-A-REAL-HASH', '+94 71 234 5678',
 'No 10, Sample Road, Colombo');

-- ---- gallery categories (UnregisteredGallery.php filter) -------------------
INSERT INTO categories (category_name) VALUES
('French Tips'),
('3D & Embellished'),
('Seasonal & Themed'),
('Bridal');

-- ---- products --------------------------------------------------------------
-- The first 5 rows appear on the Gallery page in this exact order
-- (ordered by product_id), matching the Figma design.
-- Row 6 is the full-detail product used by ProductDetails.php.
INSERT INTO products (category_id, product_name, description, price,
                      stock_quantity, image_url,
                      material_info, includes_info, shipping_info) VALUES
(2, 'Signature Press On Set', NULL, 45.00, 50, 'images/node-32.png',
 NULL, NULL, NULL),
(3, 'Azure Hearts', NULL, 35.00, 50, 'images/node-48.png',
 NULL, NULL, NULL),
(4, 'Soft Blush Collection', NULL, 40.00, 50, 'images/node-64.png',
 NULL, NULL, NULL),
(1, 'Pink Cherry Delight', NULL, 45.00, 50, 'images/pink-ribbon-delight-80.png',
 NULL, NULL, NULL),
(2, 'Midnight Blue Hearts', NULL, 45.00, 50, 'images/midnight-blue-hearts-96.png',
 NULL, NULL, NULL),
(2, 'Signature Blossom Press-On Set',
 'Indulge in the delicate artistry of our Signature Blossom set. Each nail is meticulously handcrafted with a soft pink base, accented by elegant bow charms and pearl details. Perfect for adding a touch of quiet luxury to your everyday look.',
 1000.00, 25, 'images/product.png',
 'Salon-grade gel', '24 nails, adhesive, prep kit',
 'Free standard shipping on orders over $50. Express shipping available at checkout.');

-- ---- services for Appointment.php (radio + add-on checkbox) ----------------
INSERT INTO services (category, service_name, description, price,
                      duration_minutes, is_addon, is_active, sort_order) VALUES
('MANICURES', 'Signature Gel Manicure',
 'Detailed cuticle care, shaping, and premium gel polish application.',
 3500.00, 60, 0, 1, 1),
('MANICURES', 'Classic Polish Manicure',
 'Essential care with traditional polish finish.',
 4650.00, 45, 0, 1, 2),
('ENHANCEMENTS & ART', 'Impressionist Art Tier 1',
 'Minimalist line art, negative space, or simple abstract designs on 2-4 nails.',
 250.00, 15, 1, 1, 3);

-- ---- services for the Services page (category / tag / icon) ----------------
-- Specialty rows use price 0 -> the page shows "Inquire for pricing".
INSERT INTO services (category, service_name, description, price,
                      duration_minutes, is_addon, is_active, sort_order,
                      tag, icon_url) VALUES
('Manicure', 'Classic',
 'Meticulous cuticle care, shaping, light massage, and finishing with a premium lacquer of your choice.',
 3500.00, 45, 0, 1, 11, 'Essential', NULL),
('Manicure', 'Gel',
 'Our classic manicure elevated with high-gloss, durable gel polish cured under LED for a flawless finish.',
 4500.00, 60, 0, 1, 12, 'Long-lasting', NULL),
('Manicure', 'Monet Signature Art',
 'A personalized canvas. Includes intricate hand-painted designs, line art, or specialized textures curated to your style.',
 5000.00, 90, 0, 1, 13, 'Bespoke', NULL),
('Pedicure', 'Botanical Spa',
 'A restorative soak infused with seasonal botanicals, followed by gentle exfoliation, massage, and polish.',
 5000.00, 60, 0, 1, 14, 'Relaxing', NULL),
('Pedicure', 'Luxury Jelly Soak',
 'The ultimate hydration experience. Warm aloe-vera jelly soothes joints and softens skin before full pedicure detailing.',
 5200.00, 75, 0, 1, 15, 'Indulgent', NULL),
('Pedicure', 'Express Refresh',
 'A swift, meticulous tidy-up including shaping, light cuticle care, and fresh polish application.',
 4500.00, 30, 0, 1, 16, NULL, NULL),
('Specialty', 'Hard Gel Overlays',
 'Adds a layer of protective strength to natural nails, promoting healthy growth without the use of tips.',
 0.00, 45, 0, 1, 17, NULL, 'images/icon-82.svg'),
('Specialty', 'Acrylic Extensions',
 'Sculpted extensions tailored to your desired length and shape, offering maximum durability.',
 0.00, 75, 0, 1, 18, NULL, 'images/icon-91.svg'),
('Specialty', 'Nail Repair',
 'Precision repair using silk wraps or builder gel to restore torn or broken nails seamlessly.',
 0.00, 45, 0, 1, 19, NULL, 'images/icon-100.svg');



ALTER TABLE cart_items
    ADD COLUMN shape           VARCHAR(50)  NULL DEFAULT NULL,   -- 'Almond', 'Coffin', 'Soft Oval'
    ADD COLUMN finish          VARCHAR(50)  NULL DEFAULT NULL,   -- 'Glossy Gel', 'Velvet Matte'
    ADD COLUMN size_preset     VARCHAR(100) NULL DEFAULT NULL,   -- 'Preset S (14-10-11-10-8mm)', 'Preset M'
    ADD COLUMN custom_code     VARCHAR(50)  NULL DEFAULT NULL,   -- '#MN-88421' (null when using a preset)
    ADD COLUMN extras          VARCHAR(150) NULL DEFAULT NULL,   -- 'Embedded Pearl Accent'
    ADD COLUMN saved_for_later TINYINT(1)   NOT NULL DEFAULT 0;  -- powers the "Save for Later" button
