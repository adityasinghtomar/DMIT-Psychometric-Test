<?php
/**
 * Login Page - DMIT Psychometric Test System
 * Secure user authentication with rate limiting and security logging
 */

require_once '../config/config.php';

// Redirect if already logged in
if (Security::isAuthenticated()) {
    header('Location: ../index.php');
    exit();
}

$errors = [];
$loginAttempts = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Security::sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF token
    if (!Security::verifyCSRFToken($csrfToken)) {
        $errors[] = 'Invalid security token. Please try again.';
        Security::logSecurityEvent('suspicious_activity', null, ['reason' => 'Invalid CSRF token']);
    }
    
    // Rate limiting
    $clientId = getUserIP() . '_' . ($username ?: 'unknown');
    if (!Security::checkRateLimit($clientId, MAX_LOGIN_ATTEMPTS, LOCKOUT_DURATION)) {
        $errors[] = 'Too many login attempts. Please try again later.';
        Security::logSecurityEvent('login_attempt', null, ['username' => $username, 'status' => 'rate_limited']);
    }
    
    if (empty($errors)) {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            // Get user by username or email
            $stmt = $conn->prepare("
                SELECT id, username, email, password_hash, salt, role, first_name, last_name, 
                       is_active, login_attempts, locked_until 
                FROM users 
                WHERE (username = ? OR email = ?) AND is_active = 1
            ");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Check if account is locked
                if ($user['locked_until'] && new DateTime() < new DateTime($user['locked_until'])) {
                    $errors[] = 'Account is temporarily locked due to multiple failed login attempts.';
                    Security::logSecurityEvent('login_attempt', $user['id'], ['status' => 'account_locked']);
                } else {
                    // Verify password
                    if (Security::verifyPassword($password, $user['password_hash'], $user['salt'])) {
                        // Successful login
                        
                        // Reset login attempts
                        $stmt = $conn->prepare("
                            UPDATE users 
                            SET login_attempts = 0, locked_until = NULL, last_login = NOW() 
                            WHERE id = ?
                        ");
                        $stmt->execute([$user['id']]);
                        
                        // Create session
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['login_time'] = time();
                        
                        // Log session in database
                        $sessionId = session_id();
                        $stmt = $conn->prepare("
                            INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, expires_at) 
                            VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND))
                        ");
                        $stmt->execute([
                            $user['id'], 
                            $sessionId, 
                            getUserIP(), 
                            $_SERVER['HTTP_USER_AGENT'] ?? '', 
                            SESSION_LIFETIME
                        ]);
                        
                        // Log successful login
                        Security::logSecurityEvent('login_success', $user['id']);
                        logAudit('user_login', 'users', $user['id']);
                        
                        // Redirect to dashboard
                        redirect('../index.php', 'Welcome back, ' . $user['first_name'] . '!', 'success');
                        
                    } else {
                        // Failed login - increment attempts
                        $newAttempts = $user['login_attempts'] + 1;
                        $lockedUntil = null;
                        
                        if ($newAttempts >= MAX_LOGIN_ATTEMPTS) {
                            $lockedUntil = date('Y-m-d H:i:s', time() + LOCKOUT_DURATION);
                            $errors[] = 'Account locked due to multiple failed login attempts. Try again in ' . 
                                       (LOCKOUT_DURATION / 60) . ' minutes.';
                            Security::logSecurityEvent('account_locked', $user['id']);
                        } else {
                            $remaining = MAX_LOGIN_ATTEMPTS - $newAttempts;
                            $errors[] = "Invalid credentials. $remaining attempts remaining.";
                        }
                        
                        $stmt = $conn->prepare("
                            UPDATE users 
                            SET login_attempts = ?, locked_until = ? 
                            WHERE id = ?
                        ");
                        $stmt->execute([$newAttempts, $lockedUntil, $user['id']]);
                        
                        Security::logSecurityEvent('login_failure', $user['id'], ['attempts' => $newAttempts]);
                    }
                }
            } else {
                $errors[] = 'Invalid credentials.';
                Security::logSecurityEvent('login_failure', null, ['username' => $username, 'reason' => 'user_not_found']);
            }
            
        } catch (Exception $e) {
            $errors[] = 'Login failed. Please try again.';
            error_log("Login error: " . $e->getMessage());
        }
    }
}

$pageTitle = 'Login - ' . APP_NAME;
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
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .security-info {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 1rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <h1><i class="fas fa-brain"></i></h1>
                        <h3>DMIT Psychometric</h3>
                        <p class="mb-0">Secure Login</p>
                    </div>
                    
                    <div class="login-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="loginForm">
                            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user"></i> Username or Email
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                                       required autocomplete="username">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Password
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           required autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                                <label class="form-check-label" for="rememberMe">
                                    Remember me
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3" id="loginBtn">
                                <i class="fas fa-sign-in-alt"></i> Sign In
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <a href="forgot_password.php" class="text-decoration-none">
                                <i class="fas fa-key"></i> Forgot Password?
                            </a>
                        </div>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Don't have an account? 
                                <a href="register.php" class="text-decoration-none">Register here</a>
                            </small>
                        </div>
                        
                        <div class="security-info">
                            <small>
                                <i class="fas fa-shield-alt text-success"></i>
                                <strong>Security Notice:</strong> This is a secure login. 
                                Your session will expire after <?php echo SESSION_LIFETIME / 60; ?> minutes of inactivity.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Signing In...';
        });
        
        // Auto-focus username field
        document.getElementById('username').focus();
    </script>
</body>
</html>
