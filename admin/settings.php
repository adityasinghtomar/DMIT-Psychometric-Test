<?php
/**
 * System Settings - DMIT Psychometric Test System
 * Configure system-wide settings
 */

require_once '../config/config.php';

Security::requireAuth();
Security::requireRole('admin');

$errors = [];
$success = '';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::verifyCSRFToken($csrfToken)) {
        $errors[] = 'Invalid security token.';
    } else {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            $settings = [
                'site_name' => $_POST['site_name'] ?? '',
                'max_file_upload_size' => $_POST['max_file_upload_size'] ?? '',
                'session_timeout' => $_POST['session_timeout'] ?? '',
                'enable_registration' => isset($_POST['enable_registration']) ? 'true' : 'false',
                'maintenance_mode' => isset($_POST['maintenance_mode']) ? 'true' : 'false',
                'report_retention_days' => $_POST['report_retention_days'] ?? '',
                'max_login_attempts' => $_POST['max_login_attempts'] ?? '',
                'lockout_duration' => $_POST['lockout_duration'] ?? ''
            ];
            
            foreach ($settings as $key => $value) {
                $stmt = $conn->prepare("
                    INSERT INTO system_settings (setting_key, setting_value, updated_by) 
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    setting_value = VALUES(setting_value), 
                    updated_by = VALUES(updated_by),
                    updated_at = CURRENT_TIMESTAMP
                ");
                $stmt->execute([$key, $value, $_SESSION['user_id']]);
            }
            
            $success = 'Settings updated successfully.';
            logAudit('system_settings_updated', 'system_settings', null);
            
        } catch (Exception $e) {
            $errors[] = 'Failed to update settings: ' . $e->getMessage();
            error_log("Settings update error: " . $e->getMessage());
        }
    }
}

// Load current settings
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT setting_key, setting_value FROM system_settings");
    $stmt->execute();
    $settingsData = $stmt->fetchAll();
    
    $settings = [];
    foreach ($settingsData as $setting) {
        $settings[$setting['setting_key']] = $setting['setting_value'];
    }
    
    // Set defaults if not found
    $defaults = [
        'site_name' => APP_NAME,
        'max_file_upload_size' => '5242880',
        'session_timeout' => '3600',
        'enable_registration' => 'true',
        'maintenance_mode' => 'false',
        'report_retention_days' => '365',
        'max_login_attempts' => '5',
        'lockout_duration' => '900'
    ];
    
    foreach ($defaults as $key => $value) {
        if (!isset($settings[$key])) {
            $settings[$key] = $value;
        }
    }
    
} catch (Exception $e) {
    $settings = [];
    error_log("Settings load error: " . $e->getMessage());
}

$pageTitle = 'System Settings - ' . APP_NAME;
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
                            <i class="fas fa-tachometer-alt"></i> Main Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-cog"></i> Admin Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users"></i> User Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="security.php">
                            <i class="fas fa-shield-alt"></i> Security Logs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="settings.php">
                            <i class="fas fa-cogs"></i> System Settings
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">System Settings</h1>
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

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                
                <!-- General Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-cog"></i> General Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_name" class="form-label">Site Name</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" 
                                           value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="report_retention_days" class="form-label">Report Retention (Days)</label>
                                    <input type="number" class="form-control" id="report_retention_days" name="report_retention_days" 
                                           value="<?php echo htmlspecialchars($settings['report_retention_days'] ?? ''); ?>" 
                                           min="1" max="3650">
                                    <div class="form-text">How long to keep generated reports</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="enable_registration" name="enable_registration"
                                           <?php echo ($settings['enable_registration'] ?? '') === 'true' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="enable_registration">
                                        Enable User Registration
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode"
                                           <?php echo ($settings['maintenance_mode'] ?? '') === 'true' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="maintenance_mode">
                                        Maintenance Mode
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-shield-alt"></i> Security Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="session_timeout" class="form-label">Session Timeout (Seconds)</label>
                                    <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                                           value="<?php echo htmlspecialchars($settings['session_timeout'] ?? ''); ?>" 
                                           min="300" max="86400">
                                    <div class="form-text">User session expiry time</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                                    <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" 
                                           value="<?php echo htmlspecialchars($settings['max_login_attempts'] ?? ''); ?>" 
                                           min="3" max="10">
                                    <div class="form-text">Failed attempts before lockout</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lockout_duration" class="form-label">Lockout Duration (Seconds)</label>
                                    <input type="number" class="form-control" id="lockout_duration" name="lockout_duration" 
                                           value="<?php echo htmlspecialchars($settings['lockout_duration'] ?? ''); ?>" 
                                           min="300" max="3600">
                                    <div class="form-text">Account lockout time</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_file_upload_size" class="form-label">Max File Upload Size (Bytes)</label>
                                    <input type="number" class="form-control" id="max_file_upload_size" name="max_file_upload_size" 
                                           value="<?php echo htmlspecialchars($settings['max_file_upload_size'] ?? ''); ?>" 
                                           min="1048576" max="52428800">
                                    <div class="form-text">Maximum file upload size (1MB - 50MB)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> System Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Application Version:</strong></td>
                                        <td><?php echo APP_VERSION; ?></td>
                                    </tr>
                                    <!--<tr>
                                        <td><strong>PHP Version:</strong></td>
                                        <td><?php echo PHP_VERSION; ?></td>
                                    </tr>-->
                                    <tr>
                                        <td><strong>Server Software:</strong></td>
                                        <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Document Root:</strong></td>
                                        <td><code><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></code></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Database:</strong></td>
                                        <td>MySQL/MariaDB</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Memory Limit:</strong></td>
                                        <td><?php echo ini_get('memory_limit'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Upload Max Size:</strong></td>
                                        <td><?php echo ini_get('upload_max_filesize'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Time Zone:</strong></td>
                                        <td><?php echo date_default_timezone_get(); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Directory Permissions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-folder"></i> Directory Permissions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Directory</th>
                                        <th>Exists</th>
                                        <th>Writable</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $directories = [
                                        '../uploads' => 'Uploads Directory',
                                        '../reports' => 'Reports Directory',
                                        '../logs' => 'Logs Directory',
                                        '../config' => 'Config Directory'
                                    ];
                                    
                                    foreach ($directories as $path => $name):
                                        $exists = is_dir($path);
                                        $writable = $exists && is_writable($path);
                                        $status = $exists && $writable ? 'OK' : 'Issue';
                                        $statusClass = $status === 'OK' ? 'text-success' : 'text-danger';
                                    ?>
                                    <tr>
                                        <td><?php echo $name; ?></td>
                                        <td>
                                            <i class="fas fa-<?php echo $exists ? 'check text-success' : 'times text-danger'; ?>"></i>
                                        </td>
                                        <td>
                                            <i class="fas fa-<?php echo $writable ? 'check text-success' : 'times text-danger'; ?>"></i>
                                        </td>
                                        <td class="<?php echo $statusClass; ?>">
                                            <strong><?php echo $status; ?></strong>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
