-- Security & Rate Limiting Subsystem Migration
USE `creda_db`;

-- Rate Limits & Lockout Tracking Table
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `identifier` VARCHAR(191) NOT NULL, -- IP address or Email identifier (e.g. login_127.0.0.1 or login_user@domain.com)
    `attempts` INT UNSIGNED NOT NULL DEFAULT 1,
    `last_attempt_at` DATETIME NOT NULL,
    `locked_until` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_identifier` (`identifier`),
    INDEX `idx_locked_until` (`locked_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
