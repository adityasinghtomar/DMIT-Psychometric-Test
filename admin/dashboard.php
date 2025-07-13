<?php
/**
 * Admin Dashboard - DMIT Psychometric Test System
 * Administrative overview and system management
 */

require_once '../config/config.php';

Security::requireAuth();
Security::requireRole('admin');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Get system statistics
    $stats = [];
    
    // Total users
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
    $stmt->execute();
    $stats['total_users'] = $stmt->fetch()['count'];
    
    // Total assessments
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM assessment_subjects");
    $stmt->execute();
    $stats['total_assessments'] = $stmt->fetch()['count'];
    
    // Completed reports
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM assessment_reports WHERE report_status = 'completed'");
    $stmt->execute();
    $stats['completed_reports'] = $stmt->fetch()['count'];
    
    // Recent security events
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM security_events 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    $stmt->execute();
    $stats['recent_security_events'] = $stmt->fetch()['count'];
    
    // Recent users (last 7 days)
    $stmt = $conn->prepare("
        SELECT u.*, DATE(u.created_at) as join_date
        FROM users u 
        WHERE u.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ORDER BY u.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recentUsers = $stmt->fetchAll();
    
    // Recent assessments
    $stmt = $conn->prepare("
        SELECT s.*, u.first_name, u.last_name,
               (SELECT COUNT(*) FROM intelligence_scores i WHERE i.subject_id = s.id) as analysis_complete
        FROM assessment_subjects s
        LEFT JOIN users u ON s.user_id = u.id
        ORDER BY s.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recentAssessments = $stmt->fetchAll();
    
    // System health checks
    $systemHealth = [
        'database' => 'healthy',
        'uploads_dir' => is_writable('../uploads') ? 'healthy' : 'warning',
        'reports_dir' => is_writable('../reports') ? 'healthy' : 'warning',
        'logs_dir' => is_writable('../logs') ? 'healthy' : 'warning'
    ];
    
} catch (Exception $e) {
    error_log("Admin dashboard error: " . $e->getMessage());
    $stats = ['total_users' => 0, 'total_assessments' => 0, 'completed_reports' => 0, 'recent_security_events' => 0];
    $recentUsers = [];
    $recentAssessments = [];
    $systemHealth = ['database' => 'error', 'uploads_dir' => 'error', 'reports_dir' => 'error', 'logs_dir' => 'error'];
}

$pageTitle = 'Admin Dashboard - ' . APP_NAME;
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
                        <a class="nav-link active" href="dashboard.php">
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
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cogs"></i> System Settings
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Admin Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <!-- System Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Total Users</div>
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $stats['total_users']; ?></h4>
                            <p class="card-text">Active registered users</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Total Assessments</div>
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $stats['total_assessments']; ?></h4>
                            <p class="card-text">Subjects assessed</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">Completed Reports</div>
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $stats['completed_reports']; ?></h4>
                            <p class="card-text">Reports generated</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">Security Events (24h)</div>
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $stats['recent_security_events']; ?></h4>
                            <p class="card-text">Recent security events</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-heartbeat"></i> System Health</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($systemHealth as $component => $status): ?>
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex align-items-center">
                                        <?php
                                        $iconClass = 'fas fa-check-circle text-success';
                                        if ($status === 'warning') $iconClass = 'fas fa-exclamation-triangle text-warning';
                                        if ($status === 'error') $iconClass = 'fas fa-times-circle text-danger';
                                        ?>
                                        <i class="<?php echo $iconClass; ?> me-2"></i>
                                        <span><?php echo ucfirst(str_replace('_', ' ', $component)); ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row">
                <!-- Recent Users -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-user-plus"></i> Recent Users (Last 7 Days)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentUsers)): ?>
                                <p class="text-muted">No new users in the last 7 days.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Joined</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentUsers as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <span class="badge bg-secondary"><?php echo ucfirst($user['role']); ?></span>
                                                </td>
                                                <td><?php echo formatDate($user['created_at'], 'M d'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Assessments -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-brain"></i> Recent Assessments</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentAssessments)): ?>
                                <p class="text-muted">No recent assessments.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Created By</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentAssessments as $assessment): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($assessment['subject_name']); ?></td>
                                                <td><?php echo htmlspecialchars($assessment['first_name'] . ' ' . $assessment['last_name']); ?></td>
                                                <td>
                                                    <?php if ($assessment['analysis_complete'] > 0): ?>
                                                        <span class="badge bg-success">Complete</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo formatDate($assessment['created_at'], 'M d'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="users.php" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-users"></i><br>
                                        Manage Users
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="../assessments/list.php" class="btn btn-outline-success w-100">
                                        <i class="fas fa-list"></i><br>
                                        View All Assessments
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="security.php" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-shield-alt"></i><br>
                                        Security Logs
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="settings.php" class="btn btn-outline-info w-100">
                                        <i class="fas fa-cogs"></i><br>
                                        System Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
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
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Database:</strong></td>
                                            <td>MySQL/MariaDB</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Session Timeout:</strong></td>
                                            <td><?php echo SESSION_LIFETIME / 60; ?> minutes</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Max File Upload:</strong></td>
                                            <td><?php echo formatFileSize(MAX_FILE_SIZE); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
