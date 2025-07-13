<?php
/**
 * Main Dashboard - DMIT Psychometric Test System
 * User dashboard with overview and quick actions
 */

require_once 'config/config.php';

// Redirect to login if not authenticated
if (!Security::isAuthenticated()) {
    redirect('auth/login.php');
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $userId = $_SESSION['user_id'];
    $userRole = $_SESSION['user_role'];
    
    // Get user statistics
    $stats = [];
    
    if ($userRole === 'admin') {
        // Admin sees system-wide stats
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM assessment_subjects");
        $stmt->execute();
        $stats['total_assessments'] = $stmt->fetch()['count'];
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
        $stmt->execute();
        $stats['total_users'] = $stmt->fetch()['count'];
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM assessment_reports WHERE report_status = 'completed'");
        $stmt->execute();
        $stats['completed_reports'] = $stmt->fetch()['count'];
        
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM security_events 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $stmt->execute();
        $stats['security_events'] = $stmt->fetch()['count'];
        
    } else {
        // Regular users see their own stats
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM assessment_subjects WHERE user_id = ?");
        $stmt->execute([$userId]);
        $stats['my_assessments'] = $stmt->fetch()['count'];
        
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM assessment_subjects s
            JOIN intelligence_scores i ON s.id = i.subject_id
            WHERE s.user_id = ?
        ");
        $stmt->execute([$userId]);
        $stats['completed_analyses'] = $stmt->fetch()['count'];
        
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM assessment_subjects s
            JOIN assessment_reports r ON s.id = r.subject_id
            WHERE s.user_id = ? AND r.report_status = 'completed'
        ");
        $stmt->execute([$userId]);
        $stats['my_reports'] = $stmt->fetch()['count'];
        
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM assessment_subjects s
            LEFT JOIN intelligence_scores i ON s.id = i.subject_id
            WHERE s.user_id = ? AND i.id IS NULL
        ");
        $stmt->execute([$userId]);
        $stats['pending_analyses'] = $stmt->fetch()['count'];
    }
    
    // Get recent assessments
    $recentQuery = $userRole === 'admin' ? 
        "SELECT s.*, u.first_name, u.last_name,
                (SELECT COUNT(*) FROM intelligence_scores i WHERE i.subject_id = s.id) as analysis_complete
         FROM assessment_subjects s
         LEFT JOIN users u ON s.user_id = u.id
         ORDER BY s.created_at DESC LIMIT 5" :
        "SELECT s.*, 
                (SELECT COUNT(*) FROM intelligence_scores i WHERE i.subject_id = s.id) as analysis_complete
         FROM assessment_subjects s
         WHERE s.user_id = ?
         ORDER BY s.created_at DESC LIMIT 5";
    
    $stmt = $conn->prepare($recentQuery);
    if ($userRole !== 'admin') {
        $stmt->execute([$userId]);
    } else {
        $stmt->execute();
    }
    $recentAssessments = $stmt->fetchAll();
    
} catch (Exception $e) {
    $stats = [];
    $recentAssessments = [];
    error_log("Dashboard error: " . $e->getMessage());
}

