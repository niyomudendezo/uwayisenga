-- AgruKrwanda Database Schema
-- AI-Based Agriculture Cooperative and Market Linkage System
-- Rwanda

SET FOREIGN_KEY_CHECKS = 0;
DROP DATABASE IF EXISTS agrukrwanda;
CREATE DATABASE agrukrwanda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agrukrwanda;

-- ─── GEOGRAPHY ───────────────────────────────────────────────────────────────
CREATE TABLE districts (
    id   TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    province VARCHAR(50) NOT NULL
);

CREATE TABLE sectors (
    id          SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    district_id TINYINT UNSIGNED NOT NULL,
    name        VARCHAR(100) NOT NULL,
    FOREIGN KEY (district_id) REFERENCES districts(id)
);

CREATE TABLE cells (
    id        SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sector_id SMALLINT UNSIGNED NOT NULL,
    name      VARCHAR(100) NOT NULL,
    FOREIGN KEY (sector_id) REFERENCES sectors(id)
);

CREATE TABLE villages (
    id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cell_id SMALLINT UNSIGNED NOT NULL,
    name    VARCHAR(100) NOT NULL,
    FOREIGN KEY (cell_id) REFERENCES cells(id)
);

-- ─── ROLES & USERS ───────────────────────────────────────────────────────────
CREATE TABLE roles (
    id   TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE users (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id             TINYINT UNSIGNED NOT NULL,
    first_name          VARCHAR(80) NOT NULL,
    last_name           VARCHAR(80) NOT NULL,
    email               VARCHAR(150) NOT NULL UNIQUE,
    phone               VARCHAR(20),
    password            VARCHAR(255) NOT NULL,
    avatar              VARCHAR(255),
    district_id         TINYINT UNSIGNED,
    sector_id           SMALLINT UNSIGNED,
    status              ENUM('active','inactive','suspended') DEFAULT 'active',
    email_verified_at   DATETIME,
    reset_token         VARCHAR(100),
    reset_token_expires DATETIME,
    last_login          DATETIME,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id)     REFERENCES roles(id),
    FOREIGN KEY (district_id) REFERENCES districts(id),
    FOREIGN KEY (sector_id)   REFERENCES sectors(id),
    INDEX idx_email (email),
    INDEX idx_role  (role_id)
);

CREATE TABLE permissions (
    id   SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    module VARCHAR(50) NOT NULL
);

CREATE TABLE role_permissions (
    role_id       TINYINT UNSIGNED NOT NULL,
    permission_id SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id)       REFERENCES roles(id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id)
);

-- ─── COOPERATIVES ────────────────────────────────────────────────────────────
CREATE TABLE cooperatives (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    manager_id      INT UNSIGNED,
    name            VARCHAR(200) NOT NULL,
    registration_no VARCHAR(80) UNIQUE,
    district_id     TINYINT UNSIGNED,
    sector_id       SMALLINT UNSIGNED,
    address         TEXT,
    phone           VARCHAR(20),
    email           VARCHAR(150),
    logo            VARCHAR(255),
    description     TEXT,
    status          ENUM('active','inactive') DEFAULT 'active',
    established_at  DATE,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (manager_id)  REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (district_id) REFERENCES districts(id),
    FOREIGN KEY (sector_id)   REFERENCES sectors(id)
);

CREATE TABLE cooperative_members (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cooperative_id INT UNSIGNED NOT NULL,
    farmer_id      INT UNSIGNED NOT NULL,
    joined_at      DATE,
    role           VARCHAR(50) DEFAULT 'member',
    status         ENUM('active','inactive') DEFAULT 'active',
    UNIQUE KEY uq_coop_farmer (cooperative_id, farmer_id),
    FOREIGN KEY (cooperative_id) REFERENCES cooperatives(id) ON DELETE CASCADE,
    FOREIGN KEY (farmer_id)      REFERENCES farmers(id) ON DELETE CASCADE
);

