<?php
/**
 * View Assessment - DMIT Psychometric Test System
 * View detailed assessment information
 */

require_once '../config/config.php';

Security::requireAuth();

$subjectId = $_GET['id'] ?? 0;

// Verify subject exists and belongs to current user
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("
        SELECT s.*, u.first_name as created_by_first, u.last_name as created_by_last
        FROM assessment_subjects s
        LEFT JOIN users u ON s.user_id = u.id
        WHERE s.id = ? AND (s.user_id = ? OR ? = 'admin')
    ");
    $stmt->execute([$subjectId, $_SESSION['user_id'], $_SESSION['user_role']]);
    $subject = $stmt->fetch();
    
    if (!$subject) {
        redirect('list.php', 'Assessment subject not found.', 'error');
    }
    
    // Get fingerprint data
    $stmt = $conn->prepare("SELECT * FROM fingerprint_data WHERE subject_id = ? ORDER BY finger_position");
    $stmt->execute([$subjectId]);
    $fingerprintData = $stmt->fetchAll();
    
    // Get analysis data
    $stmt = $conn->prepare("
        SELECT i.*, p.primary_type, p.secondary_type, 
               b.left_brain_percent, b.right_brain_percent, b.dominance_type,
               l.primary_style, q.iq_score, q.eq_score, q.cq_score, q.aq_score
        FROM intelligence_scores i
        LEFT JOIN personality_profiles p ON i.subject_id = p.subject_id
        LEFT JOIN brain_dominance b ON i.subject_id = b.subject_id
        LEFT JOIN learning_styles l ON i.subject_id = l.subject_id
        LEFT JOIN quotient_scores q ON i.subject_id = q.subject_id
        WHERE i.subject_id = ?
    ");
    $stmt->execute([$subjectId]);
    $analysisData = $stmt->fetch();
    
    // Get reports
    $stmt = $conn->prepare("SELECT * FROM assessment_reports WHERE subject_id = ? ORDER BY generated_at DESC");
    $stmt->execute([$subjectId]);
    $reports = $stmt->fetchAll();
    
} catch (Exception $e) {
    redirect('list.php', 'Error loading assessment data.', 'error');
}

$pageTitle = 'View Assessment - ' . APP_NAME;
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
                        <a class="nav-link" href="new.php">
                            <i class="fas fa-plus-circle"></i> New Assessment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="list.php">
                            <i class="fas fa-list"></i> View Assessments
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Assessment Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="edit.php?id=<?php echo $subjectId; ?>" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <?php if ($analysisData): ?>
                        <a href="report.php?id=<?php echo $subjectId; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-file-pdf"></i> View Report
                        </a>
                        <?php else: ?>
                        <a href="analysis.php?id=<?php echo $subjectId; ?>" class="btn btn-sm btn-success">
                            <i class="fas fa-brain"></i> Analyze
                        </a>
                        <?php endif; ?>
                    </div>
                    <a href="list.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <!-- Subject Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-user"></i> Subject Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Date of Birth:</strong></td>
                                    <td><?php echo formatDate($subject['date_of_birth'], 'M d, Y'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Age at Assessment:</strong></td>
                                    <td><?php echo $subject['age_at_assessment']; ?> years</td>
                                </tr>
                                <tr>
                                    <td><strong>Gender:</strong></td>
                                    <td><?php echo ucfirst($subject['gender']); ?></td>
                                </tr>
                                <?php if ($subject['parent_name']): ?>
                                <tr>
                                    <td><strong>Parent/Guardian:</strong></td>
                                    <td><?php echo htmlspecialchars($subject['parent_name']); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <?php if ($subject['contact_email']): ?>
                                <tr>
                                    <td><strong>Contact Email:</strong></td>
                                    <td><?php echo htmlspecialchars($subject['contact_email']); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($subject['contact_phone']): ?>
                                <tr>
                                    <td><strong>Contact Phone:</strong></td>
                                    <td><?php echo htmlspecialchars($subject['contact_phone']); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($subject['school_name']): ?>
                                <tr>
                                    <td><strong>School:</strong></td>
                                    <td><?php echo htmlspecialchars($subject['school_name']); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($subject['grade_class']): ?>
                                <tr>
                                    <td><strong>Grade/Class:</strong></td>
                                    <td><?php echo htmlspecialchars($subject['grade_class']); ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td><?php echo htmlspecialchars($subject['created_by_first'] . ' ' . $subject['created_by_last']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Created On:</strong></td>
                                    <td><?php echo formatDate($subject['created_at'], 'M d, Y g:i A'); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fingerprint Data -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-fingerprint"></i> Fingerprint Data (<?php echo count($fingerprintData); ?>/10)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($fingerprintData)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> No fingerprint data collected yet.
                            <a href="<?php echo url('assessments/fingerprint_collection.php?id=' . $subjectId); ?>" class="btn btn-sm btn-warning ms-2">
                                Collect Fingerprints
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Left Hand</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Finger</th>
                                                <th>Pattern</th>
                                                <th>Ridge Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $leftFingers = ['left_thumb' => 'Thumb', 'left_index' => 'Index', 'left_middle' => 'Middle', 'left_ring' => 'Ring', 'left_little' => 'Little'];
                                            foreach ($leftFingers as $position => $name):
                                                $fingerData = array_filter($fingerprintData, function($f) use ($position) {
                                                    return $f['finger_position'] === $position;
                                                });
                                                $fingerData = reset($fingerData);
                                            ?>
                                            <tr>
                                                <td><?php echo $name; ?></td>
                                                <td>
                                                    <?php if ($fingerData): ?>
                                                        <span class="badge bg-info"><?php echo ucfirst($fingerData['pattern_type']); ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo $fingerData ? $fingerData['ridge_count'] : '-'; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Right Hand</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Finger</th>
                                                <th>Pattern</th>
                                                <th>Ridge Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $rightFingers = ['right_thumb' => 'Thumb', 'right_index' => 'Index', 'right_middle' => 'Middle', 'right_ring' => 'Ring', 'right_little' => 'Little'];
                                            foreach ($rightFingers as $position => $name):
                                                $fingerData = array_filter($fingerprintData, function($f) use ($position) {
                                                    return $f['finger_position'] === $position;
                                                });
                                                $fingerData = reset($fingerData);
                                            ?>
                                            <tr>
                                                <td><?php echo $name; ?></td>
                                                <td>
                                                    <?php if ($fingerData): ?>
                                                        <span class="badge bg-info"><?php echo ucfirst($fingerData['pattern_type']); ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo $fingerData ? $fingerData['ridge_count'] : '-'; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (count($fingerprintData) < 10): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> 
                            <?php echo (10 - count($fingerprintData)); ?> more fingerprint(s) needed for complete analysis.
                            <a href="<?php echo url('assessments/fingerprint_collection.php?id=' . $subjectId); ?>" class="btn btn-sm btn-info ms-2">
                                Complete Collection
                            </a>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Analysis Results -->
            <?php if ($analysisData): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-brain"></i> Analysis Results</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Multiple Intelligence</h6>
                            <p><strong>Dominant:</strong> 
                                <span class="badge bg-primary">
                                    <?php echo ucfirst(str_replace('_', ' ', $analysisData['dominant_intelligence'])); ?>
                                </span>
                            </p>
                            
                            <h6>Personality Profile</h6>
                            <p><strong>Primary Type:</strong> 
                                <span class="badge bg-success"><?php echo ucfirst($analysisData['primary_type']); ?></span>
                                <?php if ($analysisData['secondary_type']): ?>
                                    <span class="badge bg-secondary"><?php echo ucfirst($analysisData['secondary_type']); ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Brain Dominance</h6>
                            <p>
                                <strong>Left:</strong> <?php echo $analysisData['left_brain_percent']; ?>% | 
                                <strong>Right:</strong> <?php echo $analysisData['right_brain_percent']; ?>%
                                <br><span class="badge bg-info"><?php echo ucfirst($analysisData['dominance_type']); ?> Dominant</span>
                            </p>
                            
                            <h6>Quotient Scores</h6>
                            <p>
                                <strong>IQ:</strong> <?php echo $analysisData['iq_score']; ?> | 
                                <strong>EQ:</strong> <?php echo $analysisData['eq_score']; ?> | 
                                <strong>CQ:</strong> <?php echo $analysisData['cq_score']; ?> | 
                                <strong>AQ:</strong> <?php echo $analysisData['aq_score']; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="analysis.php?id=<?php echo $subjectId; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-chart-line"></i> View Detailed Analysis
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Reports -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-file-pdf"></i> Generated Reports</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($reports)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No reports generated yet.
                            <?php if ($analysisData): ?>
                                <a href="report.php?id=<?php echo $subjectId; ?>" class="btn btn-sm btn-primary ms-2">
                                    Generate Report
                                </a>
                            <?php else: ?>
                                Complete analysis first to generate reports.
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Report Type</th>
                                        <th>Status</th>
                                        <th>Generated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reports as $report): ?>
                                    <tr>
                                        <td><?php echo ucfirst($report['report_type']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $report['report_status'] === 'completed' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($report['report_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($report['generated_at'], 'M d, Y g:i A'); ?></td>
                                        <td>
                                            <?php if ($report['report_status'] === 'completed'): ?>
                                                <a href="report.php?id=<?php echo $subjectId; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
