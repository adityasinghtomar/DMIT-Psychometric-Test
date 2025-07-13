<?php
/**
 * Assessment Report - DMIT Psychometric Test System
 * Generate and display comprehensive assessment reports
 */

require_once '../config/config.php';

Security::requireAuth();

$subjectId = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? 'view';
$errors = [];
$reportData = null;

// Verify subject exists and belongs to current user
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("
        SELECT * FROM assessment_subjects 
        WHERE id = ? AND (user_id = ? OR ? = 'admin')
    ");
    $stmt->execute([$subjectId, $_SESSION['user_id'], $_SESSION['user_role']]);
    $subject = $stmt->fetch();
    
    if (!$subject) {
        redirect('list.php', 'Assessment subject not found.', 'error');
    }
    
} catch (Exception $e) {
    redirect('list.php', 'Error loading assessment subject.', 'error');
}

// Handle report generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_report'])) {
    $csrfToken = $_POST['csrf_token'] ?? '';
    $reportType = $_POST['report_type'] ?? 'standard';
    
    if (!Security::verifyCSRFToken($csrfToken)) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        try {
            $reportGenerator = new ReportGenerator($database);
            $result = $reportGenerator->generateReport($subjectId, $reportType);
            
            if ($result['success']) {
                // Log audit
                logAudit('report_generated', 'assessment_reports', $result['report_id']);
                
                redirect("report.php?id=$subjectId&action=view", 
                        'Report generated successfully.', 
                        'success');
            } else {
                $errors[] = $result['error'] ?? 'Report generation failed. Please try again.';
            }
            
        } catch (Exception $e) {
            $errors[] = 'Report generation failed: ' . $e->getMessage();
            error_log("Report generation error: " . $e->getMessage());
        }
    }
}

// Load existing report
try {
    $stmt = $conn->prepare("
        SELECT * FROM assessment_reports 
        WHERE subject_id = ? AND report_status = 'completed'
        ORDER BY generated_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$subjectId]);
    $reportData = $stmt->fetch();
    
    // Check if analysis is complete
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM intelligence_scores WHERE subject_id = ?");
    $stmt->execute([$subjectId]);
    $analysisComplete = $stmt->fetch()['count'] > 0;
    
} catch (Exception $e) {
    error_log("Error loading report data: " . $e->getMessage());
    $reportData = null;
    $analysisComplete = false;
}

// Handle different actions
if ($action === 'print' && $reportData) {
    // Display print-friendly version
    echo $reportData['report_data'];
    exit();
}

if ($action === 'download' && $reportData) {
    // For now, we'll use the HTML content
    // In production, you might want to use a library like wkhtmltopdf or Puppeteer
    $filename = 'DMIT_Report_' . $subject['subject_name'] . '_' . date('Y-m-d') . '.html';
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $reportData['report_data'];
    exit();
}

$pageTitle = 'Assessment Report - ' . APP_NAME;
$disableRightClick = true; // Disable right-click for security
include '../includes/header.php';
?>

