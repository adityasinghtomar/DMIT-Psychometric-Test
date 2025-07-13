-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2025 at 11:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dmit_psychometric`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessment_reports`
--

CREATE TABLE `assessment_reports` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `report_type` enum('basic','standard','premium') NOT NULL,
  `report_status` enum('pending','processing','completed','failed') DEFAULT 'pending',
  `pdf_file_path` varchar(255) DEFAULT NULL,
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`report_data`)),
  `generated_by` int(11) NOT NULL,
  `generated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_subjects`
--

CREATE TABLE `assessment_subjects` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assessment_subjects`
--

INSERT INTO `assessment_subjects` (`id`, `user_id`, `subject_name`, `date_of_birth`, `gender`, `age_at_assessment`, `parent_name`, `contact_email`, `contact_phone`, `school_name`, `grade_class`, `created_at`, `updated_at`) VALUES
(1, 2, 'English', '2010-10-10', 'male', 14, 'test', 'temporary.email.new@gmail.com', '9876543210', 'Test School', '7th', '2025-07-13 09:19:46', '2025-07-13 09:31:41'),
(2, 2, 'English', '2010-10-10', 'male', 14, 'test', 'temporary.email.new@gmail.com', '9876543210', 'Test School', '8th', '2025-07-13 09:32:48', '2025-07-13 09:32:48');

-- --------------------------------------------------------

--
-- Table structure for table `atd_measurements`
--

CREATE TABLE `atd_measurements` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `left_angle` decimal(5,2) DEFAULT NULL,
  `right_angle` decimal(5,2) DEFAULT NULL,
  `avg_angle` decimal(5,2) DEFAULT NULL,
  `learning_sensitivity` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'user_login', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 08:09:26'),
(2, 1, 'user_logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 08:10:45'),
(3, 1, 'user_login', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 08:21:48'),
(4, 1, 'system_settings_updated', 'system_settings', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 08:43:02'),
(5, 1, 'profile_updated', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 08:53:24'),
(6, 1, 'password_changed', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 08:53:49'),
(7, 1, 'user_logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:02:28'),
(8, 1, 'user_login', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:02:55'),
(9, 1, 'user_logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:07:23'),
(10, 1, 'user_login', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:07:38'),
(11, 1, 'user_logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:07:42'),
(12, 1, 'user_login', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:08:05'),
(13, 1, 'user_logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:08:50'),
(14, 1, 'user_login', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:16:13'),
(15, 1, 'user_logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:16:31'),
(16, NULL, 'user_registration', 'users', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:18:40'),
(17, 2, 'user_login', 'users', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:19:01'),
(18, 2, 'assessment_created', 'assessment_subjects', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:19:46'),
(19, 2, 'user_logout', 'users', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:29:47'),
(20, 2, 'user_login', 'users', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:31:21'),
(21, 2, 'assessment_subject_updated', 'assessment_subjects', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:31:41'),
(22, 2, 'assessment_created', 'assessment_subjects', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:32:48'),
(23, 2, 'fingerprint_data_collected', 'fingerprint_data', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:34:30');

-- --------------------------------------------------------

--
-- Table structure for table `brain_dominance`
--

CREATE TABLE `brain_dominance` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `left_brain_percent` decimal(5,2) NOT NULL,
  `right_brain_percent` decimal(5,2) NOT NULL,
  `dominance_type` enum('left','right','balanced') NOT NULL,
  `characteristics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`characteristics`)),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `career_recommendations`
--

CREATE TABLE `career_recommendations` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `recommended_streams` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recommended_streams`)),
  `career_roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`career_roles`)),
  `riasec_scores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`riasec_scores`)),
  `suitability_percent` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fingerprint_data`
--

CREATE TABLE `fingerprint_data` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `finger_position` enum('left_thumb','left_index','left_middle','left_ring','left_little','right_thumb','right_index','right_middle','right_ring','right_little') NOT NULL,
  `pattern_type` enum('arch','loop','whorl') NOT NULL,
  `ridge_count` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `analysis_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`analysis_data`)),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fingerprint_data`
--

INSERT INTO `fingerprint_data` (`id`, `subject_id`, `finger_position`, `pattern_type`, `ridge_count`, `image_path`, `analysis_data`, `created_at`) VALUES
(1, 2, 'left_thumb', 'arch', 10, NULL, NULL, '2025-07-13 09:34:30'),
(2, 2, 'left_index', 'loop', 10, NULL, NULL, '2025-07-13 09:34:30'),
(3, 2, 'left_middle', 'arch', 12, NULL, NULL, '2025-07-13 09:34:30'),
(4, 2, 'left_ring', 'arch', 8, NULL, NULL, '2025-07-13 09:34:30'),
(5, 2, 'left_little', 'loop', 2, NULL, NULL, '2025-07-13 09:34:30'),
(6, 2, 'right_thumb', 'arch', 10, NULL, NULL, '2025-07-13 09:34:30'),
(7, 2, 'right_index', 'arch', 5, NULL, NULL, '2025-07-13 09:34:30'),
(8, 2, 'right_middle', 'loop', 9, NULL, NULL, '2025-07-13 09:34:30'),
(9, 2, 'right_ring', 'whorl', 8, NULL, NULL, '2025-07-13 09:34:30'),
(10, 2, 'right_little', 'loop', 10, NULL, NULL, '2025-07-13 09:34:30');

