<?php
/**
 * Security Functions
 * Comprehensive security utilities for DMIT Psychometric Test System
 */

class Security {
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Sanitize input data
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    /**
     * Validate email address
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number (Indian format)
     */
    public static function validatePhone($phone) {
        $pattern = '/^[6-9]\d{9}$/';
        return preg_match($pattern, $phone);
    }
    
    /**
     * Generate secure password hash
     */
    public static function hashPassword($password, $salt = null) {
        if ($salt === null) {
            $salt = bin2hex(random_bytes(16));
        }
        $hash = hash('sha256', $password . $salt);
        return ['hash' => $hash, 'salt' => $salt];
    }
    
    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash, $salt) {
        $computed_hash = hash('sha256', $password . $salt);
        return hash_equals($hash, $computed_hash);
    }
    
    /**
     * Generate secure random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Encrypt sensitive data
     */
    public static function encryptData($data, $key = null) {
        if ($key === null) {
            $key = ENCRYPTION_KEY;
        }
        
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt sensitive data
     */
    public static function decryptData($encryptedData, $key = null) {
        if ($key === null) {
            $key = ENCRYPTION_KEY;
        }
        
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
    
    /**
     * Rate limiting check
     */
    public static function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 300) {
        $key = 'rate_limit_' . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
        }
        
        $data = $_SESSION[$key];
        
        // Reset if time window has passed
        if (time() - $data['first_attempt'] > $timeWindow) {
            $_SESSION[$key] = ['count' => 1, 'first_attempt' => time()];
            return true;
        }
        
        // Check if limit exceeded
        if ($data['count'] >= $maxAttempts) {
            return false;
        }
        
        $_SESSION[$key]['count']++;
        return true;
    }
    
    /**
     * Log security event
     */
    public static function logSecurityEvent($eventType, $userId = null, $details = []) {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            $stmt = $conn->prepare("
                INSERT INTO security_events (event_type, user_id, ip_address, user_agent, details, severity) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            $detailsJson = json_encode($details);
            $severity = self::determineSeverity($eventType);
            
            $stmt->execute([$eventType, $userId, $ipAddress, $userAgent, $detailsJson, $severity]);
            
        } catch (Exception $e) {
            error_log("Failed to log security event: " . $e->getMessage());
        }
    }
    
    /**
     * Determine event severity
     */
    private static function determineSeverity($eventType) {
        $highSeverity = ['account_locked', 'suspicious_activity'];
        $mediumSeverity = ['login_failure', 'password_reset'];
        
        if (in_array($eventType, $highSeverity)) {
            return 'high';
        } elseif (in_array($eventType, $mediumSeverity)) {
            return 'medium';
        }
        
        return 'low';
    }
    
    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, $allowedTypes = null) {
        if ($allowedTypes === null) {
            $allowedTypes = ALLOWED_IMAGE_TYPES;
        }
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'No file uploaded'];
        }
        
        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['valid' => false, 'error' => 'File size exceeds limit'];
        }
        
        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return ['valid' => false, 'error' => 'Invalid file type'];
        }
        
        return ['valid' => true, 'mime_type' => $mimeType];
    }
    
    /**
     * Generate secure filename
     */
    public static function generateSecureFilename($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        return $filename;
    }
    
    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
    }
    
    /**
     * Check if user has required role
     */
    public static function hasRole($requiredRole) {
        if (!self::isAuthenticated()) {
            return false;
        }
        
        $userRole = $_SESSION['user_role'];
        $roleHierarchy = ['user' => 1, 'counselor' => 2, 'admin' => 3];
        
        return $roleHierarchy[$userRole] >= $roleHierarchy[$requiredRole];
    }
    
    /**
     * Require authentication
     */
    public static function requireAuth($redirectUrl = 'auth/login.php') {
        if (!self::isAuthenticated()) {
            // Use the global redirect function for proper URL handling
            redirect($redirectUrl);
        }
    }
    
    /**
     * Require specific role
     */
    public static function requireRole($requiredRole, $redirectUrl = 'auth/unauthorized.php') {
        if (!self::hasRole($requiredRole)) {
            // Use the global redirect function for proper URL handling
            redirect($redirectUrl);
        }
    }
}
?>
