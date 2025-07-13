<?php
/**
 * Reset Password - DMIT Psychometric Test System
 * Password reset form with token validation
 */

require_once '../config/config.php';

$errors = [];
$success = false;
$validToken = false;
$token = $_GET['token'] ?? '';

// Validate token
if (!empty($token)) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $stmt = $conn->prepare("
            SELECT pr.*, u.email, u.first_name 
            FROM password_resets pr
            JOIN users u ON pr.user_id = u.id
            WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used = 0
        ");
        $stmt->execute([$token]);
        $resetData = $stmt->fetch();
        
        if ($resetData) {
            $validToken = true;
        } else {
            $errors[] = 'Invalid or expired reset token.';
        }
    } catch (Exception $e) {
        $errors[] = 'Error validating reset token.';
        error_log("Reset token validation error: " . $e->getMessage());
    }
} else {
    $errors[] = 'No reset token provided.';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::verifyCSRFToken($csrfToken)) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($newPassword)) {
            $errors[] = 'New password is required.';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        } else {
            try {
                // Update password
                $passwordData = Security::hashPassword($newPassword);
                $stmt = $conn->prepare("UPDATE users SET password_hash = ?, salt = ? WHERE id = ?");
                $stmt->execute([$passwordData['hash'], $passwordData['salt'], $resetData['user_id']]);
                
                // Mark token as used
                $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
                $stmt->execute([$token]);
                
                // Log security event
                Security::logSecurityEvent('password_reset_completed', $resetData['user_id']);
                
                $success = true;
                
            } catch (Exception $e) {
                $errors[] = 'Failed to reset password. Please try again.';
                error_log("Password reset error: " . $e->getMessage());
            }
        }
    }
}

$pageTitle = 'Reset Password - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            text-align: center;
            padding: 2rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
        }
        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-lock"></i> Reset Password</h3>
                        <p class="mb-0">Create a new secure password</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle"></i> Password Reset Successful!</h5>
                                <p>Your password has been successfully reset. You can now log in with your new password.</p>
                            </div>
                            <div class="text-center">
                                <a href="login.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt"></i> Login Now
                                </a>
                            </div>
                        <?php elseif (!$validToken): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-exclamation-triangle"></i> Invalid Reset Link</h5>
                                <p>This password reset link is invalid or has expired. Please request a new password reset.</p>
                            </div>
                            <div class="text-center">
                                <a href="forgot_password.php" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Request New Reset
                                </a>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-user"></i> 
                                    <strong>Account:</strong> <?php echo htmlspecialchars($resetData['email']); ?>
                                </small>
                            </div>

                            <form method="POST" id="resetPasswordForm">
                                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" id="new_password" name="new_password" 
                                               placeholder="Enter new password" required minlength="8">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength bg-light" id="passwordStrength"></div>
                                    <small class="text-muted">Minimum 8 characters required</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                               placeholder="Confirm new password" required>
                                    </div>
                                    <div id="passwordMatch" class="mt-1"></div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3" id="resetBtn">
                                    <i class="fas fa-save"></i> Reset Password
                                </button>
                            </form>
                            
                            <div class="text-center">
                                <a href="login.php" class="text-decoration-none">
                                    <i class="fas fa-arrow-left"></i> Back to Login
                                </a>
                            </div>
                            
                            <div class="alert alert-warning mt-3">
                                <small>
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Security Note:</strong> This reset link will expire and can only be used once.
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-white">
                        <i class="fas fa-shield-alt"></i> Secure Password Reset | 
                        <a href="../help/privacy.php" class="text-white">Privacy Policy</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('new_password');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Password strength indicator
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            const colors = ['bg-danger', 'bg-warning', 'bg-info', 'bg-success', 'bg-primary'];
            const widths = ['20%', '40%', '60%', '80%', '100%'];
            
            strengthBar.className = 'password-strength ' + (colors[strength - 1] || 'bg-light');
            strengthBar.style.width = widths[strength - 1] || '0%';
        });

        // Password confirmation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('new_password').value;
            const confirm = this.value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirm.length > 0) {
                if (password === confirm) {
                    matchDiv.innerHTML = '<small class="text-success"><i class="fas fa-check"></i> Passwords match</small>';
                } else {
                    matchDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times"></i> Passwords do not match</small>';
                }
            } else {
                matchDiv.innerHTML = '';
            }
        });

        // Form submission
        document.getElementById('resetPasswordForm').addEventListener('submit', function() {
            const btn = document.getElementById('resetBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Resetting...';
        });
    </script>
</body>
</html>
