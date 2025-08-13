-- MPP Final Consolidated Database Schema
-- This single file sets up the entire database from scratch.
-- It combines schemas from schema.sql, update_volunteer_schema.sql, and enhanced_schema.sql.

-- Drop existing tables to ensure a clean setup (ordered to respect foreign key constraints)
DROP TABLE IF EXISTS `admin_activity_log`;
DROP TABLE IF EXISTS `admin_sessions`;
DROP TABLE IF EXISTS `email_queue`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `prayer_time_slots`;
DROP TABLE IF EXISTS `system_settings`;
DROP TABLE IF EXISTS `volunteer_registrations`;
DROP TABLE IF EXISTS `prayer_signups`;
DROP TABLE IF EXISTS `admin_users`;
DROP TABLE IF EXISTS `newsletter_subscriptions`;
DROP VIEW IF EXISTS `registration_analytics`;
DROP VIEW IF EXISTS `prayer_location_analytics`;
DROP VIEW IF EXISTS `volunteer_service_analytics`;

-- Create admin users table (from enhanced_schema.sql)
CREATE TABLE `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `role` ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL,
    `created_by` INT NULL,
    FOREIGN KEY (`created_by`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create admin activity log table (from enhanced_schema.sql)
CREATE TABLE `admin_activity_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT NULL,
    `action` VARCHAR(50) NOT NULL,
    `details` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`admin_id`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Prayer Signups Table (from schema.sql, but with INT PK)
CREATE TABLE `prayer_signups` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(255) NOT NULL,
    `phone_number` VARCHAR(20) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `state` VARCHAR(100) NOT NULL,
    `prayer_time` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Volunteer Registrations Table (from update_volunteer_schema.sql)
CREATE TABLE `volunteer_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `country` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `church_affiliation` varchar(255) DEFAULT NULL,
  `service_type` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `duration` varchar(50) NOT NULL,
  `time_of_day` varchar(50) NOT NULL,
  `born_again` varchar(10) NOT NULL,
  `bible_study` varchar(50) NOT NULL,
  `prayer_frequency` varchar(50) NOT NULL,
  `emergency_name` varchar(255) NOT NULL,
  `emergency_phone` varchar(50) NOT NULL,
  `emergency_relationship` varchar(100) NOT NULL,
  `comments` text DEFAULT NULL,
  `commitment_consent` tinyint(1) NOT NULL,
  `devotional_consent` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Newsletter Subscriptions Table (from schema.sql, but with INT PK)
CREATE TABLE `newsletter_subscriptions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_active` BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create system settings table (from enhanced_schema.sql)
CREATE TABLE `system_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) UNIQUE NOT NULL,
    `setting_value` TEXT NULL,
    `setting_type` ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    `description` TEXT NULL,
    `is_public` BOOLEAN DEFAULT FALSE,
    `updated_by` INT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`updated_by`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create email queue table (from enhanced_schema.sql)
CREATE TABLE `email_queue` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `recipient_email` VARCHAR(255) NOT NULL,
    `recipient_name` VARCHAR(255) NULL,
    `subject` VARCHAR(500) NOT NULL,
    `body` TEXT NOT NULL,
    `email_type` ENUM('welcome', 'reminder', 'notification', 'report', 'custom') NOT NULL,
    `status` ENUM('pending', 'sent', 'failed', 'cancelled') DEFAULT 'pending',
    `priority` ENUM('low', 'medium', 'high') DEFAULT 'medium',
    `scheduled_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `sent_at` TIMESTAMP NULL,
    `error_message` TEXT NULL,
    `created_by` INT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: 'admin123')
INSERT IGNORE INTO `admin_users` (`username`, `password_hash`, `full_name`, `email`, `role`) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'MPP Administrator', 'admin@marathonpraise.ng', 'super_admin');

-- Insert default system settings
INSERT IGNORE INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `is_public`) VALUES
('site_name', '84 Days Marathon Praise & Prayer', 'string', 'Website name', TRUE),
('registration_open', 'true', 'boolean', 'Whether registration is open', TRUE),
('email_notifications', 'true', 'boolean', 'Enable email notifications', FALSE);

-- Add Indexes for performance
CREATE INDEX idx_admin_activity ON admin_activity_log(admin_id, created_at);
CREATE INDEX idx_prayer_email ON prayer_signups(email);
CREATE INDEX idx_volunteer_email ON volunteer_registrations(email);
CREATE INDEX idx_volunteer_state ON volunteer_registrations(state);
CREATE INDEX idx_volunteer_service ON volunteer_registrations(service_type);
CREATE INDEX idx_newsletter_email ON newsletter_subscriptions(email);
CREATE INDEX idx_email_queue_status ON email_queue(status);
