<?php
/**
 * Analysis Button Debug - DMIT Psychometric Test System
 * Debug the "Perform DMIT Analysis" button issue
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';

Security::requireAuth();

$pageTitle = 'Analysis Button Debug - ' . APP_NAME;

// Get subject ID
$subjectId = $_GET['id'] ?? 0;
if (!$subjectId) {
    redirect('assessments/list.php', 'No subject ID provided.', 'error');
}

$debugInfo = [];
$analysisResult = null;
$analysisError = null;

// Debug the analysis process step by step
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['perform_analysis'])) {
    $debugInfo[] = "✓ POST request received with perform_analysis";
    
    $csrfToken = $_POST['csrf_token'] ?? '';
    $debugInfo[] = "✓ CSRF token: " . ($csrfToken ? 'Present' : 'Missing');
    
    if (!Security::verifyCSRFToken($csrfToken)) {
        $debugInfo[] = "✗ CSRF token verification failed";
        $analysisError = 'Invalid security token';
    } else {
        $debugInfo[] = "✓ CSRF token verified";
        
        try {
            $debugInfo[] = "✓ Creating AssessmentEngine...";
            $database = new Database();
            $assessmentEngine = new AssessmentEngine($database);
            $debugInfo[] = "✓ AssessmentEngine created successfully";
            
            $debugInfo[] = "✓ Starting analysis for subject ID: $subjectId";
            $startTime = microtime(true);
            
            $analysisResults = $assessmentEngine->performAnalysis($subjectId);
            
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);
            $debugInfo[] = "✓ Analysis completed in {$executionTime} seconds";
            
            if ($analysisResults['success']) {
                $debugInfo[] = "✓ Analysis successful";
                $debugInfo[] = "✓ Attempting redirect to report.php?id=$subjectId";
                
                // Instead of redirecting, let's show what would happen
                $analysisResult = $analysisResults;
                $debugInfo[] = "✓ Would redirect to: report.php?id=$subjectId";
                $debugInfo[] = "✓ Message: 'Analysis completed successfully. Report is ready.'";
                
            } else {
                $debugInfo[] = "✗ Analysis failed";
                $analysisError = $analysisResults['error'] ?? 'Analysis failed. Please try again.';
                $debugInfo[] = "✗ Error: " . $analysisError;
            }
            
        } catch (Exception $e) {
            $debugInfo[] = "✗ Exception caught: " . $e->getMessage();
            $analysisError = 'Analysis failed: ' . $e->getMessage();
            error_log("Analysis error: " . $e->getMessage());
        }
    }
}

// Get subject info
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM assessment_subjects WHERE id = ?");
    $stmt->execute([$subjectId]);
    $subject = $stmt->fetch();
    
    if (!$subject) {
        redirect('assessments/list.php', 'Subject not found.', 'error');
    }
    
    // Get fingerprint count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM fingerprint_data WHERE subject_id = ?");
    $stmt->execute([$subjectId]);
    $fingerprintCount = $stmt->fetchColumn();
    
} catch (Exception $e) {
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
    <style>
        .debug-info { font-family: monospace; font-size: 0.9rem; }
        .debug-success { color: #28a745; }
        .debug-error { color: #dc3545; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <h1><i class="fas fa-bug"></i> Analysis Button Debug</h1>
        <p class="lead">Debug the "Perform DMIT Analysis" button functionality</p>

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

        <!-- Debug Info -->
        <?php if (!empty($debugInfo)): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Debug Information</h5>
            </div>
            <div class="card-body">
                <div class="debug-info">
                    <?php foreach ($debugInfo as $info): ?>
                        <div class="<?php echo strpos($info, '✓') === 0 ? 'debug-success' : (strpos($info, '✗') === 0 ? 'debug-error' : ''); ?>">
                            <?php echo htmlspecialchars($info); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Analysis Results -->
        <?php if ($analysisResult): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5><i class="fas fa-check-circle"></i> Analysis Successful!</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <h6>✓ Analysis completed successfully!</h6>
                    <p>The analysis engine is working correctly. The issue is likely with the redirect mechanism.</p>
                </div>
                
                <h6>Analysis Summary:</h6>
                <ul>
                    <li><strong>Intelligence Types:</strong> <?php echo count($analysisResult['intelligence_scores']); ?> analyzed</li>
                    <li><strong>Personality Type:</strong> <?php echo ucfirst($analysisResult['personality_profile']['primary_type']); ?></li>
                    <li><strong>Brain Dominance:</strong> <?php echo ucfirst($analysisResult['brain_dominance']['dominance_type']); ?></li>
                    <li><strong>Learning Style:</strong> <?php echo ucfirst($analysisResult['learning_styles']['primary_style']); ?></li>
                </ul>
                
                <div class="mt-3">
                    <a href="<?php echo url('assessments/report.php?id=' . $subjectId); ?>" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> View Generated Report
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Analysis Error -->
        <?php if ($analysisError): ?>
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5><i class="fas fa-times-circle"></i> Analysis Failed</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <strong>Error:</strong> <?php echo htmlspecialchars($analysisError); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Test Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-play"></i> Test Analysis Button</h5>
            </div>
            <div class="card-body">
                <?php if ($fingerprintCount >= 8): ?>
                <p>Click the button below to test the analysis functionality with detailed debugging:</p>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <button type="submit" name="perform_analysis" class="btn btn-primary btn-lg" id="debugAnalysisBtn">
                        <i class="fas fa-cogs"></i> Test Perform DMIT Analysis
                    </button>
                </form>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        This will run the same analysis process but with detailed debugging information instead of redirecting.
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

        <!-- Comparison with Real Button -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-balance-scale"></i> Compare with Real Analysis Page</h5>
            </div>
            <div class="card-body">
                <p>Test the actual analysis page to see the difference:</p>
                <a href="<?php echo url('assessments/analysis.php?id=' . $subjectId); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-external-link-alt"></i> Go to Real Analysis Page
                </a>
                
                <div class="alert alert-info mt-3">
                    <h6><i class="fas fa-lightbulb"></i> Expected Behavior:</h6>
                    <ul class="mb-0">
                        <li>If this debug test works but the real page doesn't, the issue is with the redirect</li>
                        <li>If both fail, the issue is with the analysis engine or database</li>
                        <li>If both work, the issue might be browser-specific or JavaScript-related</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="text-center">
            <a href="<?php echo url('assessments/list.php'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Assessment List
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Debug button loading state
        document.getElementById('debugAnalysisBtn')?.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Running Debug Analysis...';
        });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
