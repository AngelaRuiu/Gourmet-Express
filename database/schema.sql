/*
Gourmet Express — Database Schema
Engine: MySQL 8.0
Charset: utf8mb4 / utf8mb4_unicode_ci
This SQL file defines the database structure for the Gourmet Express restaurant management system.
It includes tables for users, addresses, menu items, reservations, orders, payments, and reviews.
The schema is designed to support dine-in, delivery, and takeout operations with a focus on data integrity and scalability.
*/

CREATE DATABASE IF NOT EXISTS `restaurant_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
 
USE `restaurant_db`;
/*
TABLEA: restaurant_info
Var: TABLE_RESTAURANT_INFO
Stores general information about the restaurant (name, address, contact details, opening hours).
*/
 CREATE TABLE IF NOT EXISTS `restaurant_info` (
    `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`             VARCHAR(150)  NOT NULL,
    `phone`            VARCHAR(20)           DEFAULT NULL,
    `email`            VARCHAR(180)          DEFAULT NULL,
    `street`           VARCHAR(200)  NOT NULL,
    `city`             VARCHAR(100)  NOT NULL,
    `postal_code`      VARCHAR(20)   NOT NULL,
    `opening_time`     TIME          NOT NULL,
    `closing_time`     TIME          NOT NULL,
    `max_delivery_km`  INT           NOT NULL DEFAULT 15,
    `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: users
Var: TABLE_USERS
Stores user accounts for staff, and admins.
*/

