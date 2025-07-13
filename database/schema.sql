-- DMIT Psychometric Test System Database Schema
-- Clean version without encoding issues

CREATE DATABASE IF NOT EXISTS dmit_psychometric CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dmit_psychometric;

-- Users table with role-based access
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    salt VARCHAR(32) NOT NULL,
    role ENUM('admin', 'counselor', 'user') DEFAULT 'user',
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(15),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_token_expires DATETIME,
    last_login DATETIME,
    login_attempts INT DEFAULT 0,
    locked_until DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role)
);

-- User sessions for security tracking
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(128) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_id (session_id),
    INDEX idx_user_id (user_id)
);

-- Assessment subjects
CREATE TABLE assessment_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    age_at_assessment INT NOT NULL,
    parent_name VARCHAR(100),
    contact_email VARCHAR(100),
    contact_phone VARCHAR(15),
    school_name VARCHAR(100),
    grade_class VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_subject_name (subject_name)
);

-- Fingerprint data storage
CREATE TABLE fingerprint_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    finger_position ENUM('left_thumb', 'left_index', 'left_middle', 'left_ring', 'left_little',
                        'right_thumb', 'right_index', 'right_middle', 'right_ring', 'right_little') NOT NULL,
    pattern_type ENUM('arch', 'loop', 'whorl') NOT NULL,
    ridge_count INT NOT NULL,
    image_path VARCHAR(255),
    analysis_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_finger (subject_id, finger_position),
    INDEX idx_subject_id (subject_id)
);

-- ATD angle measurements
CREATE TABLE atd_measurements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    left_angle DECIMAL(5,2),
    right_angle DECIMAL(5,2),
    avg_angle DECIMAL(5,2),
    learning_sensitivity VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    INDEX idx_subject_id (subject_id)
);

-- Multiple Intelligence scores
CREATE TABLE intelligence_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    linguistic DECIMAL(5,2) NOT NULL,
    logical_math DECIMAL(5,2) NOT NULL,
    spatial DECIMAL(5,2) NOT NULL,
    kinesthetic DECIMAL(5,2) NOT NULL,
    musical DECIMAL(5,2) NOT NULL,
    interpersonal DECIMAL(5,2) NOT NULL,
    intrapersonal DECIMAL(5,2) NOT NULL,
    naturalist DECIMAL(5,2) NOT NULL,
    dominant_intelligence VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    INDEX idx_subject_id (subject_id)
);

-- Personality profiles
CREATE TABLE personality_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    primary_type ENUM('eagle', 'peacock', 'dove', 'owl') NOT NULL,
    secondary_type ENUM('eagle', 'peacock', 'dove', 'owl'),
    disc_d DECIMAL(5,2),
    disc_i DECIMAL(5,2),
    disc_s DECIMAL(5,2),
    disc_c DECIMAL(5,2),
    traits JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    INDEX idx_subject_id (subject_id)
);

-- Brain dominance analysis
CREATE TABLE brain_dominance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    left_brain_percent DECIMAL(5,2) NOT NULL,
    right_brain_percent DECIMAL(5,2) NOT NULL,
    dominance_type ENUM('left', 'right', 'balanced') NOT NULL,
    characteristics JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    INDEX idx_subject_id (subject_id)
);

-- Learning styles
CREATE TABLE learning_styles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    visual_percent DECIMAL(5,2) NOT NULL,
    auditory_percent DECIMAL(5,2) NOT NULL,
    kinesthetic_percent DECIMAL(5,2) NOT NULL,
    primary_style ENUM('visual', 'auditory', 'kinesthetic') NOT NULL,
    learning_tips JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    INDEX idx_subject_id (subject_id)
);

-- Quotient scores
CREATE TABLE quotient_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    iq_score INT NOT NULL,
    eq_score INT NOT NULL,
    cq_score INT NOT NULL,
    aq_score INT NOT NULL,
    overall_score DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    INDEX idx_subject_id (subject_id)
);

-- Career recommendations
CREATE TABLE career_recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    recommended_streams JSON,
    career_roles JSON,
    riasec_scores JSON,
    suitability_percent DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    INDEX idx_subject_id (subject_id)
);

-- Assessment reports
CREATE TABLE assessment_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    report_type ENUM('basic', 'standard', 'premium') NOT NULL,
    report_status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    pdf_file_path VARCHAR(255),
    report_data JSON,
    generated_by INT NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES users(id),
    INDEX idx_subject_id (subject_id),
    INDEX idx_generated_by (generated_by)
);

-- System audit logs
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- Security events tracking
CREATE TABLE security_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type ENUM('login_attempt', 'login_success', 'login_failure', 'account_locked', 
                   'password_reset', 'suspicious_activity', 'data_access') NOT NULL,
    user_id INT,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    details JSON,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_event_type (event_type),
    INDEX idx_user_id (user_id),
    INDEX idx_severity (severity),
    INDEX idx_created_at (created_at)
);

-- System settings
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    is_encrypted BOOLEAN DEFAULT FALSE,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_setting_key (setting_key)
);

-- Insert default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO users (username, email, password_hash, salt, role, first_name, last_name, is_active, email_verified)
VALUES ('admin', 'admin@dmitpsychometric.com',
        SHA2(CONCAT('admin123', 'default_salt_change_this'), 256),
        'default_salt_change_this', 'admin', 'System', 'Administrator', TRUE, TRUE);

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'DMIT Psychometric Test System', 'string', 'Website name'),
('max_file_upload_size', '5242880', 'integer', 'Maximum file upload size in bytes'),
('session_timeout', '3600', 'integer', 'Session timeout in seconds'),
('enable_registration', 'true', 'boolean', 'Allow new user registration'),
('maintenance_mode', 'false', 'boolean', 'Enable maintenance mode'),
('report_retention_days', '365', 'integer', 'Days to retain generated reports');
