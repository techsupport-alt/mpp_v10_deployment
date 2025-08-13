-- MPP Database - Comprehensive Merged Schema
-- Combines all schema files into one for easier deployment

-- First, drop existing tables if they exist (in reverse dependency order)
DROP TABLE IF EXISTS admin_sessions;
DROP TABLE IF EXISTS admin_activity_log;
DROP TABLE IF EXISTS email_queue;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS system_settings;
DROP TABLE IF EXISTS admin_users;
DROP TABLE IF EXISTS prayer_time_slots;
DROP TABLE IF EXISTS volunteer_registrations;
DROP TABLE IF EXISTS prayer_signups;
DROP TABLE IF EXISTS volunteer_signups;
DROP TABLE IF EXISTS newsletter_subscriptions;

-- Admin Users Table (most complete version)
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    created_by INT NULL,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Admin Activity Log Table
CREATE TABLE admin_activity_log (
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

-- System Settings Table
CREATE TABLE system_settings (
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

-- Notifications Table
CREATE TABLE notifications (
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

-- Prayer Time Slots Table
CREATE TABLE prayer_time_slots (
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

-- Email Queue Table
CREATE TABLE email_queue (
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_scheduled_at (scheduled_at),
    INDEX idx_priority (priority)
);

-- Prayer Signups Table (most complete version)
CREATE TABLE prayer_signups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    state VARCHAR(100) NOT NULL,
    church VARCHAR(255),
    prayer_time VARCHAR(50) NOT NULL,
    commitment VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    status ENUM('pending', 'active', 'prayed', 'completed', 'deleted') DEFAULT 'pending',
    preferred_language ENUM('english', 'hausa', 'yoruba', 'igbo', 'pidgin') DEFAULT 'english',
    prayer_location VARCHAR(255) NULL,
    admin_notes TEXT NULL,
    prayer_response TEXT NULL,
    prayed_at TIMESTAMP NULL,
    prayed_by INT NULL,
    assigned_time_slot INT NULL,
    assigned_by INT NULL,
    
    UNIQUE KEY unique_email (email),
    INDEX idx_state (state),
    INDEX idx_prayer_time (prayer_time),
    INDEX idx_created_at (created_at),
    INDEX idx_status (status)
);

-- Volunteer Registrations Table (most complete version)
CREATE TABLE volunteer_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Personal Information
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    age INT NOT NULL CHECK (age >= 16 AND age <= 100),
    country VARCHAR(100) DEFAULT 'Nigeria',
    state VARCHAR(100) NOT NULL,
    church_affiliation VARCHAR(255) DEFAULT '',
    
    -- Volunteer Preferences
    duration ENUM('7', '14', '21', '40') NOT NULL,
    start_date DATE NOT NULL,
    time_of_day ENUM('morning', 'afternoon', 'night', 'flexible') NOT NULL,
    service_type ENUM(
        'intercession', 
        'technical', 
        'ushering', 
        'logistics', 
        'protocol', 
        'medical', 
        'sanitation', 
        'praise'
    ) NOT NULL,
    
    -- Spiritual Background
    born_again ENUM('yes', 'no') NOT NULL,
    holy_spirit ENUM('yes', 'no') NOT NULL,
    prayer_frequency ENUM('daily', 'weekly', 'occasionally', 'rarely') NOT NULL,
    bible_study ENUM('daily', 'weekly', 'monthly', 'occasionally', 'rarely') NOT NULL,
    
    -- Emergency Contact
    emergency_name VARCHAR(255) NOT NULL,
    emergency_phone VARCHAR(20) NOT NULL,
    emergency_relationship VARCHAR(100) NOT NULL,
    
    -- Consent Fields
    commitment_consent TINYINT(1) DEFAULT 1,
    devotional_consent TINYINT(1) DEFAULT 1,
    
    -- Metadata
    status ENUM('pending', 'approved', 'rejected', 'active', 'completed') DEFAULT 'pending',
    notes TEXT,
    assigned_team VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_service_type (service_type),
    INDEX idx_duration (duration),
    INDEX idx_status (status),
    INDEX idx_start_date (start_date),
    INDEX idx_created_at (created_at)
);

-- Volunteer Signups Table
CREATE TABLE volunteer_signups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    state VARCHAR(100) NOT NULL,
    areas JSON NOT NULL,
    experience TEXT,
    availability VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    status ENUM('pending', 'approved', 'contacted', 'rejected') DEFAULT 'pending',
    
    UNIQUE KEY unique_email (email),
    INDEX idx_state (state),
    INDEX idx_availability (availability),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Newsletter Subscriptions Table
CREATE TABLE newsletter_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    
    INDEX idx_email (email),
    INDEX idx_subscribed_at (subscribed_at)
);

-- Admin Sessions Table
CREATE TABLE admin_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    INDEX idx_session_token (session_token),
    INDEX idx_admin_id (admin_id),
    INDEX idx_expires_at (expires_at)
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

-- Insert sample prayer signups
INSERT IGNORE INTO prayer_signups (name, email, phone, state, church, prayer_time, commitment) VALUES
('Test User 1', 'test1@example.com', '08012345678', 'Lagos', 'Test Church', '6am-9am', 'daily'),
('Test User 2', 'test2@example.com', '08087654321', 'Abuja', 'Another Church', '9pm-12am', 'weekly');

-- Insert sample volunteer registrations
INSERT IGNORE INTO volunteer_registrations (
    full_name, email, phone_number, gender, age, state,
    duration, start_date, time_of_day, service_type,
    born_again, holy_spirit, prayer_frequency, bible_study,
    emergency_name, emergency_phone, emergency_relationship
) VALUES 
(
    'John Adebayo', 'john.adebayo@example.com', '+234-801-234-5678', 
    'male', 28, 'Lagos',
    '21', '2024-01-15', 'morning', 'intercession',
    'yes', 'yes', 'daily', 'daily',
    'Mary Adebayo', '+234-801-234-5679', 'Wife'
),
(
    'Grace Okafor', 'grace.okafor@example.com', '+234-802-345-6789', 
    'female', 32, 'Abuja',
    '14', '2024-01-20', 'evening', 'praise',
    'yes', 'yes', 'daily', 'weekly',
    'Peter Okafor', '+234-802-345-6790', 'Husband'
),
(
    'David Emeka', 'david.emeka@example.com', '+234-803-456-7890', 
    'male', 25, 'Anambra',
    '40', '2024-01-25', 'flexible', 'technical',
    'yes', 'no', 'weekly', 'weekly',
    'Ruth Emeka', '+234-803-456-7891', 'Sister'
);

-- Insert sample volunteer signups
INSERT IGNORE INTO volunteer_signups (name, email, phone, state, areas, experience, availability) VALUES
('Volunteer Test', 'volunteer@example.com', '08098765432', 'Rivers', '["prayer_coordination", "technical_support"]', 'Previous church volunteer experience', 'part_time');

-- Insert sample newsletter subscriptions
INSERT IGNORE INTO newsletter_subscriptions (email) VALUES
('subscriber1@example.com'),
('subscriber2@example.com');

-- Create additional indexes for performance
CREATE INDEX IF NOT EXISTS idx_volunteer_status ON volunteer_registrations(status);
CREATE INDEX IF NOT EXISTS idx_volunteer_service ON volunteer_registrations(service_type);
CREATE INDEX IF NOT EXISTS idx_volunteer_state ON volunteer_registrations(state);
CREATE INDEX IF NOT EXISTS idx_volunteer_created ON volunteer_registrations(created_at);
CREATE INDEX IF NOT EXISTS idx_volunteer_email ON volunteer_registrations(email);

CREATE INDEX IF NOT EXISTS idx_prayer_status ON prayer_signups(status);
CREATE INDEX IF NOT EXISTS idx_prayer_state ON prayer_signups(state);
CREATE INDEX IF NOT EXISTS idx_prayer_created ON prayer_signups(created_at);
CREATE INDEX IF NOT EXISTS idx_prayer_email ON prayer_signups(email);
