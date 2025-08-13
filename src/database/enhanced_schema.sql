-- Enhanced MPP Database Schema
-- Adds admin users, activity logging, and improved structure

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
    created_by INT NULL,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Create admin activity log table
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NULL,
    action VARCHAR(50) NOT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_admin_activity (admin_id, created_at),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES admin_users(id) ON DELETE SET NULL
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
    read_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_recipient (recipient_type, recipient_id),
    INDEX idx_created_at (created_at),
    INDEX idx_priority (priority)
);

-- Enhance volunteer_registrations table with additional fields
ALTER TABLE volunteer_registrations 
ADD COLUMN IF NOT EXISTS status ENUM('pending', 'approved', 'rejected', 'on_hold') DEFAULT 'pending' AFTER comments,
ADD COLUMN IF NOT EXISTS approval_date TIMESTAMP NULL AFTER status,
ADD COLUMN IF NOT EXISTS approved_by INT NULL AFTER approval_date,
ADD COLUMN IF NOT EXISTS rejection_reason TEXT NULL AFTER approved_by,
ADD COLUMN IF NOT EXISTS notes TEXT NULL AFTER rejection_reason,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER notes;

-- Add foreign key for approved_by if admin_users table exists
-- ALTER TABLE volunteer_registrations 
-- ADD FOREIGN KEY (approved_by) REFERENCES admin_users(id) ON DELETE SET NULL;

-- Enhance prayer_signups table with additional fields (check if table exists first)
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'prayer_signups');

-- Only add columns if table exists
SET @sql = IF(@table_exists > 0, 
    'ALTER TABLE prayer_signups 
     ADD COLUMN IF NOT EXISTS status ENUM(\'pending\', \'active\', \'prayed\', \'completed\', \'deleted\') DEFAULT \'pending\',
     ADD COLUMN IF NOT EXISTS preferred_language ENUM(\'english\', \'hausa\', \'yoruba\', \'igbo\', \'pidgin\') DEFAULT \'english\',
     ADD COLUMN IF NOT EXISTS prayer_location VARCHAR(255) NULL,
     ADD COLUMN IF NOT EXISTS admin_notes TEXT NULL,
     ADD COLUMN IF NOT EXISTS prayer_response TEXT NULL,
     ADD COLUMN IF NOT EXISTS prayed_at TIMESTAMP NULL,
     ADD COLUMN IF NOT EXISTS prayed_by INT NULL,
     ADD COLUMN IF NOT EXISTS assigned_time_slot INT NULL,
     ADD COLUMN IF NOT EXISTS assigned_by INT NULL,
     ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    'SELECT "prayer_signups table does not exist, skipping column additions" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

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
    email_type ENUM('welcome', 'reminder', 'notification', 'report', 'custom') NOT NULL,
    status ENUM('pending', 'sent', 'failed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    scheduled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sent_at TIMESTAMP NULL,
    error_message TEXT NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_scheduled_at (scheduled_at),
    INDEX idx_priority (priority)
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

-- Create indexes for better performance (only if tables and columns exist)
-- Volunteer indexes
SET @volunteer_table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'volunteer_registrations');
SET @volunteer_status_col_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'volunteer_registrations' AND column_name = 'status');

SET @sql1 = IF(@volunteer_table_exists > 0 AND @volunteer_status_col_exists > 0, 
    'CREATE INDEX IF NOT EXISTS idx_volunteer_status ON volunteer_registrations(status)',
    'SELECT "Skipping volunteer status index" as message'
);
PREPARE stmt1 FROM @sql1; EXECUTE stmt1; DEALLOCATE PREPARE stmt1;

SET @sql2 = IF(@volunteer_table_exists > 0, 
    'CREATE INDEX IF NOT EXISTS idx_volunteer_service ON volunteer_registrations(service_type)',
    'SELECT "Skipping volunteer service index" as message'
);
PREPARE stmt2 FROM @sql2; EXECUTE stmt2; DEALLOCATE PREPARE stmt2;

SET @sql3 = IF(@volunteer_table_exists > 0, 
    'CREATE INDEX IF NOT EXISTS idx_volunteer_state ON volunteer_registrations(state)',
    'SELECT "Skipping volunteer state index" as message'
);
PREPARE stmt3 FROM @sql3; EXECUTE stmt3; DEALLOCATE PREPARE stmt3;

SET @sql4 = IF(@volunteer_table_exists > 0, 
    'CREATE INDEX IF NOT EXISTS idx_volunteer_created ON volunteer_registrations(created_at)',
    'SELECT "Skipping volunteer created index" as message'
);
PREPARE stmt4 FROM @sql4; EXECUTE stmt4; DEALLOCATE PREPARE stmt4;

SET @sql5 = IF(@volunteer_table_exists > 0, 
    'CREATE INDEX IF NOT EXISTS idx_volunteer_email ON volunteer_registrations(email)',
    'SELECT "Skipping volunteer email index" as message'
);
PREPARE stmt5 FROM @sql5; EXECUTE stmt5; DEALLOCATE PREPARE stmt5;

-- Prayer indexes
SET @prayer_table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'prayer_signups');
SET @prayer_status_col_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'prayer_signups' AND column_name = 'status');

SET @sql6 = IF(@prayer_table_exists > 0 AND @prayer_status_col_exists > 0, 
    'CREATE INDEX IF NOT EXISTS idx_prayer_status ON prayer_signups(status)',
    'SELECT "Skipping prayer status index" as message'
);
PREPARE stmt6 FROM @sql6; EXECUTE stmt6; DEALLOCATE PREPARE stmt6;

SET @sql7 = IF(@prayer_table_exists > 0, 
    'CREATE INDEX IF NOT EXISTS idx_prayer_state ON prayer_signups(state)',
    'SELECT "Skipping prayer state index" as message'
);
PREPARE stmt7 FROM @sql7; EXECUTE stmt7; DEALLOCATE PREPARE stmt7;

SET @sql8 = IF(@prayer_table_exists > 0, 
    'CREATE INDEX IF NOT EXISTS idx_prayer_created ON prayer_signups(created_at)',
    'SELECT "Skipping prayer created index" as message'
);
PREPARE stmt8 FROM @sql8; EXECUTE stmt8; DEALLOCATE PREPARE stmt8;

SET @sql9 = IF(@prayer_table_exists > 0, 
    'CREATE INDEX IF NOT EXISTS idx_prayer_email ON prayer_signups(email)',
    'SELECT "Skipping prayer email index" as message'
);
PREPARE stmt9 FROM @sql9; EXECUTE stmt9; DEALLOCATE PREPARE stmt9;
