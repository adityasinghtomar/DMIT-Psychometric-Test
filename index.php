<?php
/**
 * DMIT Psychometric Test System - Main Entry Point
 * Secure and comprehensive psychometric assessment platform
 */

require_once 'config/config.php';

// Check if user is logged in
if (!Security::isAuthenticated()) {
    header('Location: auth/login.php');
    exit();
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
                    <li class="nav-item">
                        <a class="nav-link" href="reports/list.php">
                            <i class="fas fa-file-pdf"></i> Reports
                        </a>
                    </li>
                    
                    <?php if (Security::hasRole('counselor')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="counselor/dashboard.php">
                            <i class="fas fa-user-md"></i> Counselor Panel
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (Security::hasRole('admin')): ?>
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
                        <a class="nav-link" href="auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="assessments/new.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-plus"></i> New Assessment
                        </a>
                    </div>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <!-- Dashboard Statistics -->
            <div class="row mb-4">
                <?php
                try {
                    $database = new Database();
                    $conn = $database->getConnection();
                    
                    // Get statistics
                    $userId = $_SESSION['user_id'];
                    $userRole = $_SESSION['user_role'];
                    
                    // Total assessments
                    if ($userRole === 'admin') {
                        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM assessment_subjects");
                        $stmt->execute();
                    } else {
                        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM assessment_subjects WHERE user_id = ?");
                        $stmt->execute([$userId]);
                    }
                    $totalAssessments = $stmt->fetch()['total'];
                    
                    // Completed reports
                    if ($userRole === 'admin') {
                        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM assessment_reports WHERE report_status = 'completed'");
                        $stmt->execute();
                    } else {
                        $stmt = $conn->prepare("
                            SELECT COUNT(*) as total 
                            FROM assessment_reports ar 
                            JOIN assessment_subjects s ON ar.subject_id = s.id 
                            WHERE s.user_id = ? AND ar.report_status = 'completed'
                        ");
                        $stmt->execute([$userId]);
                    }
                    $completedReports = $stmt->fetch()['total'];
                    
                    // Pending assessments
                    if ($userRole === 'admin') {
                        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM assessment_reports WHERE report_status = 'pending'");
                        $stmt->execute();
                    } else {
                        $stmt = $conn->prepare("
                            SELECT COUNT(*) as total 
                            FROM assessment_reports ar 
                            JOIN assessment_subjects s ON ar.subject_id = s.id 
                            WHERE s.user_id = ? AND ar.report_status = 'pending'
                        ");
                        $stmt->execute([$userId]);
                    }
                    $pendingReports = $stmt->fetch()['total'];
                    
                } catch (Exception $e) {
                    $totalAssessments = $completedReports = $pendingReports = 0;
                    error_log("Dashboard stats error: " . $e->getMessage());
                }
                ?>
                
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Total Assessments</div>
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $totalAssessments; ?></h4>
                            <p class="card-text">Subjects assessed</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Completed Reports</div>
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $completedReports; ?></h4>
                            <p class="card-text">Reports generated</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">Pending Reports</div>
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $pendingReports; ?></h4>
                            <p class="card-text">Awaiting processing</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Assessments -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Recent Assessments</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            try {
                                if ($userRole === 'admin') {
                                    $stmt = $conn->prepare("
                                        SELECT s.*, u.first_name, u.last_name 
                                        FROM assessment_subjects s 
                                        JOIN users u ON s.user_id = u.id 
                                        ORDER BY s.created_at DESC 
                                        LIMIT 10
                                    ");
                                    $stmt->execute();
                                } else {
                                    $stmt = $conn->prepare("
                                        SELECT * FROM assessment_subjects 
                                        WHERE user_id = ? 
                                        ORDER BY created_at DESC 
                                        LIMIT 10
                                    ");
                                    $stmt->execute([$userId]);
                                }
                                
                                $recentAssessments = $stmt->fetchAll();
                                
                                if (empty($recentAssessments)): ?>
                                    <p class="text-muted">No assessments found. <a href="assessments/new.php">Create your first assessment</a></p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Subject Name</th>
                                                    <th>Age</th>
                                                    <th>Gender</th>
                                                    <?php if ($userRole === 'admin'): ?>
                                                    <th>Created By</th>
                                                    <?php endif; ?>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentAssessments as $assessment): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($assessment['subject_name']); ?></td>
                                                    <td><?php echo $assessment['age_at_assessment']; ?></td>
                                                    <td><?php echo ucfirst($assessment['gender']); ?></td>
                                                    <?php if ($userRole === 'admin'): ?>
                                                    <td><?php echo htmlspecialchars($assessment['first_name'] . ' ' . $assessment['last_name']); ?></td>
                                                    <?php endif; ?>
                                                    <td><?php echo formatDate($assessment['created_at'], 'M d, Y'); ?></td>
                                                    <td>
                                                        <a href="assessments/view.php?id=<?php echo $assessment['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                                        <a href="assessments/edit.php?id=<?php echo $assessment['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif;
                                
                            } catch (Exception $e) {
                                echo '<p class="text-danger">Error loading recent assessments.</p>';
                                error_log("Recent assessments error: " . $e->getMessage());
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
