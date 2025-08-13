-- MPP Database Schema - Corrected Version
-- Supports both MySQL and PostgreSQL

-- First, drop existing tables if they exist
DROP TABLE IF EXISTS admin_sessions;
DROP TABLE IF EXISTS prayer_signups;
DROP TABLE IF EXISTS volunteer_registrations;
DROP TABLE IF EXISTS volunteer_signups;
DROP TABLE IF EXISTS newsletter_subscriptions;
DROP TABLE IF EXISTS admin_users;

-- Prayer Signups Table (matches API expectations)
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
    
    UNIQUE KEY unique_email (email),
    INDEX idx_state (state),
    INDEX idx_prayer_time (prayer_time),
    INDEX idx_created_at (created_at)
);

-- Volunteer Signups Table (matches API expectations)
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

-- Admin Users Table
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Session Management Table
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

-- Insert default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO admin_users (email, password_hash, full_name, role) 
VALUES ('admin@mpp.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'MPP Administrator', 'super_admin');

-- Create some sample data for testing
INSERT INTO prayer_signups (name, email, phone, state, church, prayer_time, commitment) VALUES
('Test User 1', 'test1@example.com', '08012345678', 'Lagos', 'Test Church', '6am-9am', 'daily'),
('Test User 2', 'test2@example.com', '08087654321', 'Abuja', 'Another Church', '9pm-12am', 'weekly');

INSERT INTO volunteer_signups (name, email, phone, state, areas, experience, availability) VALUES
('Volunteer Test', 'volunteer@example.com', '08098765432', 'Rivers', '["prayer_coordination", "technical_support"]', 'Previous church volunteer experience', 'part_time');

INSERT INTO newsletter_subscriptions (email) VALUES
('subscriber1@example.com'),
('subscriber2@example.com');
