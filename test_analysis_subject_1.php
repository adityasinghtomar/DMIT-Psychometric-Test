<?php
/**
 * Test Analysis Subject 1 - DMIT Psychometric Test System
 * Quick test for subject ID 1 analysis issue
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';

Security::requireAuth();

$pageTitle = 'Test Analysis Subject 1 - ' . APP_NAME;

$subjectId = 1;
$debugInfo = [];
$analysisResult = null;
$analysisError = null;

// Debug the analysis process step by step
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_analysis'])) {
    $debugInfo[] = "✓ POST request received for subject ID: $subjectId";
    
    try {
        $debugInfo[] = "✓ Creating database connection...";
        $database = new Database();
        $conn = $database->getConnection();
        $debugInfo[] = "✓ Database connected";
        
        // Check if subject exists
        $stmt = $conn->prepare("SELECT * FROM assessment_subjects WHERE id = ?");
        $stmt->execute([$subjectId]);
        $subject = $stmt->fetch();
        
        if (!$subject) {
            $debugInfo[] = "✗ Subject ID $subjectId not found";
            $analysisError = "Subject not found";
        } else {
            $debugInfo[] = "✓ Subject found: " . $subject['subject_name'];
            
            // Check fingerprint count
            $stmt = $conn->prepare("SELECT COUNT(*) FROM fingerprint_data WHERE subject_id = ?");
            $stmt->execute([$subjectId]);
            $fingerprintCount = $stmt->fetchColumn();
            $debugInfo[] = "✓ Fingerprints found: $fingerprintCount/10";
            
            if ($fingerprintCount < 8) {
                $debugInfo[] = "✗ Insufficient fingerprints ($fingerprintCount/10). Need at least 8.";
                $analysisError = "Insufficient fingerprint data. Found $fingerprintCount/10 fingerprints.";
            } else {
                $debugInfo[] = "✓ Sufficient fingerprints for analysis";
                
                // Try to create AssessmentEngine
                $debugInfo[] = "✓ Creating AssessmentEngine...";
                $assessmentEngine = new AssessmentEngine($database);
                $debugInfo[] = "✓ AssessmentEngine created";
                
                // Perform analysis
                $debugInfo[] = "✓ Starting analysis...";
                $startTime = microtime(true);
                
                $analysisResults = $assessmentEngine->performAnalysis($subjectId);
                
                $endTime = microtime(true);
                $executionTime = round($endTime - $startTime, 2);
                $debugInfo[] = "✓ Analysis completed in {$executionTime} seconds";
                
                if ($analysisResults['success']) {
                    $debugInfo[] = "✓ Analysis successful!";
                    $analysisResult = $analysisResults;
                } else {
                    $debugInfo[] = "✗ Analysis failed: " . ($analysisResults['error'] ?? 'Unknown error');
                    $analysisError = $analysisResults['error'] ?? 'Analysis failed';
                }
            }
        }
        
    } catch (Exception $e) {
        $debugInfo[] = "✗ Exception: " . $e->getMessage();
        $analysisError = $e->getMessage();
    }
}

// Get subject info for display
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM assessment_subjects WHERE id = ?");
    $stmt->execute([$subjectId]);
    $subject = $stmt->fetch();
    
    if ($subject) {
        // Get fingerprint count
        $stmt = $conn->prepare("SELECT COUNT(*) FROM fingerprint_data WHERE subject_id = ?");
        $stmt->execute([$subjectId]);
        $fingerprintCount = $stmt->fetchColumn();
    }
    
} catch (Exception $e) {
    $subject = null;
    $fingerprintCount = 0;
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
        <h1><i class="fas fa-user-check"></i> Test Analysis for Subject ID 1</h1>
        <p class="lead">Debug analysis issue for specific subject</p>

        <!-- Subject Info -->
        <?php if ($subject): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-user"></i> Subject Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul>
                            <li><strong>ID:</strong> <?php echo $subject['id']; ?></li>
                            <li><strong>Name:</strong> <?php echo htmlspecialchars($subject['subject_name']); ?></li>
                            <li><strong>Age:</strong> <?php echo $subject['age_at_assessment']; ?> years</li>
                            <li><strong>Gender:</strong> <?php echo ucfirst($subject['gender']); ?></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul>
                            <li><strong>Fingerprints:</strong> 
                                <span class="badge bg-<?php echo $fingerprintCount >= 8 ? 'success' : 'warning'; ?>">
                                    <?php echo $fingerprintCount; ?>/10
                                </span>
                            </li>
                            <li><strong>Status:</strong> 
                                <span class="badge bg-<?php echo $fingerprintCount >= 8 ? 'success' : 'danger'; ?>">
                                    <?php echo $fingerprintCount >= 8 ? 'Ready for Analysis' : 'Insufficient Data'; ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle"></i> Subject Not Found</h5>
            <p>Subject with ID 1 was not found in the database.</p>
        </div>
        <?php endif; ?>

        <!-- Test Analysis -->
        <?php if ($subject && $fingerprintCount >= 8): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-play"></i> Test Analysis</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <button type="submit" name="test_analysis" class="btn btn-primary btn-lg">
                        <i class="fas fa-brain"></i> Test Analysis for Subject 1
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Debug Results -->
        <?php if (!empty($debugInfo)): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-bug"></i> Debug Information</h5>
            </div>
            <div class="card-body">
                <div class="debug-log" style="font-family: monospace; font-size: 0.9rem;">
                    <?php foreach ($debugInfo as $info): ?>
                        <div class="<?php echo strpos($info, '✓') === 0 ? 'text-success' : (strpos($info, '✗') === 0 ? 'text-danger' : ''); ?>">
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
                <h5><i class="fas fa-check-circle"></i> Analysis Successful</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <h6>✓ Analysis completed successfully!</h6>
                    <p>The analysis engine is working correctly for subject ID 1.</p>
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
                        <i class="fas fa-file-pdf"></i> View Report
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
                
                <?php if (strpos($analysisError, 'Insufficient') !== false): ?>
                <div class="mt-3">
                    <a href="<?php echo url('assessments/fingerprint_collection.php?id=' . $subjectId); ?>" class="btn btn-warning">
                        <i class="fas fa-fingerprint"></i> Collect More Fingerprints
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Compare with Real Analysis -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-balance-scale"></i> Compare with Real Analysis</h5>
            </div>
            <div class="card-body">
                <p>Test the actual analysis page for subject 1:</p>
                <a href="<?php echo url('assessments/analysis.php?id=1'); ?>" class="btn btn-outline-primary" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Open Real Analysis Page (Subject 1)
                </a>
                
                <div class="alert alert-info mt-3">
                    <h6><i class="fas fa-lightbulb"></i> Comparison:</h6>
                    <ul class="mb-0">
                        <li><strong>This debug test:</strong> Shows detailed progress and errors</li>
                        <li><strong>Real analysis page:</strong> May hang on "Analyzing..." if there's an issue</li>
                        <li><strong>Expected:</strong> Both should work if subject has enough fingerprints</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="text-center">
            <a href="<?php echo url('assessments/list.php'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Assessment List
            </a>
            <a href="simple_analysis_test.php?id=1" class="btn btn-info ms-2">
                <i class="fas fa-vial"></i> General Analysis Test
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
