-- Simplified Enhanced MPP Database Schema
-- Compatible with PDO execution

-- Create admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    created_by INT NULL
);

-- Create admin activity log table
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NULL,
    action VARCHAR(50) NOT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create system settings table
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NULL,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    updated_by INT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('volunteer', 'prayer', 'system', 'admin') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    recipient_type ENUM('all', 'volunteers', 'prayer_warriors', 'admins', 'specific') DEFAULT 'all',
    recipient_id INT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL
);

-- Create prayer time slots table for better scheduling
CREATE TABLE IF NOT EXISTS prayer_time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slot_name VARCHAR(100) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    max_participants INT DEFAULT 100,
    current_participants INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_time_slot (start_time, end_time)
);

-- Create email queue table for better email management
CREATE TABLE IF NOT EXISTS email_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_email VARCHAR(255) NOT NULL,
    recipient_name VARCHAR(255) NULL,
    subject VARCHAR(500) NOT NULL,
    body TEXT NOT NULL,
    email_type ENUM('welcome', 'reminder', 'notification', 'report', 'custom', 'volunteer_welcome', 'volunteer_approved', 'prayer_response') NOT NULL,
    status ENUM('pending', 'sent', 'failed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    scheduled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sent_at TIMESTAMP NULL,
    error_message TEXT NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: 'admin123' - CHANGE THIS!)
INSERT IGNORE INTO admin_users (username, password_hash, full_name, email, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'MPP Administrator', 'admin@marathonpraise.ng', 'super_admin');

-- Insert default system settings
INSERT IGNORE INTO system_settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('site_name', '84 Days Marathon Praise & Prayer', 'string', 'Website name', TRUE),
('start_date', '2025-08-01', 'string', 'Campaign start date', TRUE),
('end_date', '2025-10-23', 'string', 'Campaign end date', TRUE),
('max_volunteers', '10000', 'number', 'Maximum number of volunteers', FALSE),
('registration_open', 'true', 'boolean', 'Whether registration is open', TRUE),
('email_notifications', 'true', 'boolean', 'Enable email notifications', FALSE),
('prayer_reminder_frequency', '24', 'number', 'Hours between prayer reminders', FALSE);

-- Insert default prayer time slots
INSERT IGNORE INTO prayer_time_slots (slot_name, start_time, end_time, max_participants) VALUES
('Early Morning', '05:00:00', '07:00:00', 200),
('Morning', '07:00:00', '09:00:00', 300),
('Mid Morning', '09:00:00', '11:00:00', 250),
('Late Morning', '11:00:00', '13:00:00', 200),
('Afternoon', '13:00:00', '15:00:00', 250),
('Mid Afternoon', '15:00:00', '17:00:00', 300),
('Evening', '17:00:00', '19:00:00', 400),
('Night', '19:00:00', '21:00:00', 350),
('Late Night', '21:00:00', '23:00:00', 200),
('Midnight', '23:00:00', '01:00:00', 150),
('Deep Night', '01:00:00', '03:00:00', 100),
('Dawn', '03:00:00', '05:00:00', 100);
