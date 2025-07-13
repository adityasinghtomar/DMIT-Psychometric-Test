<?php
/**
 * General Utility Functions
 * Common functions for DMIT Psychometric Test System
 */

/**
 * Generate absolute URL from relative path
 */
function url($path = '') {
    // Remove leading slash if present
    $path = ltrim($path, '/');

    // For critical pages like index.php, use absolute URL to avoid XAMPP conflicts
    if ($path === 'index.php' || $path === '') {
        return BASE_URL . 'index.php';
    }

    // Get the current script's directory
    $currentDir = dirname($_SERVER['SCRIPT_NAME']);
    $baseDir = '/DMIT-Psychometric-Test';

    // Special handling for login.php when we're in auth directory
    if ($path === 'login.php' && $currentDir === '/DMIT-Psychometric-Test/auth') {
        return 'login.php'; // Same directory
    }

    // If we're in a subdirectory, calculate relative path to base
    if ($currentDir !== $baseDir) {
        $depth = substr_count(str_replace($baseDir, '', $currentDir), '/');
        $relativePath = str_repeat('../', $depth);
        return $relativePath . $path;
    }

    return $path;
}

/**
 * Generate absolute URL for assets and links
 */
function asset($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Redirect with message
 */
function redirect($url, $message = '', $type = 'info') {
    if (!empty($message)) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }

    // For critical pages, use absolute URL to avoid XAMPP conflicts
    if ($url === 'index.php' || $url === '') {
        $url = BASE_URL . 'index.php';
    } elseif (!preg_match('/^(https?:\/\/|\/)/i', $url)) {
        // Check if this is a same-directory redirect (no path separators)
        if (strpos($url, '/') === false) {
            // Same directory redirect - use as-is
            // This handles cases like "view.php?id=1" from within assessments/
            $url = $url;
        } else {
            // Cross-directory redirect - use url() function
            $url = url($url);
        }
    }

    header("Location: $url");
    exit();
}

/**
 * Display flash message
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ][$type] ?? 'alert-info';
        
        echo "<div class='alert $alertClass alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
        
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    }
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'Y-m-d H:i:s') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Calculate age from date of birth
 */
function calculateAge($dateOfBirth) {
    $today = new DateTime();
    $dob = new DateTime($dateOfBirth);
    return $today->diff($dob)->y;
}

/**
 * Generate unique reference ID
 */
function generateReferenceId($prefix = 'DMIT') {
    return $prefix . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

/**
 * Validate date format
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Get user's IP address
 */
function getUserIP() {
    $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, 
                    FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Log audit trail
 */
function logAudit($action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $stmt = $conn->prepare("
            INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $userId = $_SESSION['user_id'] ?? null;
        $ipAddress = getUserIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $oldValuesJson = $oldValues ? json_encode($oldValues) : null;
        $newValuesJson = $newValues ? json_encode($newValues) : null;
        
        $stmt->execute([
            $userId, $action, $tableName, $recordId, 
            $oldValuesJson, $newValuesJson, $ipAddress, $userAgent
        ]);
        
    } catch (Exception $e) {
        error_log("Failed to log audit: " . $e->getMessage());
    }
}

/**
 * Send email notification
 */
function sendEmail($to, $subject, $body, $isHTML = true) {
    // Basic email function - can be enhanced with PHPMailer
    $headers = [
        'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>',
        'Reply-To: ' . FROM_EMAIL,
        'X-Mailer: PHP/' . phpversion()
    ];
    
    if ($isHTML) {
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
    }
    
    return mail($to, $subject, $body, implode("\r\n", $headers));
}

/**
 * Create directory if not exists
 */
function createDirectory($path, $permissions = 0755) {
    if (!is_dir($path)) {
        return mkdir($path, $permissions, true);
    }
    return true;
}

/**
 * Get file extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Format file size
 */
function formatFileSize($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Generate pagination
 */
function generatePagination($currentPage, $totalPages, $baseUrl, $params = []) {
    $pagination = '';
    $range = 2; // Number of pages to show on each side of current page
    
    if ($totalPages <= 1) return $pagination;
    
    $pagination .= '<nav aria-label="Page navigation"><ul class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        $prevParams = array_merge($params, ['page' => $currentPage - 1]);
        $prevUrl = $baseUrl . '?' . http_build_query($prevParams);
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $prevUrl . '">Previous</a></li>';
    }
    
    // Page numbers
    $start = max(1, $currentPage - $range);
    $end = min($totalPages, $currentPage + $range);
    
    if ($start > 1) {
        $firstParams = array_merge($params, ['page' => 1]);
        $firstUrl = $baseUrl . '?' . http_build_query($firstParams);
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $firstUrl . '">1</a></li>';
        if ($start > 2) {
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $pageParams = array_merge($params, ['page' => $i]);
        $pageUrl = $baseUrl . '?' . http_build_query($pageParams);
        $active = ($i == $currentPage) ? ' active' : '';
        $pagination .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $pageUrl . '">' . $i . '</a></li>';
    }
    
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $lastParams = array_merge($params, ['page' => $totalPages]);
        $lastUrl = $baseUrl . '?' . http_build_query($lastParams);
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $lastUrl . '">' . $totalPages . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $nextParams = array_merge($params, ['page' => $currentPage + 1]);
        $nextUrl = $baseUrl . '?' . http_build_query($nextParams);
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $nextUrl . '">Next</a></li>';
    }
    
    $pagination .= '</ul></nav>';
    
    return $pagination;
}

/**
 * Validate required fields
 */
function validateRequired($data, $requiredFields) {
    $errors = [];
    
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    
    return $errors;
}

/**
 * Clean filename for safe storage
 */
function cleanFilename($filename) {
    // Remove any path information
    $filename = basename($filename);
    
    // Remove special characters
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    
    // Limit length
    if (strlen($filename) > 100) {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $filename = substr($name, 0, 96 - strlen($extension)) . '.' . $extension;
    }
    
    return $filename;
}
?>
