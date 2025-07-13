<?php
/**
 * Analysis Debug - DMIT Psychometric Test System
 * Debug page to test and troubleshoot the analysis functionality
 */

require_once 'config/config.php';

$pageTitle = 'Analysis Debug - ' . APP_NAME;

// Check if analysis tables exist
$database = new Database();
$conn = $database->getConnection();

$requiredTables = [
    'intelligence_scores',
    'personality_profiles', 
    'brain_dominance',
    'learning_styles',
    'quotient_scores',
    'career_recommendations'
];

$tableStatus = [];
foreach ($requiredTables as $table) {
    try {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        $tableStatus[$table] = $stmt->rowCount() > 0;
    } catch (Exception $e) {
        $tableStatus[$table] = false;
    }
}

// Check for test subjects with fingerprint data
$testSubjects = [];
try {
    $stmt = $conn->query("
        SELECT s.*, COUNT(f.id) as fingerprint_count 
        FROM assessment_subjects s 
        LEFT JOIN fingerprint_data f ON s.id = f.subject_id 
        GROUP BY s.id 
        HAVING fingerprint_count >= 8 
        ORDER BY s.created_at DESC 
        LIMIT 5
    ");
    $testSubjects = $stmt->fetchAll();
} catch (Exception $e) {
    // Table might not exist
}

// Test analysis if requested
$testResults = null;
$testError = null;
if ($_POST['test_analysis'] ?? false) {
    $testSubjectId = $_POST['subject_id'] ?? 0;
    
    if ($testSubjectId) {
        try {
            $assessmentEngine = new AssessmentEngine($database);
            $testResults = $assessmentEngine->performAnalysis($testSubjectId);
        } catch (Exception $e) {
            $testError = $e->getMessage();
        }
    }
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
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-section { background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); color: white; padding: 3rem 0; }
        .debug-card { border: none; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        .status-good { color: #28a745; }
        .status-bad { color: #dc3545; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1><i class="fas fa-bug"></i> Analysis Debug & Test</h1>
                    <p class="lead">Troubleshoot and test the DMIT analysis functionality</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Problem Summary -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="alert alert-success">
                    <h4><i class="fas fa-check-circle"></i> Analysis Tables Already Exist!</h4>
                    <p><strong>Good News:</strong> The main database schema already includes all required analysis tables</p>
                    <p><strong>Tables Included:</strong> intelligence_scores, personality_profiles, brain_dominance, learning_styles, quotient_scores, career_recommendations</p>
                    <p><strong>Status:</strong> Only minor column updates may be needed for analysis completion tracking</p>
                </div>
            </div>
        </div>

        <!-- Database Tables Check -->
        <section class="mb-5">
            <h2><i class="fas fa-database"></i> Database Tables Status</h2>
            <div class="debug-card card">
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($tableStatus as $table => $exists): ?>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><code><?php echo $table; ?></code></span>
                                <span class="<?php echo $exists ? 'status-good' : 'status-bad'; ?>">
                                    <i class="fas fa-<?php echo $exists ? 'check-circle' : 'times-circle'; ?>"></i>
                                    <?php echo $exists ? 'Exists' : 'Missing'; ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (in_array(false, $tableStatus)): ?>
                    <div class="alert alert-warning mt-3">
                        <h6><i class="fas fa-exclamation-triangle"></i> Missing Tables Detected!</h6>
                        <p>Some required tables are missing. The main database schema should include all analysis tables.</p>
                        <p>Make sure you've imported the complete database schema:</p>
                        <code>mysql -u root -p dmit_system < database/dmit_psychometric.sql</code>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle"></i> All required tables exist! (Already included in main schema)
                        <p class="mb-0 mt-2">You may need to add analysis tracking columns:</p>
                        <code>mysql -u root -p dmit_system < database/analysis_columns_update.sql</code>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Test Subjects -->
        <section class="mb-5">
            <h2><i class="fas fa-users"></i> Test Subjects (Ready for Analysis)</h2>
            <div class="debug-card card">
                <div class="card-body">
                    <?php if (empty($testSubjects)): ?>
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> No Test Subjects Available</h6>
                        <p>No subjects found with sufficient fingerprint data (minimum 8 fingerprints required).</p>
                        <p class="mb-0">Create a new assessment and collect fingerprint data first.</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Fingerprints</th>
                                    <th>Analysis Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($testSubjects as $subject): ?>
                                <tr>
                                    <td><?php echo $subject['id']; ?></td>
                                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                    <td><?php echo $subject['age_at_assessment']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $subject['fingerprint_count'] >= 8 ? 'success' : 'warning'; ?>">
                                            <?php echo $subject['fingerprint_count']; ?>/10
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($subject['analysis_complete'] ?? false): ?>
                                            <span class="badge bg-success">Complete</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="subject_id" value="<?php echo $subject['id']; ?>">
                                            <button type="submit" name="test_analysis" class="btn btn-sm btn-primary">
                                                <i class="fas fa-cogs"></i> Test Analysis
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Test Results -->
        <?php if ($testResults || $testError): ?>
        <section class="mb-5">
            <h2><i class="fas fa-vial"></i> Test Results</h2>
            <div class="debug-card card">
                <div class="card-body">
                    <?php if ($testError): ?>
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-times-circle"></i> Analysis Failed</h6>
                        <p><strong>Error:</strong> <?php echo htmlspecialchars($testError); ?></p>
                    </div>
                    <?php elseif ($testResults && $testResults['success']): ?>
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle"></i> Analysis Successful!</h6>
                        <p>The analysis completed successfully. Here's a summary of the results:</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-6">
                            <h6>Intelligence Scores:</h6>
                            <ul class="small">
                                <?php foreach ($testResults['intelligence_scores'] as $type => $score): ?>
                                    <?php if ($type !== 'dominant_intelligence'): ?>
                                    <li><strong><?php echo ucfirst(str_replace('_', ' ', $type)); ?>:</strong> <?php echo $score; ?>%</li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="col-lg-6">
                            <h6>Personality Profile:</h6>
                            <ul class="small">
                                <li><strong>Primary Type:</strong> <?php echo ucfirst($testResults['personality_profile']['primary_type']); ?></li>
                                <li><strong>Secondary Type:</strong> <?php echo ucfirst($testResults['personality_profile']['secondary_type'] ?? 'None'); ?></li>
                                <li><strong>Brain Dominance:</strong> <?php echo ucfirst($testResults['brain_dominance']['dominance_type']); ?></li>
                                <li><strong>Learning Style:</strong> <?php echo ucfirst($testResults['learning_styles']['primary_style']); ?></li>
                            </ul>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Analysis Incomplete</h6>
                        <p>The analysis returned but was not successful. Check the error logs for details.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- AssessmentEngine Status -->
        <section class="mb-5">
            <h2><i class="fas fa-cogs"></i> AssessmentEngine Status</h2>
            <div class="debug-card card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h6>Class Status:</h6>
                            <ul>
                                <li><strong>AssessmentEngine class:</strong> 
                                    <span class="<?php echo class_exists('AssessmentEngine') ? 'status-good' : 'status-bad'; ?>">
                                        <i class="fas fa-<?php echo class_exists('AssessmentEngine') ? 'check-circle' : 'times-circle'; ?>"></i>
                                        <?php echo class_exists('AssessmentEngine') ? 'Loaded' : 'Missing'; ?>
                                    </span>
                                </li>
                                <li><strong>File exists:</strong> 
                                    <span class="<?php echo file_exists('includes/assessment_engine.php') ? 'status-good' : 'status-bad'; ?>">
                                        <i class="fas fa-<?php echo file_exists('includes/assessment_engine.php') ? 'check-circle' : 'times-circle'; ?>"></i>
                                        <?php echo file_exists('includes/assessment_engine.php') ? 'Yes' : 'No'; ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-6">
                            <h6>Required Methods:</h6>
                            <?php if (class_exists('AssessmentEngine')): ?>
                            <ul class="small">
                                <?php 
                                $methods = ['performAnalysis', 'getFingerprintData', 'calculateIntelligenceScores'];
                                foreach ($methods as $method): 
                                    $exists = method_exists('AssessmentEngine', $method);
                                ?>
                                <li><strong><?php echo $method; ?>:</strong> 
                                    <span class="<?php echo $exists ? 'status-good' : 'status-bad'; ?>">
                                        <i class="fas fa-<?php echo $exists ? 'check' : 'times'; ?>"></i>
                                    </span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?>
                            <p class="text-muted">Class not loaded</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Solutions -->
        <section class="mb-5">
            <h2><i class="fas fa-wrench"></i> Solutions</h2>
            <div class="debug-card card">
                <div class="card-body">
                    <h5>Step-by-Step Fix:</h5>
                    <ol>
                        <li><strong>Import Database Tables:</strong>
                            <p>Run the SQL file to create missing analysis tables:</p>
                            <code>mysql -u root -p dmit_system < database/analysis_tables.sql</code>
                        </li>
                        <li><strong>Verify Fingerprint Data:</strong>
                            <p>Ensure test subjects have at least 8 fingerprint patterns collected.</p>
                        </li>
                        <li><strong>Test Analysis:</strong>
                            <p>Use the test buttons above to verify the analysis engine works.</p>
                        </li>
                        <li><strong>Check Error Logs:</strong>
                            <p>If analysis fails, check PHP error logs for detailed error messages.</p>
                        </li>
                    </ol>
                    
                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-lightbulb"></i> Quick Test:</h6>
                        <p class="mb-0">If you have subjects with fingerprint data, click "Test Analysis" above to verify the system works.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Navigation -->
        <div class="text-center">
            <div class="debug-card card bg-primary text-white">
                <div class="card-body py-4">
                    <h3><i class="fas fa-tools"></i> Analysis System Debug Complete</h3>
                    <p class="lead">Use this page to troubleshoot and verify the analysis functionality.</p>
                    <div class="mt-4">
                        <a href="<?php echo url('assessments/list.php'); ?>" class="btn btn-light btn-lg me-3">
                            <i class="fas fa-list"></i> Assessment List
                        </a>
                        <a href="<?php echo url('assessments/new.php'); ?>" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-plus"></i> Create Test Assessment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