$pageTitle = 'Dashboard - ' . APP_NAME;
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="assessments/new.php">
                            <i class="fas fa-plus-circle"></i> New Assessment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="assessments/list.php">
                            <i class="fas fa-list"></i> View Assessments
                        </a>
                    </li>
                    <?php if ($userRole === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/dashboard.php">
                            <i class="fas fa-cog"></i> Admin Panel
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="profile/settings.php">
                            <i class="fas fa-user-cog"></i> Profile Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="help/user_guide.php">
                            <i class="fas fa-question-circle"></i> Help & Support
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="assessments/new.php" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> New Assessment
                        </a>
                    </div>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <?php if ($userRole === 'admin'): ?>
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-header">Total Assessments</div>
                            <div class="card-body">
                                <h4 class="card-title"><?php echo $stats['total_assessments'] ?? 0; ?></h4>
                                <p class="card-text">System-wide assessments</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-header">Active Users</div>
                            <div class="card-body">
                                <h4 class="card-title"><?php echo $stats['total_users'] ?? 0; ?></h4>
                                <p class="card-text">Registered users</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-header">Reports Generated</div>
                            <div class="card-body">
                                <h4 class="card-title"><?php echo $stats['completed_reports'] ?? 0; ?></h4>
                                <p class="card-text">Completed reports</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-header">Security Events (24h)</div>
                            <div class="card-body">
                                <h4 class="card-title"><?php echo $stats['security_events'] ?? 0; ?></h4>
                                <p class="card-text">Recent events</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-header">My Assessments</div>
                            <div class="card-body">
                                <h4 class="card-title"><?php echo $stats['my_assessments'] ?? 0; ?></h4>
                                <p class="card-text">Total assessments</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-header">Completed Analyses</div>
                            <div class="card-body">
                                <h4 class="card-title"><?php echo $stats['completed_analyses'] ?? 0; ?></h4>
                                <p class="card-text">Analyzed assessments</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-header">Generated Reports</div>
                            <div class="card-body">
                                <h4 class="card-title"><?php echo $stats['my_reports'] ?? 0; ?></h4>
                                <p class="card-text">Available reports</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-header">Pending Analyses</div>
                            <div class="card-body">
                                <h4 class="card-title"><?php echo $stats['pending_analyses'] ?? 0; ?></h4>
                                <p class="card-text">Awaiting analysis</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="assessments/new.php" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center">
                                        <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                        <span>Create New Assessment</span>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="assessments/list.php" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center">
                                        <i class="fas fa-list fa-2x mb-2"></i>
                                        <span>View All Assessments</span>
                                    </a>
                                </div>
                                <?php if ($userRole === 'admin'): ?>
                                <div class="col-md-3 mb-3">
                                    <a href="admin/users.php" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <span>Manage Users</span>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="admin/security.php" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center">
                                        <i class="fas fa-shield-alt fa-2x mb-2"></i>
                                        <span>Security Logs</span>
                                    </a>
                                </div>
                                <?php else: ?>
                                <div class="col-md-3 mb-3">
                                    <a href="profile/settings.php" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center">
                                        <i class="fas fa-user-cog fa-2x mb-2"></i>
                                        <span>Profile Settings</span>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="help/user_guide.php" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center">
                                        <i class="fas fa-question-circle fa-2x mb-2"></i>
                                        <span>Help & Support</span>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Assessments -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-clock"></i> Recent Assessments</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentAssessments)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <h5>No Assessments Yet</h5>
                                    <p class="text-muted">Start by creating your first assessment.</p>
                                    <a href="assessments/new.php" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Assessment
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Subject Name</th>
                                                <th>Age</th>
                                                <?php if ($userRole === 'admin'): ?>
                                                <th>Created By</th>
                                                <?php endif; ?>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentAssessments as $assessment): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($assessment['subject_name']); ?></strong>
                                                </td>
                                                <td><?php echo $assessment['age_at_assessment']; ?> years</td>
                                                <?php if ($userRole === 'admin'): ?>
                                                <td><?php echo htmlspecialchars($assessment['first_name'] . ' ' . $assessment['last_name']); ?></td>
                                                <?php endif; ?>
                                                <td>
                                                    <?php if ($assessment['analysis_complete'] > 0): ?>
                                                        <span class="badge bg-success">Analysis Complete</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Pending Analysis</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo formatDate($assessment['created_at'], 'M d, Y'); ?></td>
                                                <td>
                                                    <?php if ($assessment['analysis_complete'] > 0): ?>
                                                        <a href="assessments/report.php?id=<?php echo $assessment['id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-file-pdf"></i> Report
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="assessments/analysis.php?id=<?php echo $assessment['id']; ?>" 
                                                           class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-brain"></i> Analyze
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <a href="assessments/list.php" class="btn btn-outline-primary">
                                        <i class="fas fa-list"></i> View All Assessments
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