-- --------------------------------------------------------

--
-- Table structure for table `intelligence_scores`
--

CREATE TABLE `intelligence_scores` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_styles`
--

CREATE TABLE `learning_styles` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `visual_percent` decimal(5,2) NOT NULL,
  `auditory_percent` decimal(5,2) NOT NULL,
  `kinesthetic_percent` decimal(5,2) NOT NULL,
  `primary_style` enum('visual','auditory','kinesthetic') NOT NULL,
  `learning_tips` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`learning_tips`)),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personality_profiles`
--

CREATE TABLE `personality_profiles` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `primary_type` enum('eagle','peacock','dove','owl') NOT NULL,
  `secondary_type` enum('eagle','peacock','dove','owl') DEFAULT NULL,
  `disc_d` decimal(5,2) DEFAULT NULL,
  `disc_i` decimal(5,2) DEFAULT NULL,
  `disc_s` decimal(5,2) DEFAULT NULL,
  `disc_c` decimal(5,2) DEFAULT NULL,
  `traits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`traits`)),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotient_scores`
--

CREATE TABLE `quotient_scores` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `iq_score` int(11) NOT NULL,
  `eq_score` int(11) NOT NULL,
  `cq_score` int(11) NOT NULL,
  `aq_score` int(11) NOT NULL,
  `overall_score` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_events`
--

CREATE TABLE `security_events` (
  `id` int(11) NOT NULL,
  `event_type` enum('login_attempt','login_success','login_failure','account_locked','password_reset','suspicious_activity','data_access') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_events`
--

