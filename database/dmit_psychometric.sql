-- DMIT Psychometric Test System Database
-- Import this file directly into phpMyAdmin

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database
CREATE DATABASE IF NOT EXISTS `dmit_psychometric` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dmit_psychometric`;

-- Users table
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `salt` varchar(32) NOT NULL,
  `role` enum('admin','counselor','user') DEFAULT 'user',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_username` (`username`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User sessions table
CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assessment subjects table
CREATE TABLE `assessment_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `age_at_assessment` int(11) NOT NULL,
  `parent_name` varchar(100) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(15) DEFAULT NULL,
  `school_name` varchar(100) DEFAULT NULL,
  `grade_class` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_subject_name` (`subject_name`),
  CONSTRAINT `assessment_subjects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fingerprint data table
CREATE TABLE `fingerprint_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `finger_position` enum('left_thumb','left_index','left_middle','left_ring','left_little','right_thumb','right_index','right_middle','right_ring','right_little') NOT NULL,
  `pattern_type` enum('arch','loop','whorl') NOT NULL,
  `ridge_count` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `analysis_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`analysis_data`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_finger` (`subject_id`,`finger_position`),
  KEY `idx_subject_id` (`subject_id`),
  CONSTRAINT `fingerprint_data_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ATD measurements table
CREATE TABLE `atd_measurements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `left_angle` decimal(5,2) DEFAULT NULL,
  `right_angle` decimal(5,2) DEFAULT NULL,
  `avg_angle` decimal(5,2) DEFAULT NULL,
  `learning_sensitivity` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_subject_id` (`subject_id`),
  CONSTRAINT `atd_measurements_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Intelligence scores table
CREATE TABLE `intelligence_scores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `linguistic` decimal(5,2) NOT NULL,
  `logical_math` decimal(5,2) NOT NULL,
  `spatial` decimal(5,2) NOT NULL,
  `kinesthetic` decimal(5,2) NOT NULL,
  `musical` decimal(5,2) NOT NULL,
  `interpersonal` decimal(5,2) NOT NULL,
  `intrapersonal` decimal(5,2) NOT NULL,
  `naturalist` decimal(5,2) NOT NULL,
  `dominant_intelligence` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_subject_id` (`subject_id`),
  CONSTRAINT `intelligence_scores_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Personality profiles table
CREATE TABLE `personality_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `primary_type` enum('eagle','peacock','dove','owl') NOT NULL,
  `secondary_type` enum('eagle','peacock','dove','owl') DEFAULT NULL,
  `disc_d` decimal(5,2) DEFAULT NULL,
  `disc_i` decimal(5,2) DEFAULT NULL,
  `disc_s` decimal(5,2) DEFAULT NULL,
  `disc_c` decimal(5,2) DEFAULT NULL,
  `traits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`traits`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_subject_id` (`subject_id`),
  CONSTRAINT `personality_profiles_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Brain dominance table
CREATE TABLE `brain_dominance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `left_brain_percent` decimal(5,2) NOT NULL,
  `right_brain_percent` decimal(5,2) NOT NULL,
  `dominance_type` enum('left','right','balanced') NOT NULL,
  `characteristics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`characteristics`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_subject_id` (`subject_id`),
  CONSTRAINT `brain_dominance_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Learning styles table
CREATE TABLE `learning_styles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `visual_percent` decimal(5,2) NOT NULL,
  `auditory_percent` decimal(5,2) NOT NULL,
  `kinesthetic_percent` decimal(5,2) NOT NULL,
  `primary_style` enum('visual','auditory','kinesthetic') NOT NULL,
  `learning_tips` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`learning_tips`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_subject_id` (`subject_id`),
  CONSTRAINT `learning_styles_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quotient scores table
CREATE TABLE `quotient_scores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `iq_score` int(11) NOT NULL,
  `eq_score` int(11) NOT NULL,
  `cq_score` int(11) NOT NULL,
  `aq_score` int(11) NOT NULL,
  `overall_score` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_subject_id` (`subject_id`),
  CONSTRAINT `quotient_scores_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Career recommendations table
CREATE TABLE `career_recommendations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `recommended_streams` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recommended_streams`)),
  `career_roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`career_roles`)),
  `riasec_scores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`riasec_scores`)),
  `suitability_percent` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_subject_id` (`subject_id`),
  CONSTRAINT `career_recommendations_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assessment reports table
CREATE TABLE `assessment_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `report_type` enum('basic','standard','premium') NOT NULL,
  `report_status` enum('pending','processing','completed','failed') DEFAULT 'pending',
  `pdf_file_path` varchar(255) DEFAULT NULL,
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`report_data`)),
  `generated_by` int(11) NOT NULL,
  `generated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_subject_id` (`subject_id`),
  KEY `idx_generated_by` (`generated_by`),
  CONSTRAINT `assessment_reports_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_reports_ibfk_2` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit logs table
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Security events table
CREATE TABLE `security_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` enum('login_attempt','login_success','login_failure','account_locked','password_reset','suspicious_activity','data_access') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_severity` (`severity`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `security_events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- System settings table
CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_encrypted` tinyint(1) DEFAULT 0,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `idx_setting_key` (`setting_key`),
  CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO `users` (`username`, `email`, `password_hash`, `salt`, `role`, `first_name`, `last_name`, `is_active`, `email_verified`) VALUES
('admin', 'admin@dmitpsychometric.com', SHA2(CONCAT('admin123', 'default_salt_change_this'), 256), 'default_salt_change_this', 'admin', 'System', 'Administrator', 1, 1);

-- Insert default system settings
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_name', 'DMIT Psychometric Test System', 'string', 'Website name'),
('max_file_upload_size', '5242880', 'integer', 'Maximum file upload size in bytes'),
('session_timeout', '3600', 'integer', 'Session timeout in seconds'),
('enable_registration', 'true', 'boolean', 'Allow new user registration'),
('maintenance_mode', 'false', 'boolean', 'Enable maintenance mode'),
('report_retention_days', '365', 'integer', 'Days to retain generated reports');

COMMIT;
