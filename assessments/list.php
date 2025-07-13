<?php
/**
 * Assessment List - DMIT Psychometric Test System
 * View and manage all assessments
 */

require_once '../config/config.php';

Security::requireAuth();

$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Build query based on user role
    $whereClause = '';
    $params = [];
    
    if ($_SESSION['user_role'] !== 'admin') {
        $whereClause = 'WHERE s.user_id = ?';
        $params[] = $_SESSION['user_id'];
    }
    
    if (!empty($search)) {
        $whereClause .= ($_SESSION['user_role'] !== 'admin') ? ' AND ' : 'WHERE ';
        $whereClause .= 's.subject_name LIKE ?';
        $params[] = '%' . $search . '%';
    }
    
    // Get total count
    $countQuery = "
        SELECT COUNT(*) as total 
        FROM assessment_subjects s 
        " . ($whereClause ? $whereClause : '');
    
    $stmt = $conn->prepare($countQuery);
    $stmt->execute($params);
    $totalRecords = $stmt->fetch()['total'];
    $totalPages = ceil($totalRecords / $limit);
    
    // Get assessments with analysis status
    $query = "
        SELECT s.*, u.first_name, u.last_name,
               (SELECT COUNT(*) FROM fingerprint_data f WHERE f.subject_id = s.id) as fingerprint_count,
               (SELECT COUNT(*) FROM intelligence_scores i WHERE i.subject_id = s.id) as analysis_complete,
               (SELECT COUNT(*) FROM assessment_reports r WHERE r.subject_id = s.id AND r.report_status = 'completed') as report_ready
        FROM assessment_subjects s
        LEFT JOIN users u ON s.user_id = u.id
        $whereClause
        ORDER BY s.created_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $assessments = $stmt->fetchAll();
    
} catch (Exception $e) {
    $assessments = [];
    $totalPages = 0;
    error_log("Assessment list error: " . $e->getMessage());
}

$pageTitle = 'Assessment List - ' . APP_NAME;
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('index.php'); ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('assessments/new.php'); ?>">
                            <i class="fas fa-plus-circle"></i> New Assessment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo url('assessments/list.php'); ?>">
                            <i class="fas fa-list"></i> View Assessments
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Assessment List</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="new.php" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> New Assessment
                    </a>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <!-- Search and Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search by Subject Name</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Enter subject name...">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="list.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Assessment Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $totalRecords; ?></h5>
                            <p class="card-text">Total Assessments</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php 
                                $completed = array_filter($assessments, function($a) { return $a['analysis_complete'] > 0; });
                                echo count($completed);
                                ?>
                            </h5>
                            <p class="card-text">Analyzed</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php 
                                $withReports = array_filter($assessments, function($a) { return $a['report_ready'] > 0; });
                                echo count($withReports);
                                ?>
                            </h5>
                            <p class="card-text">Reports Ready</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php 
                                $pending = array_filter($assessments, function($a) { return $a['analysis_complete'] == 0; });
                                echo count($pending);
                                ?>
                            </h5>
                            <p class="card-text">Pending</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Table -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-table"></i> Assessments</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($assessments)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5>No Assessments Found</h5>
                            <p class="text-muted">
                                <?php if (empty($search)): ?>
                                    Start by creating your first assessment.
                                <?php else: ?>
                                    No assessments match your search criteria.
                                <?php endif; ?>
                            </p>
                            <a href="new.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create New Assessment
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Subject Name</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                        <th>Created By</th>
                                        <?php endif; ?>
                                        <th>Date Created</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assessments as $assessment): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($assessment['subject_name']); ?></strong>
                                            <?php if ($assessment['school_name']): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($assessment['school_name']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $assessment['age_at_assessment']; ?> years</td>
                                        <td><?php echo ucfirst($assessment['gender']); ?></td>
                                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                        <td><?php echo htmlspecialchars($assessment['first_name'] . ' ' . $assessment['last_name']); ?></td>
                                        <?php endif; ?>
                                        <td><?php echo formatDate($assessment['created_at'], 'M d, Y'); ?></td>
                                        <td>
                                            <?php if ($assessment['report_ready'] > 0): ?>
                                                <span class="badge bg-success">Report Ready</span>
                                            <?php elseif ($assessment['analysis_complete'] > 0): ?>
                                                <span class="badge bg-info">Analysis Complete</span>
                                            <?php elseif ($assessment['fingerprint_count'] >= 8): ?>
                                                <span class="badge bg-warning">Ready for Analysis</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Data Collection</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <?php 
                                                $progress = 0;
                                                if ($assessment['fingerprint_count'] >= 8) $progress = 33;
                                                if ($assessment['analysis_complete'] > 0) $progress = 66;
                                                if ($assessment['report_ready'] > 0) $progress = 100;
                                                
                                                $progressClass = 'bg-secondary';
                                                if ($progress >= 33) $progressClass = 'bg-warning';
                                                if ($progress >= 66) $progressClass = 'bg-info';
                                                if ($progress >= 100) $progressClass = 'bg-success';
                                                ?>
                                                <div class="progress-bar <?php echo $progressClass; ?>" 
                                                     style="width: <?php echo $progress; ?>%">
                                                    <?php echo $progress; ?>%
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                Fingerprints: <?php echo $assessment['fingerprint_count']; ?>/10
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if ($assessment['fingerprint_count'] < 8): ?>
                                                    <a href="<?php echo url('assessments/fingerprint_collection.php?id=' . $assessment['id']); ?>"
                                                       class="btn btn-sm btn-outline-primary" title="Collect Fingerprints">
                                                        <i class="fas fa-fingerprint"></i>
                                                    </a>
                                                <?php elseif ($assessment['analysis_complete'] == 0): ?>
                                                    <a href="analysis.php?id=<?php echo $assessment['id']; ?>" 
                                                       class="btn btn-sm btn-outline-success" title="Perform Analysis">
                                                        <i class="fas fa-brain"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="analysis.php?id=<?php echo $assessment['id']; ?>" 
                                                       class="btn btn-sm btn-outline-info" title="View Analysis">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($assessment['report_ready'] > 0): ?>
                                                    <a href="report.php?id=<?php echo $assessment['id']; ?>" 
                                                       class="btn btn-sm btn-outline-success" title="View Report">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="view.php?id=<?php echo $assessment['id']; ?>" 
                                                   class="btn btn-sm btn-outline-secondary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <a href="edit.php?id=<?php echo $assessment['id']; ?>" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="d-flex justify-content-center mt-4">
                                <?php 
                                $searchParam = !empty($search) ? ['search' => $search] : [];
                                echo generatePagination($page, $totalPages, 'list.php', $searchParam); 
                                ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
