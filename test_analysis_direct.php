<?php
/**
 * Direct Analysis Test - DMIT Psychometric Test System
 * Test analysis functionality directly with detailed error reporting
 */

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

require_once 'config/config.php';

$pageTitle = 'Direct Analysis Test - ' . APP_NAME;

// Get a test subject with fingerprints
$database = new Database();
$conn = $database->getConnection();

$testSubject = null;
$fingerprintCount = 0;
$analysisResult = null;
$analysisError = null;
$executionTime = 0;

try {
    // Find a subject with fingerprints
    $stmt = $conn->query("
        SELECT s.*, COUNT(f.id) as fingerprint_count 
        FROM assessment_subjects s 
        LEFT JOIN fingerprint_data f ON s.id = f.subject_id 
        GROUP BY s.id 
        HAVING fingerprint_count >= 8 
        ORDER BY s.created_at DESC 
        LIMIT 1
    ");
    $testSubject = $stmt->fetch();
    $fingerprintCount = $testSubject['fingerprint_count'] ?? 0;
} catch (Exception $e) {
    $analysisError = "Database error: " . $e->getMessage();
}

// Test analysis if we have a subject and form is submitted
if ($testSubject && ($_POST['run_test'] ?? false)) {
    echo "<h3>Starting Analysis Test...</h3>";
    echo "<p><strong>Subject:</strong> " . htmlspecialchars($testSubject['subject_name']) . " (ID: {$testSubject['id']})</p>";
    echo "<p><strong>Fingerprints:</strong> {$fingerprintCount}/10</p>";
    echo "<hr>";
    
    $startTime = microtime(true);
    
    try {
        echo "<p>✓ Creating AssessmentEngine...</p>";
        $assessmentEngine = new AssessmentEngine($database);
        
        echo "<p>✓ Starting analysis...</p>";
        flush(); // Send output to browser immediately
        
        $analysisResult = $assessmentEngine->performAnalysis($testSubject['id']);
        
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        
        echo "<p>✓ Analysis completed in {$executionTime} seconds</p>";
        
        if ($analysisResult['success']) {
            echo "<div style='color: green;'><strong>✓ SUCCESS!</strong> Analysis completed successfully.</div>";
        } else {
            echo "<div style='color: red;'><strong>✗ FAILED!</strong> Analysis returned failure.</div>";
        }
        
    } catch (Exception $e) {
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        $analysisError = $e->getMessage();
        echo "<div style='color: red;'><strong>✗ ERROR after {$executionTime} seconds:</strong> " . htmlspecialchars($analysisError) . "</div>";
    }
    
    echo "<hr>";
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
    <div class="container my-5">
        <h1><i class="fas fa-vial"></i> Direct Analysis Test</h1>
        <p class="lead">Test the analysis functionality directly with detailed error reporting</p>

        <?php if (!$testSubject): ?>
        <div class="alert alert-warning">
            <h5><i class="fas fa-exclamation-triangle"></i> No Test Subject Available</h5>
            <p>No subjects found with sufficient fingerprint data (minimum 8 fingerprints required).</p>
            <p><strong>Current subjects in database:</strong></p>
            <?php
            try {
                $stmt = $conn->query("
                    SELECT s.subject_name, s.id, COUNT(f.id) as fingerprint_count 
                    FROM assessment_subjects s 
                    LEFT JOIN fingerprint_data f ON s.id = f.subject_id 
                    GROUP BY s.id 
                    ORDER BY s.created_at DESC
                ");
                $allSubjects = $stmt->fetchAll();
                
                if ($allSubjects) {
                    echo "<ul>";
                    foreach ($allSubjects as $subject) {
                        echo "<li><strong>{$subject['subject_name']}</strong> (ID: {$subject['id']}) - {$subject['fingerprint_count']}/10 fingerprints</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No subjects found in database.</p>";
                }
            } catch (Exception $e) {
                echo "<p>Error loading subjects: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
            <p><a href="<?php echo url('assessments/new.php'); ?>" class="btn btn-primary">Create New Assessment</a></p>
        </div>
        <?php else: ?>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5>Test Subject Found</h5>
                <ul>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($testSubject['subject_name']); ?></li>
                    <li><strong>ID:</strong> <?php echo $testSubject['id']; ?></li>
                    <li><strong>Age:</strong> <?php echo $testSubject['age_at_assessment']; ?> years</li>
                    <li><strong>Fingerprints:</strong> <?php echo $fingerprintCount; ?>/10</li>
                    <li><strong>Analysis Status:</strong> 
                        <?php if ($testSubject['analysis_complete'] ?? false): ?>
                            <span class="badge bg-success">Complete</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Pending</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5>Run Analysis Test</h5>
                <p>This will run the analysis directly and show detailed progress and any errors.</p>
                
                <form method="POST">
                    <button type="submit" name="run_test" class="btn btn-primary btn-lg">
                        <i class="fas fa-play"></i> Run Analysis Test
                    </button>
                </form>
            </div>
        </div>

        <?php if ($analysisResult): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5>Analysis Results</h5>
                <p><strong>Execution Time:</strong> <?php echo $executionTime; ?> seconds</p>
                <p><strong>Success:</strong> <?php echo $analysisResult['success'] ? 'Yes' : 'No'; ?></p>
                
                <?php if ($analysisResult['success']): ?>
                <h6>Generated Data:</h6>
                <ul>
                    <li><strong>Intelligence Scores:</strong> <?php echo count($analysisResult['intelligence_scores']); ?> types</li>
                    <li><strong>Personality Type:</strong> <?php echo ucfirst($analysisResult['personality_profile']['primary_type']); ?></li>
                    <li><strong>Brain Dominance:</strong> <?php echo ucfirst($analysisResult['brain_dominance']['dominance_type']); ?></li>
                    <li><strong>Learning Style:</strong> <?php echo ucfirst($analysisResult['learning_styles']['primary_style']); ?></li>
                </ul>
                
                <div class="alert alert-success">
                    <strong>✓ Analysis completed successfully!</strong> The issue is not with the analysis engine itself.
                    The problem might be with the redirect or the web interface.
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($analysisError): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="text-danger">Analysis Error</h5>
                <div class="alert alert-danger">
                    <strong>Error:</strong> <?php echo htmlspecialchars($analysisError); ?>
                </div>
                <p><strong>Execution Time:</strong> <?php echo $executionTime; ?> seconds</p>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <h5>Troubleshooting Steps</h5>
                <ol>
                    <li><strong>Check PHP Error Logs:</strong>
                        <ul>
                            <li><code>C:\xampp\apache\logs\error.log</code></li>
                            <li><code>C:\xampp\php\logs\php_error_log</code></li>
                        </ul>
                    </li>
                    <li><strong>Check Database Tables:</strong>
                        <a href="analysis_debug.php" class="btn btn-sm btn-secondary">Analysis Debug</a>
                    </li>
                    <li><strong>Test Real Analysis:</strong>
                        <a href="<?php echo url('assessments/list.php'); ?>" class="btn btn-sm btn-primary">Assessment List</a>
                    </li>
                </ol>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
</body>
</html>
