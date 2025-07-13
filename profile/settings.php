<?php
/**
 * Profile Settings - DMIT Psychometric Test System
 * User profile management and settings
 */

require_once '../config/config.php';

Security::requireAuth();

$errors = [];
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::verifyCSRFToken($csrfToken)) {
        $errors[] = 'Invalid security token.';
    } else {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            $userId = $_SESSION['user_id'];
            
            if ($action === 'update_profile') {
                $firstName = Security::sanitizeInput($_POST['first_name'] ?? '');
                $lastName = Security::sanitizeInput($_POST['last_name'] ?? '');
                $email = Security::sanitizeInput($_POST['email'] ?? '');
                $phone = Security::sanitizeInput($_POST['phone'] ?? '');
                $dateOfBirth = $_POST['date_of_birth'] ?? '';
                $gender = $_POST['gender'] ?? '';
                
                // Validate required fields
                if (empty($firstName) || empty($lastName) || empty($email)) {
                    $errors[] = 'First name, last name, and email are required.';
                } elseif (!Security::validateEmail($email)) {
                    $errors[] = 'Please enter a valid email address.';
                } else {
                    // Check if email is already taken by another user
                    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                    $stmt->execute([$email, $userId]);
                    
                    if ($stmt->fetch()) {
                        $errors[] = 'Email address is already in use by another account.';
                    } else {
                        $stmt = $conn->prepare("
                            UPDATE users 
                            SET first_name = ?, last_name = ?, email = ?, phone = ?, 
                                date_of_birth = ?, gender = ?, updated_at = CURRENT_TIMESTAMP
                            WHERE id = ?
                        ");
                        $stmt->execute([
                            $firstName, $lastName, $email, $phone ?: null, 
                            $dateOfBirth ?: null, $gender ?: null, $userId
                        ]);
                        
                        // Update session data
                        $_SESSION['user_name'] = $firstName . ' ' . $lastName;
                        $_SESSION['user_email'] = $email;
                        
                        $success = 'Profile updated successfully.';
                        logAudit('profile_updated', 'users', $userId);
                    }
                }
                
            } elseif ($action === 'change_password') {
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                    $errors[] = 'All password fields are required.';
                } elseif ($newPassword !== $confirmPassword) {
                    $errors[] = 'New passwords do not match.';
                } elseif (strlen($newPassword) < 8) {
                    $errors[] = 'New password must be at least 8 characters long.';
                } else {
                    // Verify current password
                    $stmt = $conn->prepare("SELECT password_hash, salt FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch();
                    
                    if (!$user || !Security::verifyPassword($currentPassword, $user['password_hash'], $user['salt'])) {
                        $errors[] = 'Current password is incorrect.';
                    } else {
                        $passwordData = Security::hashPassword($newPassword);
                        $stmt = $conn->prepare("UPDATE users SET password_hash = ?, salt = ? WHERE id = ?");
                        $stmt->execute([$passwordData['hash'], $passwordData['salt'], $userId]);
                        
                        $success = 'Password changed successfully.';
                        logAudit('password_changed', 'users', $userId);
                        Security::logSecurityEvent('password_reset', $userId);
                    }
                }
            }
            
        } catch (Exception $e) {
            $errors[] = 'Update failed: ' . $e->getMessage();
            error_log("Profile update error: " . $e->getMessage());
        }
    }
}

// Load user data
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch();
    
} catch (Exception $e) {
    $userData = [];
    error_log("User data load error: " . $e->getMessage());
}

$pageTitle = 'Profile Settings - ' . APP_NAME;
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../assessments/new.php">
                            <i class="fas fa-plus-circle"></i> New Assessment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../assessments/list.php">
                            <i class="fas fa-list"></i> View Assessments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="settings.php">
                            <i class="fas fa-user-cog"></i> Profile Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../help/user_guide.php">
                            <i class="fas fa-question-circle"></i> Help & Support
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Profile Settings</h1>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Profile Information -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-user"></i> Profile Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="update_profile">
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" 
                                               value="<?php echo htmlspecialchars($userData['first_name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" 
                                               value="<?php echo htmlspecialchars($userData['last_name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-select" id="gender" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="male" <?php echo ($userData['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                                            <option value="female" <?php echo ($userData['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                                            <option value="other" <?php echo ($userData['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                           value="<?php echo htmlspecialchars($userData['date_of_birth'] ?? ''); ?>">
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Profile
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-key"></i> Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="change_password">
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" 
                                               minlength="8" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-info-circle"></i> Account Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td><?php echo htmlspecialchars($userData['username'] ?? ''); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Role:</strong></td>
                                    <td>
                                        <span class="badge bg-<?php echo $userData['role'] === 'admin' ? 'danger' : 'secondary'; ?>">
                                            <?php echo ucfirst($userData['role'] ?? ''); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-<?php echo $userData['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $userData['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Email Verified:</strong></td>
                                    <td>
                                        <?php if ($userData['email_verified']): ?>
                                            <i class="fas fa-check-circle text-success"></i> Verified
                                        <?php else: ?>
                                            <i class="fas fa-exclamation-triangle text-warning"></i> Unverified
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Member Since:</strong></td>
                                    <td><?php echo formatDate($userData['created_at'] ?? '', 'M d, Y'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Last Login:</strong></td>
                                    <td>
                                        <?php if ($userData['last_login']): ?>
                                            <?php echo formatDate($userData['last_login'], 'M d, Y g:i A'); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Never</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && password !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
