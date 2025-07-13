<?php
/**
 * Registration Page - DMIT Psychometric Test System
 * Secure user registration with validation and email verification
 */

require_once '../config/config.php';

// Redirect if already logged in
if (Security::isAuthenticated()) {
    header('Location: ../index.php');
    exit();
}

// Check if registration is enabled
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'enable_registration'");
    $stmt->execute();
    $registrationEnabled = $stmt->fetchColumn();
    
    if ($registrationEnabled !== 'true') {
        redirect('login.php', 'Registration is currently disabled. Please contact administrator.', 'warning');
    }
} catch (Exception $e) {
    error_log("Registration check error: " . $e->getMessage());
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Security::sanitizeInput($_POST['username'] ?? '');
    $email = Security::sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $firstName = Security::sanitizeInput($_POST['first_name'] ?? '');
    $lastName = Security::sanitizeInput($_POST['last_name'] ?? '');
    $phone = Security::sanitizeInput($_POST['phone'] ?? '');
    $dateOfBirth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF token
    if (!Security::verifyCSRFToken($csrfToken)) {
        $errors[] = 'Invalid security token. Please try again.';
    }
    
    // Validate required fields
    $requiredFields = ['username', 'email', 'password', 'first_name', 'last_name'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }
    
    // Validate username
    if (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = 'Username must be between 3 and 50 characters.';
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores.';
    }
    
    // Validate email
    if (!Security::validateEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    // Validate password
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }
    
    // Validate phone (optional)
    if (!empty($phone) && !Security::validatePhone($phone)) {
        $errors[] = 'Please enter a valid 10-digit phone number.';
    }
    
    // Validate date of birth
    if (!empty($dateOfBirth) && !validateDate($dateOfBirth)) {
        $errors[] = 'Please enter a valid date of birth.';
    }
    
    if (empty($errors)) {
        try {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                $errors[] = 'Username or email already exists.';
            } else {
                // Create user account
                $passwordData = Security::hashPassword($password);
                $verificationToken = Security::generateToken();
                
                $stmt = $conn->prepare("
                    INSERT INTO users (username, email, password_hash, salt, first_name, last_name, 
                                     phone, date_of_birth, gender, verification_token, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                
                $stmt->execute([
                    $username, $email, $passwordData['hash'], $passwordData['salt'],
                    $firstName, $lastName, $phone ?: null, $dateOfBirth ?: null, 
                    $gender ?: null, $verificationToken
                ]);
                
                $userId = $conn->lastInsertId();
                
                // Log registration
                Security::logSecurityEvent('user_registration', $userId);
                logAudit('user_registration', 'users', $userId);
                
                // Send verification email (simplified version)
                $verificationLink = BASE_URL . "auth/verify.php?token=" . $verificationToken;
                $subject = "Verify your account - " . APP_NAME;
                $body = "
                    <h2>Welcome to " . APP_NAME . "!</h2>
                    <p>Hello $firstName,</p>
                    <p>Thank you for registering. Please click the link below to verify your email address:</p>
                    <p><a href='$verificationLink'>Verify Email Address</a></p>
                    <p>If you didn't create this account, please ignore this email.</p>
                    <p>Best regards,<br>" . APP_NAME . " Team</p>
                ";
                
                // In production, use proper email service
                if (sendEmail($email, $subject, $body, true)) {
                    $success = true;
                } else {
                    // If email fails, still show success but log the error
                    error_log("Failed to send verification email to: $email");
                    $success = true;
                }
            }
            
        } catch (Exception $e) {
            $errors[] = 'Registration failed. Please try again.';
            error_log("Registration error: " . $e->getMessage());
        }
    }
}

$pageTitle = 'Register - ' . APP_NAME;
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
            padding: 2rem 0;
        }
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .register-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .register-body {
            padding: 2rem;
        }
        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 5px;
            transition: all 0.3s;
        }
        .strength-weak { background-color: #dc3545; }
        .strength-fair { background-color: #ffc107; }
        .strength-good { background-color: #28a745; }
        .strength-strong { background-color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-card">
                    <div class="register-header">
                        <h1><i class="fas fa-brain"></i></h1>
                        <h3>DMIT Psychometric</h3>
                        <p class="mb-0">Create Account</p>
                    </div>
                    
                    <div class="register-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <h4><i class="fas fa-check-circle"></i> Registration Successful!</h4>
                                <p>Your account has been created successfully. A verification email has been sent to your email address.</p>
                                <p>Please check your email and click the verification link to activate your account.</p>
                                <a href="login.php" class="btn btn-primary">Go to Login</a>
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
                            
                            <form method="POST" id="registerForm">
                                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" 
                                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" 
                                               value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username *</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                                           pattern="[a-zA-Z0-9_]{3,50}" required>
                                    <div class="form-text">3-50 characters, letters, numbers, and underscores only</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">Password *</label>
                                        <input type="password" class="form-control" id="password" name="password" 
                                               minlength="8" required>
                                        <div class="password-strength" id="passwordStrength"></div>
                                        <div class="form-text" id="passwordHelp">
                                            Must contain uppercase, lowercase, number, and special character
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                                        <input type="password" class="form-control" id="confirm_password" 
                                               name="confirm_password" required>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                                               pattern="[6-9][0-9]{9}">
                                        <div class="form-text">10-digit Indian mobile number</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-select" id="gender" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="male" <?php echo ($_POST['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                                            <option value="female" <?php echo ($_POST['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                                            <option value="other" <?php echo ($_POST['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                           value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>" 
                                           max="<?php echo date('Y-m-d'); ?>">
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="../help/terms.php" target="_blank">Terms of Service</a> 
                                        and <a href="../help/privacy.php" target="_blank">Privacy Policy</a>
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3" id="registerBtn">
                                    <i class="fas fa-user-plus"></i> Create Account
                                </button>
                            </form>
                            
                            <div class="text-center">
                                <small class="text-muted">
                                    Already have an account? 
                                    <a href="login.php" class="text-decoration-none">Sign in here</a>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            const helpText = document.getElementById('passwordHelp');
            
            let strength = 0;
            const checks = [
                /.{8,}/, // At least 8 characters
                /[a-z]/, // Lowercase letter
                /[A-Z]/, // Uppercase letter
                /[0-9]/, // Number
                /[^A-Za-z0-9]/ // Special character
            ];
            
            checks.forEach(check => {
                if (check.test(password)) strength++;
            });
            
            const levels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            const classes = ['', 'strength-weak', 'strength-weak', 'strength-fair', 'strength-good', 'strength-strong'];
            
            strengthBar.className = 'password-strength ' + (classes[strength] || '');
            helpText.textContent = levels[strength] || 'Enter password';
        });
        
        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Form submission
        document.getElementById('registerForm').addEventListener('submit', function() {
            const btn = document.getElementById('registerBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Creating Account...';
        });
    </script>
</body>
</html>