INSERT INTO `security_events` (`id`, `event_type`, `user_id`, `ip_address`, `user_agent`, `details`, `severity`, `created_at`) VALUES
(1, 'login_success', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 08:09:26'),
(2, '', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 08:10:45'),
(3, 'login_success', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 08:21:48'),
(4, 'password_reset', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'medium', '2025-07-13 08:53:49'),
(5, '', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:02:28'),
(6, 'login_success', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:02:55'),
(7, '', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:07:23'),
(8, 'login_success', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:07:38'),
(9, '', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:07:42'),
(10, 'login_success', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:08:05'),
(11, '', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:08:50'),
(12, 'login_success', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:16:13'),
(13, '', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:16:31'),
(14, '', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:18:40'),
(15, 'login_success', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:19:01'),
(16, '', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:29:47'),
(17, 'login_success', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '[]', 'low', '2025-07-13 09:31:21');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_encrypted` tinyint(1) DEFAULT 0,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `is_encrypted`, `updated_by`, `updated_at`) VALUES
(1, 'site_name', 'DMIT Psychometric Test System', 'string', 'Website name', 0, 1, '2025-07-13 08:43:02'),
(2, 'max_file_upload_size', '5242880', 'integer', 'Maximum file upload size in bytes', 0, 1, '2025-07-13 08:43:02'),
(3, 'session_timeout', '3600', 'integer', 'Session timeout in seconds', 0, 1, '2025-07-13 08:43:02'),
(4, 'enable_registration', 'true', 'boolean', 'Allow new user registration', 0, 1, '2025-07-13 08:43:02'),
(5, 'maintenance_mode', 'false', 'boolean', 'Enable maintenance mode', 0, 1, '2025-07-13 08:43:02'),
(6, 'report_retention_days', '3650', 'integer', 'Days to retain generated reports', 0, 1, '2025-07-13 08:43:02'),
(13, 'max_login_attempts', '5', 'string', NULL, 0, 1, '2025-07-13 08:43:02'),
(14, 'lockout_duration', '900', 'string', NULL, 0, 1, '2025-07-13 08:43:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `salt`, `role`, `first_name`, `last_name`, `phone`, `date_of_birth`, `gender`, `is_active`, `email_verified`, `verification_token`, `reset_token`, `reset_token_expires`, `last_login`, `login_attempts`, `locked_until`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'support@growthclimb.com', 'b57c875c76e37066655d3a608f90e7ea5cd0a1a3fdce4781111618e0eab32e1d', '823ea5175e07ff2e2b2f557fe3d0cbfc', 'admin', 'System', 'Administrator', NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, '2025-07-13 14:46:13', 0, NULL, '2025-07-13 08:03:59', '2025-07-13 09:16:13'),
(2, 'test', 'temporary.email.new@gmail.com', '692ca69d520a4fae7096b407748adaaeff942429d28779c285eafb8ab26817b8', 'a3280671d0ffbc7e788234eed221dcaf', 'user', 'Test', 'test', '9876543210', '2000-10-13', 'male', 1, 0, 'b346389dabbfd22683261572c06a9cabe18763944118894756220d7a42509c6d', NULL, NULL, '2025-07-13 15:01:21', 0, NULL, '2025-07-13 09:18:40', '2025-07-13 09:31:21');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `session_id`, `ip_address`, `user_agent`, `created_at`, `expires_at`, `is_active`) VALUES
(1, 1, 'ejtue3at5ivd9s7fa6dd7mpq8o', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 08:09:26', '2025-07-13 14:39:26', 1),
(2, 1, 'm1kmm26d3alu6c2e9hdpof46ei', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 08:21:48', '2025-07-13 14:51:48', 1),
(3, 1, '2gpff4cbomlhjuad7ovq176a9t', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:02:55', '2025-07-13 15:32:55', 0),
(4, 1, 'urj5fkr0je54p5qp1ecvoe57oq', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:07:38', '2025-07-13 15:37:38', 0),
(5, 1, '62avlekvdi8kuc3h9upfupj8oa', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:08:05', '2025-07-13 15:38:05', 0),
(6, 1, 'skdtihbo32omk9onhn6gn2mvrp', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:16:13', '2025-07-13 15:46:13', 0),
(7, 2, 'mbo5nqkmlkeu0fo65vu548ebrb', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:19:01', '2025-07-13 15:49:01', 1),
(8, 2, '9ma9lv5ma2eloq2kbfa7cek31v', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-13 09:31:21', '2025-07-13 16:01:21', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessment_reports`
--
ALTER TABLE `assessment_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subject_id` (`subject_id`),
  ADD KEY `idx_generated_by` (`generated_by`);

--
-- Indexes for table `assessment_subjects`
--
ALTER TABLE `assessment_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_subject_name` (`subject_name`);

--
-- Indexes for table `atd_measurements`
--
ALTER TABLE `atd_measurements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subject_id` (`subject_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `brain_dominance`
--
ALTER TABLE `brain_dominance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subject_id` (`subject_id`);

--
-- Indexes for table `career_recommendations`
--
ALTER TABLE `career_recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subject_id` (`subject_id`);

--
-- Indexes for table `fingerprint_data`
--
ALTER TABLE `fingerprint_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_finger` (`subject_id`,`finger_position`),
  ADD KEY `idx_subject_id` (`subject_id`);

--
-- Indexes for table `intelligence_scores`
--
ALTER TABLE `intelligence_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subject_id` (`subject_id`);

--
-- Indexes for table `learning_styles`
--
ALTER TABLE `learning_styles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subject_id` (`subject_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `personality_profiles`
--
ALTER TABLE `personality_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subject_id` (`subject_id`);

--
-- Indexes for table `quotient_scores`
--
ALTER TABLE `quotient_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subject_id` (`subject_id`);

--
-- Indexes for table `security_events`
--
ALTER TABLE `security_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event_type` (`event_type`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`),
  ADD KEY `system_settings_ibfk_1` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session_id` (`session_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessment_reports`
--
ALTER TABLE `assessment_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assessment_subjects`
--
ALTER TABLE `assessment_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `atd_measurements`
--
ALTER TABLE `atd_measurements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `brain_dominance`
--
ALTER TABLE `brain_dominance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `career_recommendations`
--
ALTER TABLE `career_recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fingerprint_data`
--
ALTER TABLE `fingerprint_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `intelligence_scores`
--
ALTER TABLE `intelligence_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_styles`
--
ALTER TABLE `learning_styles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personality_profiles`
--
ALTER TABLE `personality_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotient_scores`
--
ALTER TABLE `quotient_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_events`
--
ALTER TABLE `security_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessment_reports`
--
ALTER TABLE `assessment_reports`
  ADD CONSTRAINT `assessment_reports_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assessment_reports_ibfk_2` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `assessment_subjects`
--
ALTER TABLE `assessment_subjects`
  ADD CONSTRAINT `assessment_subjects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atd_measurements`
--
ALTER TABLE `atd_measurements`
  ADD CONSTRAINT `atd_measurements_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `brain_dominance`
--
ALTER TABLE `brain_dominance`
  ADD CONSTRAINT `brain_dominance_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `career_recommendations`
--
ALTER TABLE `career_recommendations`
  ADD CONSTRAINT `career_recommendations_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fingerprint_data`
--
ALTER TABLE `fingerprint_data`
  ADD CONSTRAINT `fingerprint_data_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `intelligence_scores`
--
ALTER TABLE `intelligence_scores`
  ADD CONSTRAINT `intelligence_scores_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `learning_styles`
--
ALTER TABLE `learning_styles`
  ADD CONSTRAINT `learning_styles_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `personality_profiles`
--
ALTER TABLE `personality_profiles`
  ADD CONSTRAINT `personality_profiles_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotient_scores`
--
ALTER TABLE `quotient_scores`
  ADD CONSTRAINT `quotient_scores_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `assessment_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `security_events`
--
ALTER TABLE `security_events`
  ADD CONSTRAINT `security_events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
