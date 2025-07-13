<?php
/**
 * Email Verification - DMIT Psychometric Test System
 */

require_once '../config/config.php';

$message = '';
$messageType = 'info';

if (isset($_GET['token'])) {
    $token = Security::sanitizeInput($_GET['token']);
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $stmt = $conn->prepare("
            SELECT id, email, first_name 
            FROM users 
            WHERE verification_token = ? AND email_verified = 0
        ");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Verify the user
            $stmt = $conn->prepare("
                UPDATE users 
                SET email_verified = 1, verification_token = NULL 
                WHERE id = ?
            ");
            $stmt->execute([$user['id']]);
            
            $message = 'Email verified successfully! You can now log in to your account.';
            $messageType = 'success';
            
            // Log verification
            Security::logSecurityEvent('email_verified', $user['id']);
            logAudit('email_verification', 'users', $user['id']);
            
        } else {
            $message = 'Invalid or expired verification token.';
            $messageType = 'error';
        }
        
    } catch (Exception $e) {
        $message = 'Verification failed. Please try again.';
        $messageType = 'error';
        error_log("Email verification error: " . $e->getMessage());
    }
} else {
    $message = 'No verification token provided.';
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .verify-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="verify-card">
                    <h1><i class="fas fa-brain text-primary"></i></h1>
                    <h3>Email Verification</h3>
                    
                    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> mt-4">
                        <?php if ($messageType === 'success'): ?>
                            <i class="fas fa-check-circle"></i>
                        <?php else: ?>
                            <i class="fas fa-exclamation-triangle"></i>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                    
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Go to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
