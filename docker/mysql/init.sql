-- Users
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

-- Seed admin user 
-- Password: Admin@1234  (change immediately after first login)
-- Hash generated with: password_hash('Admin@1234', PASSWORD_BCRYPT, ['cost' => 12])
INSERT INTO users (username, first_name, last_name, email, password, role) VALUES (
    'Admin',
    'Gourmet',
    'Express',
    'admin@gourmet-express.com',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin'
) ON DUPLICATE KEY UPDATE id = id;