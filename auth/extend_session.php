<?php
/**
 * Session Extension Handler - DMIT Psychometric Test System
 */

require_once '../config/config.php';

header('Content-Type: application/json');

if (!Security::isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Verify CSRF token
$headers = getallheaders();
$csrfToken = $headers['X-CSRF-Token'] ?? '';

if (!Security::verifyCSRFToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $userId = $_SESSION['user_id'];
    $sessionId = session_id();
    
    // Extend session in database
    $stmt = $conn->prepare("
        UPDATE user_sessions 
        SET expires_at = DATE_ADD(NOW(), INTERVAL ? SECOND) 
        WHERE user_id = ? AND session_id = ? AND is_active = 1
    ");
    $stmt->execute([SESSION_LIFETIME, $userId, $sessionId]);
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
    
    // Log session extension
    Security::logSecurityEvent('session_extended', $userId);
    
    echo json_encode(['success' => true, 'message' => 'Session extended']);
    
} catch (Exception $e) {
    error_log("Session extension error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?>
