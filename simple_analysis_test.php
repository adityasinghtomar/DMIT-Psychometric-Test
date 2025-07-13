<?php
/**
 * Simple Analysis Test - DMIT Psychometric Test System
 * Simplified version of analysis.php to isolate the issue
 */

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Increase timeout
set_time_limit(300);
ini_set('max_execution_time', 300);

require_once 'config/config.php';

Security::requireAuth();

$pageTitle = 'Simple Analysis Test - ' . APP_NAME;

// Get subject ID
$subjectId = $_GET['id'] ?? 2; // Default to ID 2

$debugLog = [];
$analysisCompleted = false;
$analysisError = null;

// Add debug logging function
function debugLog($message) {
    global $debugLog;
    $debugLog[] = date('H:i:s') . " - " . $message;
    error_log("ANALYSIS DEBUG: " . $message);
}

debugLog("Page loaded, subject ID: $subjectId");

// Handle form submission with detailed logging
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['perform_analysis'])) {
    debugLog("POST request received");
    
    // Output debug info immediately
    echo "<!DOCTYPE html><html><head><title>Analysis Debug</title></head><body>";
    echo "<h1>Analysis Debug Output</h1>";
    echo "<div style='font-family: monospace; background: #f8f9fa; padding: 20px; margin: 20px;'>";
    
    debugLog("Starting analysis process");
    echo "<p>✓ Form submitted successfully</p>";
    flush();
    
    $csrfToken = $_POST['csrf_token'] ?? '';
    debugLog("CSRF token present: " . ($csrfToken ? 'Yes' : 'No'));
    
    if (!Security::verifyCSRFToken($csrfToken)) {
        debugLog("CSRF verification failed");
        echo "<p style='color: red;'>✗ CSRF token verification failed</p>";
        $analysisError = 'Invalid security token';
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
            
            debugLog("Creating AssessmentEngine");
            echo "<p>✓ Creating AssessmentEngine...</p>";
            flush();
            
            $assessmentEngine = new AssessmentEngine($database);
            
            debugLog("AssessmentEngine created");
            echo "<p>✓ AssessmentEngine created</p>";
            flush();
            
            debugLog("Starting analysis for subject $subjectId");
            echo "<p>✓ Starting analysis for subject $subjectId...</p>";
            flush();
            
            $startTime = microtime(true);
            $analysisResults = $assessmentEngine->performAnalysis($subjectId);
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);
            
            debugLog("Analysis completed in {$executionTime} seconds");
            echo "<p>✓ Analysis completed in {$executionTime} seconds</p>";
            flush();
            
            if ($analysisResults['success']) {
                debugLog("Analysis successful");
                echo "<p style='color: green;'>✓ Analysis successful!</p>";
                echo "<p>✓ Intelligence scores calculated</p>";
                echo "<p>✓ Personality profile generated</p>";
                echo "<p>✓ Brain dominance analyzed</p>";
                echo "<p>✓ Learning styles determined</p>";
                flush();
                
                debugLog("Attempting redirect to report");
                echo "<p>✓ Preparing redirect to report.php?id=$subjectId</p>";
                flush();
                
                // Instead of redirecting, show what would happen
                echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
                echo "<h3>Analysis Completed Successfully!</h3>";
                echo "<p><strong>Would redirect to:</strong> <a href='assessments/report.php?id=$subjectId'>report.php?id=$subjectId</a></p>";
                echo "<p><strong>Message:</strong> Analysis completed successfully. Report is ready.</p>";
                echo "<p><a href='assessments/report.php?id=$subjectId' class='btn btn-primary'>View Report</a></p>";
                echo "</div>";
                
                $analysisCompleted = true;
                
            } else {
                debugLog("Analysis failed: " . ($analysisResults['error'] ?? 'Unknown error'));
                echo "<p style='color: red;'>✗ Analysis failed: " . htmlspecialchars($analysisResults['error'] ?? 'Unknown error') . "</p>";
                $analysisError = $analysisResults['error'] ?? 'Analysis failed';
            }
            
        } catch (Exception $e) {
            debugLog("Exception caught: " . $e->getMessage());
            echo "<p style='color: red;'>✗ Exception: " . htmlspecialchars($e->getMessage()) . "</p>";
            $analysisError = 'Analysis failed: ' . $e->getMessage();
            error_log("Analysis error: " . $e->getMessage());
        }
    }
    
    echo "</div>";
    echo "<h2>Debug Log:</h2>";
    echo "<pre style='background: #f8f9fa; padding: 15px;'>";
    foreach ($debugLog as $log) {
        echo htmlspecialchars($log) . "\n";
    }
    echo "</pre>";
    
    echo "<p><a href='simple_analysis_test.php?id=$subjectId'>← Back to Test Page</a></p>";
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
    
    // Get fingerprint count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM fingerprint_data WHERE subject_id = ?");
    $stmt->execute([$subjectId]);
    $fingerprintCount = $stmt->fetchColumn();
    
    debugLog("Subject loaded: " . $subject['subject_name'] . ", Fingerprints: $fingerprintCount");
    
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
        <h1><i class="fas fa-vial"></i> Simple Analysis Test</h1>
        <p class="lead">Simplified analysis test with detailed debugging</p>

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
                    <li><strong>Fingerprints:</strong> <?php echo $fingerprintCount; ?>/10</li>
                </ul>
            </div>
        </div>

        <!-- Analysis Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-brain"></i> Simplified Analysis Test</h5>
            </div>
            <div class="card-body">
                <?php if ($fingerprintCount >= 8): ?>
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> This is a simplified version of the analysis process</h6>
                    <p class="mb-0">It will show detailed debug output instead of redirecting, so we can see exactly what happens.</p>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <button type="submit" name="perform_analysis" class="btn btn-primary btn-lg">
                        <i class="fas fa-cogs"></i> Run Simplified Analysis Test
                    </button>
                </form>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        This will show step-by-step progress instead of hanging on "Analyzing..."
                    </small>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> Insufficient Fingerprint Data</h6>
                    <p>This subject only has <?php echo $fingerprintCount; ?>/10 fingerprints. At least 8 fingerprints are required for analysis.</p>
                    <a href="<?php echo url('assessments/fingerprint_collection.php?id=' . $subjectId); ?>" class="btn btn-warning">
                        <i class="fas fa-fingerprint"></i> Collect More Fingerprints
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Debug Log -->
        <?php if (!empty($debugLog)): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> Debug Log</h5>
            </div>
            <div class="card-body">
                <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px;"><?php
                foreach ($debugLog as $log) {
                    echo htmlspecialchars($log) . "\n";
                }
                ?></pre>
            </div>
        </div>
        <?php endif; ?>

        <!-- Comparison -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-balance-scale"></i> Compare with Real Analysis</h5>
            </div>
            <div class="card-body">
                <p>Test the real analysis page to compare behavior:</p>
                <a href="<?php echo url('assessments/analysis.php?id=' . $subjectId); ?>" class="btn btn-outline-primary" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Open Real Analysis Page
                </a>
                
                <div class="alert alert-info mt-3">
                    <h6><i class="fas fa-lightbulb"></i> What to Compare:</h6>
                    <ul class="mb-0">
                        <li><strong>This page:</strong> Shows detailed progress and completes</li>
                        <li><strong>Real page:</strong> Gets stuck on "Analyzing..." spinner</li>
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
            <a href="form_submission_test.php" class="btn btn-info ms-2">
                <i class="fas fa-paper-plane"></i> Test Form Submission
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- NO JavaScript button disabling - to test if that's the issue -->

    <?php include 'includes/footer.php'; ?>
</body>
</html>
