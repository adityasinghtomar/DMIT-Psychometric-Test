<?php
/**
 * Report Generation Debug - DMIT Psychometric Test System
 * Debug the report generation process
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Increase timeout
set_time_limit(300);
ini_set('max_execution_time', 300);

require_once 'config/config.php';

Security::requireAuth();

$pageTitle = 'Report Generation Debug - ' . APP_NAME;

// Get subject ID
$subjectId = $_GET['id'] ?? 2; // Default to ID 2

$debugLog = [];
$reportResult = null;
$reportError = null;

// Add debug logging function
function debugLog($message) {
    global $debugLog;
    $debugLog[] = date('H:i:s') . " - " . $message;
    error_log("REPORT DEBUG: " . $message);
}

debugLog("Page loaded, subject ID: $subjectId");

// Handle form submission with detailed logging
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_report'])) {
    debugLog("POST request received");
    
    // Output debug info immediately
    echo "<!DOCTYPE html><html><head><title>Report Generation Debug</title></head><body>";
    echo "<h1>Report Generation Debug Output</h1>";
    echo "<div style='font-family: monospace; background: #f8f9fa; padding: 20px; margin: 20px;'>";
    
    debugLog("Starting report generation process");
    echo "<p>✓ Form submitted successfully</p>";
    flush();
    
    $csrfToken = $_POST['csrf_token'] ?? '';
    $reportType = $_POST['report_type'] ?? 'standard';
    debugLog("CSRF token present: " . ($csrfToken ? 'Yes' : 'No'));
    debugLog("Report type: $reportType");
    
    if (!Security::verifyCSRFToken($csrfToken)) {
        debugLog("CSRF verification failed");
        echo "<p style='color: red;'>✗ CSRF token verification failed</p>";
        $reportError = 'Invalid security token';
    } else {
        debugLog("CSRF verification passed");
        echo "<p>✓ CSRF token verified</p>";
        flush();
        
        try {
            debugLog("Creating database connection");
            echo "<p>✓ Creating database connection...</p>";
            flush();
            
            $database = new Database();
            $conn = $database->getConnection();
            
            debugLog("Database connected");
            echo "<p>✓ Database connected</p>";
            flush();
            
            // Check if assessment_reports table exists
            debugLog("Checking assessment_reports table");
            echo "<p>✓ Checking assessment_reports table...</p>";
            flush();
            
            try {
                $stmt = $conn->query("SHOW TABLES LIKE 'assessment_reports'");
                $tableExists = $stmt->rowCount() > 0;
                debugLog("assessment_reports table exists: " . ($tableExists ? 'Yes' : 'No'));
                echo "<p>✓ assessment_reports table: " . ($tableExists ? 'Exists' : 'Missing') . "</p>";
                flush();
                
                if (!$tableExists) {
                    throw new Exception("assessment_reports table does not exist");
                }
            } catch (Exception $e) {
                debugLog("Error checking table: " . $e->getMessage());
                echo "<p style='color: red;'>✗ Error checking table: " . htmlspecialchars($e->getMessage()) . "</p>";
                throw $e;
            }
            
            debugLog("Creating ReportGenerator");
            echo "<p>✓ Creating ReportGenerator...</p>";
            flush();
            
            $reportGenerator = new ReportGenerator($database);
            
            debugLog("ReportGenerator created");
            echo "<p>✓ ReportGenerator created</p>";
            flush();
            
            debugLog("Starting report generation for subject $subjectId");
            echo "<p>✓ Starting report generation for subject $subjectId...</p>";
            flush();
            
            $startTime = microtime(true);
            $result = $reportGenerator->generateReport($subjectId, $reportType);
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);
            
            debugLog("Report generation completed in {$executionTime} seconds");
            echo "<p>✓ Report generation completed in {$executionTime} seconds</p>";
            flush();
            
            if ($result['success']) {
                debugLog("Report generation successful");
                echo "<p style='color: green;'>✓ Report generation successful!</p>";
                echo "<p>✓ Report ID: " . $result['report_id'] . "</p>";
                echo "<p>✓ Subject: " . htmlspecialchars($result['subject_name']) . "</p>";
                echo "<p>✓ HTML content generated (" . strlen($result['html_content']) . " characters)</p>";
                flush();
                
                debugLog("Report saved successfully");
                echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
                echo "<h3>Report Generated Successfully!</h3>";
                echo "<p><strong>Report ID:</strong> " . $result['report_id'] . "</p>";
                echo "<p><strong>Subject:</strong> " . htmlspecialchars($result['subject_name']) . "</p>";
                echo "<p><strong>Report Type:</strong> " . ucfirst($reportType) . "</p>";
                echo "<p><a href='assessments/report.php?id=$subjectId&action=view' class='btn btn-primary'>View Report</a></p>";
                echo "</div>";
                
                $reportResult = $result;
                
            } else {
                debugLog("Report generation failed: " . ($result['error'] ?? 'Unknown error'));
                echo "<p style='color: red;'>✗ Report generation failed: " . htmlspecialchars($result['error'] ?? 'Unknown error') . "</p>";
                $reportError = $result['error'] ?? 'Report generation failed';
            }
            
        } catch (Exception $e) {
            debugLog("Exception caught: " . $e->getMessage());
            echo "<p style='color: red;'>✗ Exception: " . htmlspecialchars($e->getMessage()) . "</p>";
            $reportError = 'Report generation failed: ' . $e->getMessage();
            error_log("Report generation error: " . $e->getMessage());
        }
    }
    
    echo "</div>";
    echo "<h2>Debug Log:</h2>";
    echo "<pre style='background: #f8f9fa; padding: 15px;'>";
    foreach ($debugLog as $log) {
        echo htmlspecialchars($log) . "\n";
    }
    echo "</pre>";
    
    echo "<p><a href='report_generation_debug.php?id=$subjectId'>← Back to Test Page</a></p>";
    echo "</body></html>";
    exit();
}

// Get subject info for display
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM assessment_subjects WHERE id = ?");
    $stmt->execute([$subjectId]);
    $subject = $stmt->fetch();
    
    if (!$subject) {
        debugLog("Subject not found");
        redirect('assessments/list.php', 'Subject not found.', 'error');
    }
    
    // Check if analysis is complete
    $stmt = $conn->prepare("SELECT COUNT(*) FROM intelligence_scores WHERE subject_id = ?");
    $stmt->execute([$subjectId]);
    $analysisComplete = $stmt->fetchColumn() > 0;
    
    debugLog("Subject loaded: " . $subject['subject_name'] . ", Analysis complete: " . ($analysisComplete ? 'Yes' : 'No'));
    
} catch (Exception $e) {
    debugLog("Error loading subject: " . $e->getMessage());
    redirect('assessments/list.php', 'Error loading assessment subject.', 'error');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <h1><i class="fas fa-file-pdf"></i> Report Generation Debug</h1>
        <p class="lead">Debug the report generation process with detailed logging</p>

        <!-- Subject Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-user"></i> Test Subject</h5>
            </div>
            <div class="card-body">
                <ul>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($subject['subject_name']); ?></li>
                    <li><strong>ID:</strong> <?php echo $subject['id']; ?></li>
                    <li><strong>Age:</strong> <?php echo $subject['age_at_assessment']; ?> years</li>
                    <li><strong>Analysis Complete:</strong> 
                        <span class="badge bg-<?php echo $analysisComplete ? 'success' : 'warning'; ?>">
                            <?php echo $analysisComplete ? 'Yes' : 'No'; ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Report Generation Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-file-pdf"></i> Test Report Generation</h5>
            </div>
            <div class="card-body">
                <?php if ($analysisComplete): ?>
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> This is a debug version of report generation</h6>
                    <p class="mb-0">It will show detailed step-by-step progress instead of redirecting, so we can see exactly what happens.</p>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="report_type" class="form-label">Report Type</label>
                        <select class="form-select" id="report_type" name="report_type">
                            <option value="basic">Basic Report</option>
                            <option value="standard" selected>Standard Report</option>
                            <option value="premium">Premium Report</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="generate_report" class="btn btn-primary btn-lg">
                        <i class="fas fa-file-pdf"></i> Test Report Generation
                    </button>
                </form>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        This will show step-by-step progress instead of hanging on "Generating Report..."
                    </small>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> Analysis Required</h6>
                    <p>Analysis must be completed before generating a report.</p>
                    <a href="<?php echo url('assessments/analysis.php?id=' . $subjectId); ?>" class="btn btn-warning">
                        <i class="fas fa-brain"></i> Complete Analysis First
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Database Check -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-database"></i> Database Status Check</h5>
            </div>
            <div class="card-body">
                <?php
                $requiredTables = ['assessment_reports', 'intelligence_scores', 'personality_profiles', 'brain_dominance', 'learning_styles', 'quotient_scores', 'career_recommendations'];
                $tableStatus = [];
                
                foreach ($requiredTables as $table) {
                    try {
                        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
                        $tableStatus[$table] = $stmt->rowCount() > 0;
                    } catch (Exception $e) {
                        $tableStatus[$table] = false;
                    }
                }
                ?>
                
                <div class="row">
                    <?php foreach ($tableStatus as $table => $exists): ?>
                    <div class="col-md-6 mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><code><?php echo $table; ?></code></span>
                            <span class="badge bg-<?php echo $exists ? 'success' : 'danger'; ?>">
                                <?php echo $exists ? 'Exists' : 'Missing'; ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (in_array(false, $tableStatus)): ?>
                <div class="alert alert-warning mt-3">
                    <h6><i class="fas fa-exclamation-triangle"></i> Missing Tables Detected!</h6>
                    <p>Some required tables are missing. Make sure you've imported the complete database schema.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comparison -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-balance-scale"></i> Compare with Real Report Page</h5>
            </div>
            <div class="card-body">
                <p>Test the real report page to compare behavior:</p>
                <a href="<?php echo url('assessments/report.php?id=' . $subjectId); ?>" class="btn btn-outline-primary" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Open Real Report Page
                </a>
                
                <div class="alert alert-info mt-3">
                    <h6><i class="fas fa-lightbulb"></i> What to Compare:</h6>
                    <ul class="mb-0">
                        <li><strong>This page:</strong> Shows detailed progress and completes</li>
                        <li><strong>Real page:</strong> Gets stuck on "Generating Report..." spinner</li>
                        <li><strong>Difference:</strong> Will help identify the exact issue</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="text-center">
            <a href="<?php echo url('assessments/list.php'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Assessment List
            </a>
            <a href="<?php echo url('assessments/report.php?id=' . $subjectId); ?>" class="btn btn-info ms-2">
                <i class="fas fa-file-pdf"></i> Real Report Page
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- NO JavaScript button disabling - to test if that's the issue -->

    <?php include 'includes/footer.php'; ?>
</body>
</html>
