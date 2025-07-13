<?php
/**
 * Installation Script for DMIT Psychometric Test System
 * This script sets up the database and creates the initial admin user
 */

// Prevent direct access if already installed
if (file_exists('../config/installed.lock')) {
    die('System is already installed. Delete config/installed.lock to reinstall.');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbName = $_POST['db_name'] ?? 'dmit_psychometric';
    $dbUser = $_POST['db_user'] ?? 'root';
    $dbPass = $_POST['db_pass'] ?? '';
    
    $adminUsername = $_POST['admin_username'] ?? '';
    $adminEmail = $_POST['admin_email'] ?? '';
    $adminPassword = $_POST['admin_password'] ?? '';
    $adminFirstName = $_POST['admin_first_name'] ?? '';
    $adminLastName = $_POST['admin_last_name'] ?? '';
    
    // Validate inputs
    if (empty($adminUsername)) $errors[] = 'Admin username is required';
    if (empty($adminEmail)) $errors[] = 'Admin email is required';
    if (empty($adminPassword)) $errors[] = 'Admin password is required';
    if (empty($adminFirstName)) $errors[] = 'Admin first name is required';
    if (empty($adminLastName)) $errors[] = 'Admin last name is required';
    
    if (strlen($adminPassword) < 8) {
        $errors[] = 'Admin password must be at least 8 characters long';
    }
    
    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address';
    }
    
    if (empty($errors)) {
        try {
            // Test database connection
            $dsn = "mysql:host=$dbHost;charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // Create database if it doesn't exist
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$dbName`");
            
            // Read and execute schema
            $schema = file_get_contents('../database/schema.sql');
            
            // Remove the database creation and use statements from schema
            $schema = preg_replace('/CREATE DATABASE.*?;/', '', $schema);
            $schema = preg_replace('/USE.*?;/', '', $schema);
            
            // Split into individual statements
            $statements = array_filter(array_map('trim', explode(';', $schema)));
            
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }
            
            // Create admin user with secure password
            $salt = bin2hex(random_bytes(16));
            $passwordHash = hash('sha256', $adminPassword . $salt);
            
            $stmt = $pdo->prepare("
                DELETE FROM users WHERE username = 'admin' OR email = ?
            ");
            $stmt->execute([$adminEmail]);
            
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password_hash, salt, role, first_name, last_name, is_active, email_verified) 
                VALUES (?, ?, ?, ?, 'admin', ?, ?, 1, 1)
            ");
            $stmt->execute([$adminUsername, $adminEmail, $passwordHash, $salt, $adminFirstName, $adminLastName]);
            
            // Update database configuration
            $configContent = file_get_contents('../config/database.php');
            $configContent = str_replace("private \$host = 'localhost';", "private \$host = '$dbHost';", $configContent);
            $configContent = str_replace("private \$db_name = 'dmit_psychometric';", "private \$db_name = '$dbName';", $configContent);
            $configContent = str_replace("private \$username = 'root';", "private \$username = '$dbUser';", $configContent);
            $configContent = str_replace("private \$password = '';", "private \$password = '$dbPass';", $configContent);
            
            file_put_contents('../config/database.php', $configContent);
            
            // Generate new encryption key
            $encryptionKey = bin2hex(random_bytes(16));
            $configMainContent = file_get_contents('../config/config.php');
            $configMainContent = str_replace("define('ENCRYPTION_KEY', 'your-32-character-encryption-key-here');", 
                                           "define('ENCRYPTION_KEY', '$encryptionKey');", $configMainContent);
            file_put_contents('../config/config.php', $configMainContent);
            
            // Create required directories
            $directories = [
                '../uploads',
                '../uploads/fingerprints',
                '../reports',
                '../logs'
            ];
            
            foreach ($directories as $dir) {
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                
                // Create .htaccess for security
                $htaccess = $dir . '/.htaccess';
                if (!file_exists($htaccess)) {
                    file_put_contents($htaccess, "deny from all\n");
                }
            }
            
            // Create installation lock file
            file_put_contents('../config/installed.lock', date('Y-m-d H:i:s'));
            
            $success = true;
            
        } catch (Exception $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install DMIT Psychometric Test System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .install-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .install-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .install-body {
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="install-card">
                    <div class="install-header">
                        <h1><i class="fas fa-brain"></i> DMIT Psychometric Test System</h1>
                        <p class="mb-0">Installation Wizard</p>
                    </div>
                    
                    <div class="install-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <h4><i class="fas fa-check-circle"></i> Installation Successful!</h4>
                                <p>The DMIT Psychometric Test System has been installed successfully.</p>
                                <hr>
                                <p><strong>Admin Login Details:</strong></p>
                                <ul>
                                    <li>Username: <?php echo htmlspecialchars($adminUsername); ?></li>
                                    <li>Email: <?php echo htmlspecialchars($adminEmail); ?></li>
                                    <li>Password: [As entered during installation]</li>
                                </ul>
                                <a href="../index.php" class="btn btn-primary">
                                    <i class="fas fa-arrow-right"></i> Go to Dashboard
                                </a>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <h5><i class="fas fa-exclamation-triangle"></i> Installation Errors:</h5>
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <h3><i class="fas fa-database"></i> Database Configuration</h3>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="db_host" class="form-label">Database Host</label>
                                        <input type="text" class="form-control" id="db_host" name="db_host" 
                                               value="<?php echo htmlspecialchars($_POST['db_host'] ?? 'localhost'); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="db_name" class="form-label">Database Name</label>
                                        <input type="text" class="form-control" id="db_name" name="db_name" 
                                               value="<?php echo htmlspecialchars($_POST['db_name'] ?? 'dmit_psychometric'); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="db_user" class="form-label">Database Username</label>
                                        <input type="text" class="form-control" id="db_user" name="db_user" 
                                               value="<?php echo htmlspecialchars($_POST['db_user'] ?? 'root'); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="db_pass" class="form-label">Database Password</label>
                                        <input type="password" class="form-control" id="db_pass" name="db_pass" 
                                               value="<?php echo htmlspecialchars($_POST['db_pass'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <h3><i class="fas fa-user-shield"></i> Admin Account</h3>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="admin_username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="admin_username" name="admin_username" 
                                               value="<?php echo htmlspecialchars($_POST['admin_username'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="admin_email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                               value="<?php echo htmlspecialchars($_POST['admin_email'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="admin_first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="admin_first_name" name="admin_first_name" 
                                               value="<?php echo htmlspecialchars($_POST['admin_first_name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="admin_last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="admin_last_name" name="admin_last_name" 
                                               value="<?php echo htmlspecialchars($_POST['admin_last_name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="admin_password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password" 
                                           minlength="8" required>
                                    <div class="form-text">Password must be at least 8 characters long.</div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-download"></i> Install System
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