-- ─── FARMERS ─────────────────────────────────────────────────────────────────
CREATE TABLE farmers (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id        INT UNSIGNED NOT NULL UNIQUE,
    national_id    VARCHAR(20) UNIQUE,
    cooperative_id INT UNSIGNED,
    farm_name      VARCHAR(200),
    farm_size      DECIMAL(10,2),
    farm_size_unit ENUM('hectares','acres','sqm') DEFAULT 'hectares',
    district_id    TINYINT UNSIGNED,
    sector_id      SMALLINT UNSIGNED,
    cell_id        SMALLINT UNSIGNED,
    village_id     INT UNSIGNED,
    gps_lat        DECIMAL(10,7),
    gps_lng        DECIMAL(10,7),
    soil_type      VARCHAR(100),
    irrigation     TINYINT(1) DEFAULT 0,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)        REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (cooperative_id) REFERENCES cooperatives(id) ON DELETE SET NULL,
    FOREIGN KEY (district_id)    REFERENCES districts(id),
    FOREIGN KEY (sector_id)      REFERENCES sectors(id),
    FOREIGN KEY (cell_id)        REFERENCES cells(id),
    FOREIGN KEY (village_id)     REFERENCES villages(id)
);

-- ─── BUYERS ──────────────────────────────────────────────────────────────────
CREATE TABLE buyers (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL UNIQUE,
    company_name    VARCHAR(200),
    business_type   VARCHAR(100),
    tin_number      VARCHAR(50),
    district_id     TINYINT UNSIGNED,
    sector_id       SMALLINT UNSIGNED,
    address         TEXT,
    verified        TINYINT(1) DEFAULT 0,
    verified_at     DATETIME,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)     REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (district_id) REFERENCES districts(id),
    FOREIGN KEY (sector_id)   REFERENCES sectors(id)
);

-- ─── CROPS ───────────────────────────────────────────────────────────────────
CREATE TABLE crop_categories (
    id          TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE crops (
    id          SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id TINYINT UNSIGNED NOT NULL,
    name        VARCHAR(100) NOT NULL,
    variety     VARCHAR(100),
    unit        VARCHAR(20) DEFAULT 'kg',
    description TEXT,
    image       VARCHAR(255),
    status      ENUM('active','inactive') DEFAULT 'active',
    FOREIGN KEY (category_id) REFERENCES crop_categories(id)
);

-- ─── WAREHOUSES ──────────────────────────────────────────────────────────────
CREATE TABLE warehouses (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cooperative_id INT UNSIGNED,
    name           VARCHAR(200) NOT NULL,
    location       VARCHAR(255),
    district_id    TINYINT UNSIGNED,
    capacity       DECIMAL(12,2),
    capacity_unit  VARCHAR(20) DEFAULT 'kg',
    status         ENUM('active','inactive') DEFAULT 'active',
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cooperative_id) REFERENCES cooperatives(id) ON DELETE SET NULL,
    FOREIGN KEY (district_id)    REFERENCES districts(id)
);

-- ─── HARVESTS ────────────────────────────────────────────────────────────────
CREATE TABLE harvests (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    farmer_id      INT UNSIGNED NOT NULL,
    crop_id        SMALLINT UNSIGNED NOT NULL,
    cooperative_id INT UNSIGNED,
    quantity       DECIMAL(12,2) NOT NULL,
    unit           VARCHAR(20) DEFAULT 'kg',
    grade          ENUM('A','B','C','mixed') DEFAULT 'A',
    harvest_date   DATE NOT NULL,
    season         VARCHAR(50),
    notes          TEXT,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id)      REFERENCES farmers(id),
    FOREIGN KEY (crop_id)        REFERENCES crops(id),
    FOREIGN KEY (cooperative_id) REFERENCES cooperatives(id) ON DELETE SET NULL,
    INDEX idx_farmer_crop (farmer_id, crop_id),
    INDEX idx_harvest_date (harvest_date)
);

-- ─── INVENTORY ───────────────────────────────────────────────────────────────
CREATE TABLE inventories (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cooperative_id   INT UNSIGNED NOT NULL,
    warehouse_id     INT UNSIGNED,
    crop_id          SMALLINT UNSIGNED NOT NULL,
    harvest_id       INT UNSIGNED,
    qty_available    DECIMAL(12,2) DEFAULT 0,
    qty_reserved     DECIMAL(12,2) DEFAULT 0,
    qty_sold         DECIMAL(12,2) DEFAULT 0,
    qty_damaged      DECIMAL(12,2) DEFAULT 0,
    unit             VARCHAR(20) DEFAULT 'kg',
    grade            ENUM('A','B','C','mixed') DEFAULT 'A',
    buying_price     DECIMAL(12,2),
    asking_price     DECIMAL(12,2),
    harvest_date     DATE,
    expiry_date      DATE,
    image            VARCHAR(255),
    status           ENUM('available','reserved','sold','expired') DEFAULT 'available',
    created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cooperative_id) REFERENCES cooperatives(id),
    FOREIGN KEY (warehouse_id)   REFERENCES warehouses(id) ON DELETE SET NULL,
    FOREIGN KEY (crop_id)        REFERENCES crops(id),
    FOREIGN KEY (harvest_id)     REFERENCES harvests(id) ON DELETE SET NULL,
    INDEX idx_coop_crop (cooperative_id, crop_id)
);

