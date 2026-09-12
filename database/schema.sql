-- Scriptly Verified Service Marketplace Database Schema
-- Target Engine: MySQL 8.0+ / MariaDB (XAMPP Environment)

CREATE DATABASE IF NOT EXISTS `creda_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `creda_db`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(191) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `phone_number` VARCHAR(50) DEFAULT NULL,
    `primary_role` ENUM('client', 'provider', 'admin') NOT NULL DEFAULT 'client',
    `entity_type` ENUM('student', 'business', 'corporation') DEFAULT 'student',
    `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
    `onboarding_completed` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('active', 'suspended', 'pending') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Email Verification OTPs Table
CREATE TABLE IF NOT EXISTS `verification_otps` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(191) NOT NULL,
    `otp_code` VARCHAR(10) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `is_used` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_otp_email` (`email`),
    INDEX `idx_otp_code` (`otp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Password Resets Table
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(191) NOT NULL,
    `token` VARCHAR(191) NOT NULL UNIQUE,
    `expires_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_reset_email` (`email`),
    INDEX `idx_reset_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Projects Table
CREATE TABLE IF NOT EXISTS `projects` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `client_id` BIGINT UNSIGNED NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `status` ENUM('open', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'open',
    `budget` DECIMAL(10,2) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`client_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Proposals Table
CREATE TABLE IF NOT EXISTS `proposals` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `provider_id` BIGINT UNSIGNED NOT NULL,
    `cover_letter` TEXT,
    `bid_amount` DECIMAL(10,2) DEFAULT NULL,
    `status` ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`provider_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Milestones Table
CREATE TABLE IF NOT EXISTS `milestones` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `provider_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `completion_percentage` INT NOT NULL DEFAULT 0,
    `status` ENUM('pending', 'submitted', 'approved', 'paid') NOT NULL DEFAULT 'pending',
    `due_date` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`provider_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Messages Table
CREATE TABLE IF NOT EXISTS `messages` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `sender_id` BIGINT UNSIGNED NOT NULL,
    `receiver_id` BIGINT UNSIGNED NOT NULL,
    `project_id` BIGINT UNSIGNED DEFAULT NULL,
    `content` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