<!-- Fix header overlap with important declarations -->
<style>
    body {
        padding-top: 70px !important; /* Account for fixed navbar height */
    }
    .container-fluid {
        margin-top: 0 !important;
        padding-top: 20px !important;
    }
    .sidebar {
        top: 70px !important; /* Position sidebar below navbar */
        height: calc(100vh - 70px) !important;
        overflow-y: auto !important;
        position: fixed !important;
    }
    .main-content {
        margin-top: 0 !important;
        padding-top: 20px !important;
        min-height: calc(100vh - 156px) !important;
        padding-bottom: 100px !important;
    }
    .navbar {
        z-index: 1050 !important;
    }
    @media (max-width: 767.98px) {
        body {
            padding-top: 70px !important;
        }
        .sidebar {
            position: relative !important;
            top: 0 !important;
            height: auto !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
    }
</style>

<div class="container-fluid" style="margin-top: 0 !important; padding-top: 20px !important;">
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
                        <a class="nav-link" href="list.php">
                            <i class="fas fa-list"></i> View Assessments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="list.php">
                            <i class="fas fa-file-pdf"></i> Reports
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content" style="margin-top: 0 !important; padding-top: 30px !important;">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Assessment Report</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="analysis.php?id=<?php echo $subjectId; ?>" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="fas fa-chart-line"></i> View Analysis
                    </a>
                    <?php if ($reportData): ?>
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-primary" onclick="printReport()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <a href="report.php?id=<?php echo $subjectId; ?>&action=download" class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> Report Generation Errors:</h5>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Subject Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-user"></i> Subject Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3"><strong>Name:</strong> <?php echo htmlspecialchars($subject['subject_name']); ?></div>
                        <div class="col-md-3"><strong>Age:</strong> <?php echo $subject['age_at_assessment']; ?> years</div>
                        <div class="col-md-3"><strong>Gender:</strong> <?php echo ucfirst($subject['gender']); ?></div>
                        <div class="col-md-3"><strong>Date:</strong> <?php echo formatDate($subject['created_at'], 'M d, Y'); ?></div>
                    </div>
                </div>
            </div>

            <?php if (!$analysisComplete): ?>
                <!-- Analysis Required -->
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Analysis Required</h5>
                    <p>Assessment analysis must be completed before generating a report.</p>
                    <a href="analysis.php?id=<?php echo $subjectId; ?>" class="btn btn-warning">
                        <i class="fas fa-brain"></i> Complete Analysis First
                    </a>
                </div>
            <?php elseif (!$reportData): ?>
                <!-- Generate Report -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-file-pdf"></i> Generate Assessment Report</h5>
                    </div>
                    <div class="card-body">
                        <p>Analysis is complete. Generate a comprehensive DMIT assessment report.</p>
                        
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                            
                            <div class="mb-3">
                                <label for="report_type" class="form-label">Report Type</label>
                                <select class="form-select" id="report_type" name="report_type">
                                    <option value="basic">Basic Report</option>
                                    <option value="standard" selected>Standard Report</option>
                                    <option value="premium">Premium Report</option>
                                </select>
                                <div class="form-text">
                                    Standard report includes all analysis sections with detailed explanations.
                                </div>
                            </div>
                            
                            <button type="submit" name="generate_report" class="btn btn-primary btn-lg" id="generateBtn">
                                <i class="fas fa-file-pdf"></i> Generate Report
                            </button>
                        </form>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Report will include multiple intelligence analysis, personality profile, brain dominance, 
                                learning styles, quotient scores, and career recommendations.
                            </small>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Display Report -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-file-pdf"></i> DMIT Assessment Report</h5>
                        <div>
                            <small class="text-muted">
                                Generated: <?php echo formatDate($reportData['generated_at'], 'M d, Y g:i A'); ?>
                            </small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Report Content -->
                        <div id="reportContent" style="background: white;">
                            <?php echo $reportData['report_data']; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Report Type: <strong><?php echo ucfirst($reportData['report_type']); ?></strong> |
                                    Status: <strong><?php echo ucfirst($reportData['report_status']); ?></strong>
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="btn-group" role="group">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                        <input type="hidden" name="report_type" value="<?php echo $reportData['report_type']; ?>">
                                        <button type="submit" name="generate_report" class="btn btn-sm btn-outline-secondary" title="Regenerate Report">
                                            <i class="fas fa-redo"></i> Regenerate
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="printReport()" title="Print Report">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                    <a href="report.php?id=<?php echo $subjectId; ?>&action=download" class="btn btn-sm btn-success" title="Download Report">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
// Print report function
function printReport() {
    const printWindow = window.open('report.php?id=<?php echo $subjectId; ?>&action=print', '_blank');
    printWindow.onload = function() {
        printWindow.print();
    };
}

// Generate report button loading state
document.getElementById('generateBtn')?.addEventListener('click', function() {
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Generating Report...';
});

// Disable text selection for security
document.addEventListener('selectstart', function(e) {
    e.preventDefault();
});

// Disable drag and drop
document.addEventListener('dragstart', function(e) {
    e.preventDefault();
});

// Force fix header overlap on page load
document.addEventListener('DOMContentLoaded', function() {
    // Force scroll to top to ensure proper positioning
    window.scrollTo(0, 0);

    // Apply header fix styles
    document.body.style.paddingTop = '70px';
    document.body.style.marginTop = '0';

    const containerFluid = document.querySelector('.container-fluid');
    if (containerFluid) {
        containerFluid.style.marginTop = '0';
        containerFluid.style.paddingTop = '20px';
    }

    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.style.marginTop = '0';
        mainContent.style.paddingTop = '30px';
    }
});
</script>

<!-- Final CSS override to ensure header fix -->
<style>
    body {
        padding-top: 70px !important;
        margin-top: 0 !important;
    }
    .container-fluid {
        margin-top: 0 !important;
        padding-top: 20px !important;
    }
    .main-content {
        margin-top: 0 !important;
        padding-top: 30px !important;
    }
    .navbar {
        z-index: 1050 !important;
    }
    .sidebar {
        top: 70px !important;
        height: calc(100vh - 70px) !important;
    }
</style>

<?php include '../includes/footer.php'; ?>