-- ─── MARKET PRICES ───────────────────────────────────────────────────────────
CREATE TABLE market_prices (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    crop_id     SMALLINT UNSIGNED NOT NULL,
    district_id TINYINT UNSIGNED,
    price       DECIMAL(12,2) NOT NULL,
    unit        VARCHAR(20) DEFAULT 'kg',
    price_date  DATE NOT NULL,
    source      VARCHAR(100),
    created_by  INT UNSIGNED,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (crop_id)     REFERENCES crops(id),
    FOREIGN KEY (district_id) REFERENCES districts(id),
    FOREIGN KEY (created_by)  REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_crop_date (crop_id, price_date)
);

CREATE TABLE buyer_offers (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    buyer_id    INT UNSIGNED NOT NULL,
    crop_id     SMALLINT UNSIGNED NOT NULL,
    district_id TINYINT UNSIGNED,
    price       DECIMAL(12,2) NOT NULL,
    quantity    DECIMAL(12,2),
    unit        VARCHAR(20) DEFAULT 'kg',
    valid_until DATE,
    notes       TEXT,
    status      ENUM('active','expired','withdrawn') DEFAULT 'active',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id)    REFERENCES buyers(id),
    FOREIGN KEY (crop_id)     REFERENCES crops(id),
    FOREIGN KEY (district_id) REFERENCES districts(id)
);

-- ─── ORDERS ──────────────────────────────────────────────────────────────────
CREATE TABLE orders (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_no       VARCHAR(30) NOT NULL UNIQUE,
    buyer_id       INT UNSIGNED NOT NULL,
    cooperative_id INT UNSIGNED NOT NULL,
    total_amount   DECIMAL(14,2) DEFAULT 0,
    status         ENUM('pending','approved','rejected','paid','in_delivery','completed','cancelled') DEFAULT 'pending',
    delivery_date  DATE,
    delivery_addr  TEXT,
    notes          TEXT,
    reviewed_by    INT UNSIGNED,
    reviewed_at    DATETIME,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id)       REFERENCES buyers(id),
    FOREIGN KEY (cooperative_id) REFERENCES cooperatives(id),
    FOREIGN KEY (reviewed_by)    REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_buyer   (buyer_id),
    INDEX idx_coop    (cooperative_id),
    INDEX idx_status  (status)
);

