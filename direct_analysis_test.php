<?php
/**
 * Direct Analysis Test - DMIT Psychometric Test System
 * Direct test without any redirects to see exact error
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

$subjectId = $_GET['id'] ?? 1;

echo "<!DOCTYPE html>";
echo "<html><head><title>Direct Analysis Test</title>";
echo "<style>body{font-family:monospace;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";
echo "</head><body>";

echo "<h1>Direct Analysis Test - Subject ID: $subjectId</h1>";
echo "<div style='background:#f0f0f0;padding:10px;margin:10px 0;'>";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_analysis'])) {
    echo "<h2>Analysis Process:</h2>";
    
    try {
        echo "<p class='info'>Step 1: Creating database connection...</p>";
        flush();
        
        $database = new Database();
        $conn = $database->getConnection();
        echo "<p class='success'>✓ Database connected</p>";
        flush();
        
        echo "<p class='info'>Step 2: Checking subject exists...</p>";
        flush();
        
        $stmt = $conn->prepare("SELECT * FROM assessment_subjects WHERE id = ?");
        $stmt->execute([$subjectId]);
        $subject = $stmt->fetch();
        
        if (!$subject) {
            echo "<p class='error'>✗ Subject ID $subjectId not found</p>";
            exit();
        }
        
        echo "<p class='success'>✓ Subject found: " . htmlspecialchars($subject['subject_name']) . "</p>";
        flush();
        
        echo "<p class='info'>Step 3: Checking fingerprint data...</p>";
        flush();
        
        $stmt = $conn->prepare("SELECT COUNT(*) FROM fingerprint_data WHERE subject_id = ?");
        $stmt->execute([$subjectId]);
        $fingerprintCount = $stmt->fetchColumn();
        
        echo "<p class='success'>✓ Fingerprints found: $fingerprintCount/10</p>";
        flush();
        
        if ($fingerprintCount < 8) {
            echo "<p class='error'>✗ Insufficient fingerprints. Need at least 8, found $fingerprintCount</p>";
            echo "<p><a href='assessments/fingerprint_collection.php?id=$subjectId'>Collect more fingerprints</a></p>";
            exit();
        }
        
        echo "<p class='info'>Step 4: Creating AssessmentEngine...</p>";
        flush();
        
        $assessmentEngine = new AssessmentEngine($database);
        echo "<p class='success'>✓ AssessmentEngine created</p>";
        flush();
        
        echo "<p class='info'>Step 5: Starting analysis...</p>";
        flush();
        
        $startTime = microtime(true);
        
        // Try to perform analysis
        $result = $assessmentEngine->performAnalysis($subjectId);
        
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        
        echo "<p class='success'>✓ Analysis completed in {$executionTime} seconds</p>";
        flush();
        
        if ($result['success']) {
            echo "<p class='success'>✓ Analysis successful!</p>";
            echo "<h3>Results:</h3>";
            echo "<ul>";
            echo "<li>Intelligence scores: " . count($result['intelligence_scores']) . " types</li>";
            echo "<li>Personality: " . $result['personality_profile']['primary_type'] . "</li>";
            echo "<li>Brain dominance: " . $result['brain_dominance']['dominance_type'] . "</li>";
            echo "<li>Learning style: " . $result['learning_styles']['primary_style'] . "</li>";
            echo "</ul>";
            
            echo "<p><a href='assessments/report.php?id=$subjectId'>View Report</a></p>";
        } else {
            echo "<p class='error'>✗ Analysis failed: " . ($result['error'] ?? 'Unknown error') . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ EXCEPTION: " . $e->getMessage() . "</p>";
        echo "<p class='error'>File: " . $e->getFile() . "</p>";
        echo "<p class='error'>Line: " . $e->getLine() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    
} else {
    // Show subject info and test form
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM assessment_subjects WHERE id = ?");
        $stmt->execute([$subjectId]);
        $subject = $stmt->fetch();
        
        if ($subject) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM fingerprint_data WHERE subject_id = ?");
            $stmt->execute([$subjectId]);
            $fingerprintCount = $stmt->fetchColumn();
            
            echo "<h2>Subject Information:</h2>";
            echo "<ul>";
            echo "<li>ID: " . $subject['id'] . "</li>";
            echo "<li>Name: " . htmlspecialchars($subject['subject_name']) . "</li>";
            echo "<li>Age: " . $subject['age_at_assessment'] . "</li>";
            echo "<li>Fingerprints: $fingerprintCount/10</li>";
            echo "</ul>";
            
            if ($fingerprintCount >= 8) {
                echo "<form method='POST'>";
                echo "<button type='submit' name='run_analysis' style='padding:10px 20px;font-size:16px;'>Run Direct Analysis Test</button>";
                echo "</form>";
            } else {
                echo "<p class='error'>Cannot run analysis - insufficient fingerprints ($fingerprintCount/10)</p>";
                echo "<p><a href='assessments/fingerprint_collection.php?id=$subjectId'>Collect fingerprints</a></p>";
            }
        } else {
            echo "<p class='error'>Subject ID $subjectId not found</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>Database error: " . $e->getMessage() . "</p>";
    }
}

echo "</div>";

echo "<h2>Test Different Subjects:</h2>";
echo "<ul>";
echo "<li><a href='?id=1'>Test Subject 1</a></li>";
echo "<li><a href='?id=2'>Test Subject 2</a></li>";
echo "<li><a href='?id=3'>Test Subject 3</a></li>";
echo "</ul>";

echo "<h2>Other Tests:</h2>";
echo "<ul>";
echo "<li><a href='assessments/analysis.php?id=$subjectId'>Real Analysis Page (Subject $subjectId)</a></li>";
echo "<li><a href='assessments/list.php'>Assessment List</a></li>";
echo "</ul>";

echo "</body></html>";
?>
