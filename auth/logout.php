<?php
/**
 * Logout Script - DMIT Psychometric Test System
 * Secure session termination with audit logging
 */

require_once '../config/config.php';

if (Security::isAuthenticated()) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $userId = $_SESSION['user_id'];
        $sessionId = session_id();
        
        // Deactivate session in database
        $stmt = $conn->prepare("
            UPDATE user_sessions 
            SET is_active = 0 
            WHERE user_id = ? AND session_id = ?
        ");
        $stmt->execute([$userId, $sessionId]);
        
        // Log logout event
        Security::logSecurityEvent('logout', $userId);
        logAudit('user_logout', 'users', $userId);
        
    } catch (Exception $e) {
        error_log("Logout error: " . $e->getMessage());
    }
}

// Destroy session
session_unset();
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login
redirect('login.php', 'You have been logged out successfully.', 'info');
?>