CREATE TABLE order_items (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id     INT UNSIGNED NOT NULL,
    inventory_id INT UNSIGNED NOT NULL,
    crop_id      SMALLINT UNSIGNED NOT NULL,
    quantity     DECIMAL(12,2) NOT NULL,
    unit         VARCHAR(20) DEFAULT 'kg',
    unit_price   DECIMAL(12,2) NOT NULL,
    total_price  DECIMAL(14,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    FOREIGN KEY (order_id)     REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (inventory_id) REFERENCES inventories(id),
    FOREIGN KEY (crop_id)      REFERENCES crops(id)
);

-- ─── PAYMENTS ────────────────────────────────────────────────────────────────
CREATE TABLE payments (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id       INT UNSIGNED NOT NULL,
    amount         DECIMAL(14,2) NOT NULL,
    method         ENUM('bank_transfer','mobile_money','cash','cheque') DEFAULT 'bank_transfer',
    reference      VARCHAR(100),
    status         ENUM('pending','confirmed','failed') DEFAULT 'pending',
    paid_at        DATETIME,
    confirmed_by   INT UNSIGNED,
    notes          TEXT,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id)     REFERENCES orders(id),
    FOREIGN KEY (confirmed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ─── DELIVERIES ──────────────────────────────────────────────────────────────
CREATE TABLE deliveries (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id       INT UNSIGNED NOT NULL UNIQUE,
    driver_name    VARCHAR(150),
    vehicle_plate  VARCHAR(20),
    dispatch_date  DATETIME,
    delivery_date  DATETIME,
    status         ENUM('pending','dispatched','delivered','failed') DEFAULT 'pending',
    notes          TEXT,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- ─── AI PREDICTIONS ──────────────────────────────────────────────────────────
CREATE TABLE ai_predictions (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    crop_id             SMALLINT UNSIGNED NOT NULL,
    district_id         TINYINT UNSIGNED,
    cooperative_id      INT UNSIGNED,
    predicted_demand    ENUM('High','Medium','Low') NOT NULL,
    predicted_price     DECIMAL(12,2),
    best_buyer_id       INT UNSIGNED,
    best_selling_period VARCHAR(100),
    estimated_revenue   DECIMAL(14,2),
    confidence_score    DECIMAL(5,2),
    suggested_qty       DECIMAL(12,2),
    recommendation_text TEXT,
    model_version       VARCHAR(20) DEFAULT '1.0',
    prediction_date     DATE NOT NULL,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (crop_id)        REFERENCES crops(id),
    FOREIGN KEY (district_id)    REFERENCES districts(id),
    FOREIGN KEY (cooperative_id) REFERENCES cooperatives(id) ON DELETE SET NULL,
    FOREIGN KEY (best_buyer_id)  REFERENCES buyers(id) ON DELETE SET NULL,
    INDEX idx_crop_date (crop_id, prediction_date)
);

-- ─── NOTIFICATIONS ───────────────────────────────────────────────────────────
CREATE TABLE notifications (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    type       VARCHAR(50) NOT NULL,
    title      VARCHAR(200) NOT NULL,
    message    TEXT NOT NULL,
    link       VARCHAR(255),
    is_read    TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read)
);

-- ─── REPORTS ─────────────────────────────────────────────────────────────────
CREATE TABLE reports (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200) NOT NULL,
    type        VARCHAR(50) NOT NULL,
    format      ENUM('pdf','excel') DEFAULT 'pdf',
    file_path   VARCHAR(255),
    generated_by INT UNSIGNED,
    params      JSON,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ─── AUDIT & LOGS ────────────────────────────────────────────────────────────
CREATE TABLE audit_logs (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED,
    action     VARCHAR(100) NOT NULL,
    module     VARCHAR(50),
    record_id  INT UNSIGNED,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_action (user_id, action),
    INDEX idx_created (created_at)
);

CREATE TABLE activity_logs (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED,
    description TEXT NOT NULL,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE system_logs (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    level      ENUM('info','warning','error','critical') DEFAULT 'info',
    message    TEXT NOT NULL,
    context    JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ─── SETTINGS & ATTACHMENTS ──────────────────────────────────────────────────
CREATE TABLE settings (
    id         SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key_name   VARCHAR(100) NOT NULL UNIQUE,
    value      TEXT,
    group_name VARCHAR(50) DEFAULT 'general',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE attachments (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    model_type  VARCHAR(50) NOT NULL,
    model_id    INT UNSIGNED NOT NULL,
    file_name   VARCHAR(255) NOT NULL,
    file_path   VARCHAR(255) NOT NULL,
    file_type   VARCHAR(100),
    file_size   INT UNSIGNED,
    uploaded_by INT UNSIGNED,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_model (model_type, model_id)
);

-- ─── PRODUCTION PLANS ────────────────────────────────────────────────────────
CREATE TABLE production_plans (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cooperative_id INT UNSIGNED NOT NULL,
    crop_id        SMALLINT UNSIGNED NOT NULL,
    season         VARCHAR(50),
    planned_qty    DECIMAL(12,2),
    actual_qty     DECIMAL(12,2),
    planned_date   DATE,
    notes          TEXT,
    status         ENUM('planned','in_progress','completed','cancelled') DEFAULT 'planned',
    created_by     INT UNSIGNED,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cooperative_id) REFERENCES cooperatives(id),
    FOREIGN KEY (crop_id)        REFERENCES crops(id),
    FOREIGN KEY (created_by)     REFERENCES users(id) ON DELETE SET NULL
);

SET FOREIGN_KEY_CHECKS = 1;
