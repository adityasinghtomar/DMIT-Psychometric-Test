<?php
/**
 * Main Configuration File
 * Security and application settings for DMIT Psychometric Test System
 */

// Security Configuration
define('ENCRYPTION_KEY', 'your-32-character-encryption-key-here'); // Change this!
define('HASH_ALGO', 'sha256');
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 900); // 15 minutes

// Application Configuration
define('APP_NAME', 'DMIT Psychometric Test System');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/DMIT-Psychometric-Test/');
define('UPLOAD_PATH', 'uploads/');
define('REPORTS_PATH', 'reports/');

// File Upload Limits
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Email Configuration (for notifications)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'noreply@dmitpsychometric.com');
define('FROM_NAME', 'DMIT Psychometric System');

// Error Reporting (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Session Security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Start session
session_start();

// Regenerate session ID periodically
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Include required files
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/assessment_engine.php';
require_once __DIR__ . '/../includes/report_generator.php';
?>
