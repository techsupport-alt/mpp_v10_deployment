-- MPP Database Schema
-- Supports both MySQL and PostgreSQL

-- Prayer Signups Table
CREATE TABLE prayer_signups (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    full_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    location VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    
    UNIQUE KEY unique_email (email),
    INDEX idx_location (location),
    INDEX idx_created_at (created_at)
);

-- Volunteer Registrations Table
CREATE TABLE volunteer_registrations (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    
    -- Personal Information
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    age INT NOT NULL,
    country VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    church_affiliation VARCHAR(255),
    
    -- Volunteer Preferences
    duration VARCHAR(50),
    start_date DATE,
    time_of_day ENUM('morning', 'afternoon', 'night', 'flexible'),
    service_type ENUM('intercession', 'technical', 'ushering', 'logistics', 'protocol', 'medical', 'sanitation', 'praise'),
    
    -- Spiritual Background
    born_again ENUM('yes', 'no'),
    holy_spirit ENUM('yes', 'no'),
    prayer_frequency VARCHAR(100),
    bible_study VARCHAR(100),
    
    -- Emergency Contact
    emergency_name VARCHAR(255) NOT NULL,
    emergency_phone VARCHAR(20) NOT NULL,
    emergency_relationship VARCHAR(100) NOT NULL,
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    
    UNIQUE KEY unique_email (email),
    INDEX idx_state (state),
    INDEX idx_service_type (service_type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Newsletter Subscriptions Table
CREATE TABLE newsletter_subscriptions (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    email VARCHAR(255) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    
    INDEX idx_email (email),
    INDEX idx_subscribed_at (subscribed_at)
);

-- Admin Users Table
CREATE TABLE admin_users (
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
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
    id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    admin_id VARCHAR(36) NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    INDEX idx_session_token (session_token),
    INDEX idx_admin_id (admin_id),
    INDEX idx_expires_at (expires_at)
);

-- Analytics Views
CREATE VIEW registration_analytics AS
SELECT 
    'prayer_signups' as type,
    COUNT(*) as total_count,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as week_count,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as month_count,
    COUNT(CASE WHEN created_at >= CURDATE() THEN 1 END) as today_count
FROM prayer_signups
WHERE is_active = TRUE

UNION ALL

SELECT 
    'volunteer_registrations' as type,
    COUNT(*) as total_count,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as week_count,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as month_count,
    COUNT(CASE WHEN created_at >= CURDATE() THEN 1 END) as today_count
FROM volunteer_registrations
WHERE is_active = TRUE;

-- Location Analytics View
CREATE VIEW prayer_location_analytics AS
SELECT 
    location,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM prayer_signups WHERE is_active = TRUE), 2) as percentage
FROM prayer_signups 
WHERE is_active = TRUE
GROUP BY location
ORDER BY count DESC;

-- Volunteer Service Analytics View
CREATE VIEW volunteer_service_analytics AS
SELECT 
    service_type,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM volunteer_registrations WHERE is_active = TRUE AND service_type IS NOT NULL), 2) as percentage
FROM volunteer_registrations 
WHERE is_active = TRUE AND service_type IS NOT NULL
GROUP BY service_type
ORDER BY count DESC;

-- Insert default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO admin_users (email, password_hash, full_name, role) 
VALUES ('admin@mpp.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'MPP Administrator', 'super_admin');