CREATE TABLE IF NOT EXISTS `users` (
    `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `username`     VARCHAR(120)    NOT NULL,
    `first_name`   VARCHAR(80)     NOT NULL,
    `last_name`    VARCHAR(80)     NOT NULL,
    `email`        VARCHAR(180)    NOT NULL,
    `password`     VARCHAR(255)    NOT NULL,
    `phone`        VARCHAR(20)             DEFAULT NULL,
    `role`         ENUM('admin','manager','staff') NOT NULL DEFAULT 'staff',
    `is_active`    TINYINT(1)      NOT NULL DEFAULT 1,
    `hire_date`    DATE            NOT NULL,
    `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`),
    UNIQUE KEY `uq_users_username` (`username`),
    INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: categories
Var: TABLE_CATEGORIES
Menu categories (e.g. Starters, Mains, Desserts, Drinks).
*/
CREATE TABLE IF NOT EXISTS `categories` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(80)   NOT NULL,
    `slug`        VARCHAR(100)  NOT NULL,
    `description` TEXT                   DEFAULT NULL,
    `image_path`  VARCHAR(255)           DEFAULT NULL,
    `sort_order`  SMALLINT      NOT NULL DEFAULT 0,
    `is_active`   TINYINT(1)    NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_slug` (`slug`)
    INDEX `idx_categories_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: menu_items
Var: TABLE_MENU_ITEMS
Individual dishes and drinks offered by the restaurant, linked to categories.
*/
CREATE TABLE IF NOT EXISTS `menu_items` (
    `id`              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `category_id`     INT UNSIGNED     NOT NULL,
    `name`            VARCHAR(150)     NOT NULL,
    `slug`            VARCHAR(180)     NOT NULL,
    `description`     TEXT                      DEFAULT NULL,
    `ingredients`     TEXT                      DEFAULT NULL,
    `price`           DECIMAL(8,2)     NOT NULL,
    `is_special`      TINYINT(1)       NOT NULL DEFAULT 0,
    `special_price`   DECIMAL(8,2)              DEFAULT NULL,
    `special_start`   DATETIME                  DEFAULT NULL,
    `special_end`     DATETIME                  DEFAULT NULL,
    `image_path`      VARCHAR(255)              DEFAULT NULL,
    `prep_time`       SMALLINT         NOT NULL DEFAULT 15 COMMENT 'Estimated preparation time in minutes',
    `is_available`    TINYINT(1)       NOT NULL DEFAULT 1,
    `is_featured`     TINYINT(1)       NOT NULL DEFAULT 0,
    `created_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_menu_items_slug` (`slug`),
    INDEX `idx_menu_items_available` (`is_available`),
    INDEX `idx_menu_items_category` (`category_id`),
    CONSTRAINT `fk_menu_items_category` 
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* TABLE: restaurant_tables
Var: TABLE_RESTAURANT_TABLES
Physical dine-in tables in the restaurant.
*/
CREATE TABLE IF NOT EXISTS `restaurant_tables` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `table_number`    VARCHAR(10)  NOT NULL,
    `capacity`        TINYINT      NOT NULL DEFAULT 2,
    `location`        VARCHAR(80)           DEFAULT NULL COMMENT 'e.g. Terrace, Window, Main Hall',
    `is_available`    TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_tables_number` (`table_number`),
    INDEX `idx_tables_available` (`is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: reservations
Var: TABLE_RESERVATIONS
Customer reservations for dine-in, linked to tables. 
Cistomers are guests, no user account 
*/
CREATE TABLE IF NOT EXISTS `reservations` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `table_id`          INT UNSIGNED          DEFAULT NULL,
    `customer_name`     VARCHAR(120) NOT NULL,
    `customer_email`    VARCHAR(180) NOT NULL,
    `customer_phone`    VARCHAR(20)           DEFAULT NULL,
    `guest_count`       TINYINT      NOT NULL DEFAULT 2,
    `reservation_date`  DATE         NOT NULL,
    `reservation_time`  TIME         NOT NULL,
    `duration`          SMALLINT     NOT NULL DEFAULT 60 COMMENT 'Duration of the reservation in minutes',
    `notes`             TEXT                  DEFAULT NULL,
    `status`            ENUM('pending','confirmed','cancelled','completed','no_show') NOT NULL DEFAULT 'pending',
    `created_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_reservations_table` 
        FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/*
TABLE: order_status
Var: TABLE_ORDER_STATUS
Lookup table for order statuses to allow for easier management and potential future expansion.
*/
CREATE TABLE IF NOT EXISTS `order_status` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `status_name` VARCHAR(50)  NOT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_order_status_name` (`status_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: payment_status
Var: TABLE_PAYMENT_STATUS
Lookup table for payment statuses to allow for easier management and potential future expansion.
*/
CREATE TABLE IF NOT EXISTS `payment_status` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `status_name` VARCHAR(50)  NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_payment_status_name` (`status_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: addresses
Var: TABLE_ADDRESSES
Customer addresses for delivery orders, linked to an order.
*/
CREATE TABLE IF NOT EXISTS `addresses` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `order_id`    INT UNSIGNED          DEFAULT NULL,
    `street`      VARCHAR(200)  NOT NULL,
    `city`        VARCHAR(100)  NOT NULL,
    `postal_code` VARCHAR(20)   NOT NULL,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_addresses_order` 
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: orders
Var: TABLE_ORDERS
Customer orders for dine-in, delivery, or takeout. Linked to users (staff who took the order), reservations (for dine-in), and addresses (for delivery).
*/
CREATE TABLE IF NOT EXISTS `orders` (
    `id`                    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `order_number`          VARCHAR(50)   NOT NULL COMMENT 'e.g. ORD-20250426-001',
    `reservation_id`        INT UNSIGNED           DEFAULT NULL COMMENT 'Set for dine-in orders',
    `customer_name`         VARCHAR(100)           DEFAULT NULL,
    `customer_email`        VARCHAR(180)           DEFAULT NULL,
    `customer_phone`        VARCHAR(20)            DEFAULT NULL,
    `type`                  ENUM('dine_in','delivery','takeout') NOT NULL,
    `address_id`            INT UNSIGNED           DEFAULT NULL COMMENT 'For delivery orders',
    `subtotal`              DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `delivery_fee`          DECIMAL(8,2)  NOT NULL DEFAULT 0.00,
    `tax`                   DECIMAL(8,2)  NOT NULL DEFAULT 0.00,
    `total`                 DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `order_status_id`       INT UNSIGNED  NOT NULL,
    `payment_status_id`     INT UNSIGNED  NOT NULL,
    `payment_method`        ENUM('cash','paypal') NOT NULL DEFAULT 'cash',
    `notes`                 TEXT                   DEFAULT NULL,
    `handled_by`            INT UNSIGNED           DEFAULT NULL COMMENT 'Staff member managing the order',
    `estimated_ready_time`  DATETIME               DEFAULT NULL,
    `delivered_at`          DATETIME               DEFAULT NULL,
    `paypal_transaction_id` VARCHAR(100)           DEFAULT NULL,
    `created_at`            DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`            DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_orders_order_number` (`order_number`),
    INDEX `idx_orders_status` (`order_status_id`),
    INDEX `idx_orders_payment_status` (`payment_status_id`),
    INDEX `idx_orders_created_at`      (`created_at`),
    INDEX `idx_orders_payment_method`  (`payment_method`),
    CONSTRAINT `fk_orders_reservation` 
        FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_orders_address` 
        FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_orders_status` 
        FOREIGN KEY (`order_status_id`) REFERENCES `order_status` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_orders_payment_status` 
        FOREIGN KEY (`payment_status_id`) REFERENCES `payment_status` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_orders_handled_by` 
        FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: order_items
Var: TABLE_ORDER_ITEMS
Line items for each order, linking to menu items and capturing price at time of order.
*/
CREATE TABLE IF NOT EXISTS `order_items` (
    `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `order_id`     INT UNSIGNED  NOT NULL,
    `menu_item_id` INT UNSIGNED  NOT NULL,
    `item_name`    VARCHAR(200)  NOT NULL COMMENT 'Name snapshot at time of order',
    `quantity`     SMALLINT      NOT NULL DEFAULT 1,
    `unit_price`   DECIMAL(8,2)  NOT NULL COMMENT 'Price at time of order',
    `subtotal`     DECIMAL(10,2) NOT NULL COMMENT 'quantity * unit_price',
    `notes`        VARCHAR(255)          DEFAULT NULL COMMENT 'e.g. no onions',
    `created_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_order_items_order`     
        FOREIGN KEY (`order_id`)     REFERENCES `orders`     (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_order_items_menu_item` 
        FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/*
TABLE: paypal_transactions
Var: TABLE_PAYPAL_TRANSACTIONS
Stores detailed PayPal transaction records for delivery and takeout orders paid via PayPal.
*/
CREATE TABLE IF NOT EXISTS `paypal_transactions` (
    `id`                INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `order_id`          INT UNSIGNED  NOT NULL,
    `transaction_id`    VARCHAR(100)  NOT NULL COMMENT 'PayPal transaction ID',
    `payer_email`       VARCHAR(150)          DEFAULT NULL,
    `amount`            DECIMAL(10,2) NOT NULL,
    `currency`          CHAR(3)       NOT NULL DEFAULT 'EUR',
    `payment_status`    VARCHAR(50)           DEFAULT NULL COMMENT 'Raw PayPal status string',
    `payment_status_id` INT UNSIGNED  NOT NULL,
    `invoice_id`        VARCHAR(100)          DEFAULT NULL,
    `capture_id`        VARCHAR(100)          DEFAULT NULL,
    `refund_id`         VARCHAR(100)          DEFAULT NULL,
    `payment_details`   JSON                  DEFAULT NULL COMMENT 'Full PayPal API response',
    `created_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_paypal_transaction_id` (`transaction_id`),
    INDEX `idx_paypal_order`       (`order_id`),
    INDEX `idx_paypal_payer_email` (`payer_email`),
    CONSTRAINT `fk_paypal_order`
        FOREIGN KEY (`order_id`)          REFERENCES `orders`         (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_paypal_payment_status`
        FOREIGN KEY (`payment_status_id`) REFERENCES `payment_status` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: payment_logs
Var: TABLE_PAYMENT_LOGS
Logs of payment-related actions for orders, useful for auditing and troubleshooting payment issues.
*/
CREATE TABLE IF NOT EXISTS `payment_logs` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`   INT UNSIGNED NOT NULL,
    `action`     VARCHAR(50)  NOT NULL COMMENT 'e.g. initiated, captured, refunded, failed',
    `message`    TEXT                 DEFAULT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_payment_logs_order`  (`order_id`),
    INDEX `idx_payment_logs_action` (`action`),
    CONSTRAINT `fk_payment_logs_order`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: reviews
Var: TABLE_REVIEWS
Customer reviews tied to an order or general restaurant feedback. Admin must approve before publishing.
*/
CREATE TABLE IF NOT EXISTS `reviews` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`   INT UNSIGNED         DEFAULT NULL,
    `name`       VARCHAR(120) NOT NULL COMMENT 'Guest display name',
    `email`      VARCHAR(180)         DEFAULT NULL,
    `rating`     TINYINT      NOT NULL COMMENT '1-5',
    `comment`    TEXT                 DEFAULT NULL,
    `is_visible` TINYINT(1)   NOT NULL DEFAULT 0 COMMENT 'Admin must approve',
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_reviews_order`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
    CONSTRAINT `chk_reviews_rating` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: contacts
Var: TABLE_CONTACTS
Customer contact messages submitted through the website's contact form.
*/
CREATE TABLE IF NOT EXISTS `contacts` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100) NOT NULL,
    `email`      VARCHAR(150) NOT NULL,
    `phone`      VARCHAR(20)          DEFAULT NULL,
    `subject`    VARCHAR(200)         DEFAULT NULL,
    `message`    TEXT         NOT NULL,
    `status`     ENUM('unread','read','replied') NOT NULL DEFAULT 'unread',
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_contacts_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: job_positions
Var: TABLE_JOB_POSITIONS
Job openings for the restaurant, allowing potential candidates to view and apply for positions.
*/
CREATE TABLE IF NOT EXISTS `job_positions` (
    `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `title`            VARCHAR(150)  NOT NULL,
    `department`       VARCHAR(100)          DEFAULT NULL,
    `employment_type`  ENUM('full_time','part_time','contract','internship') NOT NULL DEFAULT 'full_time',
    `location`         VARCHAR(200)          DEFAULT NULL,
    `salary_range_min` DECIMAL(10,2)         DEFAULT NULL,
    `salary_range_max` DECIMAL(10,2)         DEFAULT NULL,
    `description`      TEXT          NOT NULL,
    `requirements`     TEXT                  DEFAULT NULL,
    `status`           ENUM('open','closed','on_hold') NOT NULL DEFAULT 'open',
    `posted_date`      DATE          NOT NULL DEFAULT (CURRENT_DATE),
    `closing_date`     DATE                  DEFAULT NULL,
    `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_job_positions_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
TABLE: job_applications
Var: TABLE_JOB_APPLICATIONS
Applications submitted by candidates for job positions, linked to the job_positions table.
*/
CREATE TABLE IF NOT EXISTS `job_applications` (
    `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `job_position_id`  INT UNSIGNED  NOT NULL,
    `applicant_name`   VARCHAR(150)  NOT NULL,
    `applicant_email`  VARCHAR(150)  NOT NULL,
    `applicant_phone`  VARCHAR(20)           DEFAULT NULL,
    `cover_letter`     TEXT                  DEFAULT NULL,
    `resume_path`      VARCHAR(500)          DEFAULT NULL,
    `experience_years` DECIMAL(3,1)          DEFAULT NULL,
    `current_company`  VARCHAR(150)          DEFAULT NULL,
    `expected_salary`  DECIMAL(10,2)         DEFAULT NULL,
    `status`           ENUM('pending','reviewed','shortlisted','interviewed','accepted','rejected') NOT NULL DEFAULT 'pending',
    `review_notes`     TEXT                  DEFAULT NULL,
    `reviewed_by`      INT UNSIGNED          DEFAULT NULL,
    `interview_date`   DATETIME              DEFAULT NULL,
    `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_application` (`job_position_id`, `applicant_email`),
    INDEX `idx_job_applications_position` (`job_position_id`),
    INDEX `idx_job_applications_status`   (`status`),
    CONSTRAINT `fk_job_applications_position`
        FOREIGN KEY (`job_position_id`) REFERENCES `job_positions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_job_applications_reviewer`
        FOREIGN KEY (`reviewed_by`)     REFERENCES `users`         (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 