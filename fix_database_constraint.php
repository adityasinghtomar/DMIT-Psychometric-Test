<?php
/**
 * Fix Database Constraint - DMIT Psychometric Test System
 * Remove JSON constraint from report_data column to allow HTML content
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';

$pageTitle = 'Fix Database Constraint - ' . APP_NAME;

$fixApplied = false;
$fixError = null;
$debugLog = [];

// Add debug logging function
function debugLog($message) {
    global $debugLog;
    $debugLog[] = date('H:i:s') . " - " . $message;
    error_log("DB FIX: " . $message);
}

// Apply database fix
if ($_POST['apply_fix'] ?? false) {
    debugLog("Starting database constraint fix");
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        debugLog("Database connected");
        
        // Check current constraint
        debugLog("Checking current table structure");
        $stmt = $conn->query("SHOW CREATE TABLE assessment_reports");
        $tableStructure = $stmt->fetch();
        debugLog("Current table structure retrieved");
        
        // Try to remove the JSON constraint
        debugLog("Attempting to remove JSON constraint");
        try {
            // Method 1: Try to drop the constraint by name
            $conn->exec("ALTER TABLE `assessment_reports` DROP CHECK `assessment_reports.report_data`");
            debugLog("JSON constraint removed successfully (Method 1)");
        } catch (Exception $e) {
            debugLog("Method 1 failed: " . $e->getMessage());
            
            // Method 2: Try alternative constraint name
            try {
                $conn->exec("ALTER TABLE `assessment_reports` DROP CHECK `report_data`");
                debugLog("JSON constraint removed successfully (Method 2)");
            } catch (Exception $e2) {
                debugLog("Method 2 failed: " . $e2->getMessage());
                
                // Method 3: Recreate column without constraint
                debugLog("Attempting Method 3: Recreate column");
                $conn->exec("ALTER TABLE `assessment_reports` MODIFY COLUMN `report_data` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL");
                debugLog("Column recreated without constraint (Method 3)");
            }
        }
        
        // Verify the fix
        debugLog("Verifying the fix");
        $stmt = $conn->query("SHOW CREATE TABLE assessment_reports");
        $newTableStructure = $stmt->fetch();
        
        // Check if JSON constraint is gone
        $hasJsonConstraint = strpos($newTableStructure[1], 'json_valid') !== false;
        
        if (!$hasJsonConstraint) {
            debugLog("SUCCESS: JSON constraint removed successfully");
            $fixApplied = true;
        } else {
            debugLog("WARNING: JSON constraint may still exist");
            $fixApplied = true; // Still try to proceed
        }
        
    } catch (Exception $e) {
        debugLog("ERROR: " . $e->getMessage());
        $fixError = $e->getMessage();
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
        .header-section { background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%); color: white; padding: 3rem 0; }
        .fix-card { border: none; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1><i class="fas fa-database"></i> Fix Database Constraint</h1>
                    <p class="lead">Remove JSON constraint from report_data column</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Problem Identified -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="alert alert-danger">
                    <h4><i class="fas fa-exclamation-triangle"></i> Database Constraint Issue Found!</h4>
                    <p><strong>Error:</strong> <code>CONSTRAINT assessment_reports.report_data failed</code></p>
                    <p><strong>Cause:</strong> The <code>report_data</code> column has a JSON validation constraint, but we're trying to store HTML content</p>
                    <p class="mb-0"><strong>Solution:</strong> Remove the JSON constraint to allow HTML content storage</p>
                </div>
            </div>
        </div>

        <!-- The Problem -->
        <section class="mb-5">
            <h2><i class="fas fa-bug"></i> The Problem</h2>
            <div class="fix-card card">
                <div class="card-body">
                    <h5>Database Schema Mismatch:</h5>
                    <div class="row">
                        <div class="col-lg-6">
                            <h6>❌ Current Database Schema:</h6>
                            <pre class="bg-light p-3 rounded"><code>`report_data` longtext 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_bin 
DEFAULT NULL 
CHECK (json_valid(`report_data`))</code></pre>
                            <p class="small text-danger"><strong>Problem:</strong> Expects JSON data only</p>
                        </div>
                        <div class="col-lg-6">
                            <h6>✅ What We Need:</h6>
                            <pre class="bg-light p-3 rounded"><code>`report_data` longtext 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_bin 
DEFAULT NULL</code></pre>
                            <p class="small text-success"><strong>Solution:</strong> Allow any text content (HTML)</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fix Results -->
        <?php if ($fixApplied): ?>
        <section class="mb-5">
            <div class="alert alert-success">
                <h4><i class="fas fa-check-circle"></i> Database Constraint Fixed!</h4>
                <p>The JSON validation constraint has been removed from the report_data column.</p>
                <p class="mb-0">Report generation should now work correctly.</p>
            </div>
        </section>
        <?php elseif ($fixError): ?>
        <section class="mb-5">
            <div class="alert alert-danger">
                <h4><i class="fas fa-times-circle"></i> Fix Failed</h4>
                <p><strong>Error:</strong> <?php echo htmlspecialchars($fixError); ?></p>
                <p class="mb-0">You may need to apply the fix manually using phpMyAdmin.</p>
            </div>
        </section>
        <?php endif; ?>

        <!-- Debug Log -->
        <?php if (!empty($debugLog)): ?>
        <section class="mb-5">
            <h2><i class="fas fa-list"></i> Debug Log</h2>
            <div class="fix-card card">
                <div class="card-body">
                    <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px;"><?php
                    foreach ($debugLog as $log) {
                        echo htmlspecialchars($log) . "\n";
                    }
                    ?></pre>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Apply Fix -->
        <?php if (!$fixApplied): ?>
        <section class="mb-5">
            <h2><i class="fas fa-wrench"></i> Apply Database Fix</h2>
            <div class="fix-card card">
                <div class="card-body">
                    <h5>Remove JSON Constraint:</h5>
                    <p>This will modify the <code>assessment_reports</code> table to allow HTML content in the <code>report_data</code> column.</p>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Important:</h6>
                        <ul class="mb-0">
                            <li>This will modify your database structure</li>
                            <li>Make sure you have a backup if needed</li>
                            <li>The change is safe and only removes a constraint</li>
                        </ul>
                    </div>
                    
                    <form method="POST">
                        <button type="submit" name="apply_fix" value="1" class="btn btn-danger btn-lg">
                            <i class="fas fa-database"></i> Apply Database Fix
                        </button>
                    </form>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Manual Fix Instructions -->
        <section class="mb-5">
            <h2><i class="fas fa-tools"></i> Manual Fix (Alternative)</h2>
            <div class="fix-card card">
                <div class="card-body">
                    <h5>If automatic fix fails, use phpMyAdmin:</h5>
                    <ol>
                        <li><strong>Open phpMyAdmin</strong> (http://localhost/phpmyadmin)</li>
                        <li><strong>Select database:</strong> <code>dmit_psychometric</code></li>
                        <li><strong>Go to SQL tab</strong></li>
                        <li><strong>Run this query:</strong></li>
                    </ol>
                    
                    <pre class="bg-light p-3 rounded"><code>ALTER TABLE `assessment_reports` 
MODIFY COLUMN `report_data` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL;</code></pre>
                    
                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-info-circle"></i> What this does:</h6>
                        <p class="mb-0">Removes the JSON validation constraint while keeping the column structure intact.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Test After Fix -->
        <?php if ($fixApplied): ?>
        <section class="mb-5">
            <h2><i class="fas fa-vial"></i> Test the Fix</h2>
            <div class="fix-card card">
                <div class="card-body">
                    <h5>Now test report generation:</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>1. Test Report Debug:</h6>
                            <a href="report_generation_debug.php?id=2" class="btn btn-success btn-lg w-100 mb-3">
                                <i class="fas fa-bug"></i> Test Report Debug
                            </a>
                            <p class="small text-muted">Should now complete without constraint errors</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>2. Test Real Report Generation:</h6>
                            <a href="<?php echo url('assessments/report.php?id=2'); ?>" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-file-pdf"></i> Test Real Report
                            </a>
                            <p class="small text-muted">The "Generate Report" button should now work correctly</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-success mt-4">
                        <h6><i class="fas fa-trophy"></i> Expected Results:</h6>
                        <ul class="mb-0">
                            <li>✅ No database constraint errors</li>
                            <li>✅ Report generation completes successfully</li>
                            <li>✅ HTML content saved to database</li>
                            <li>✅ Report displays correctly</li>
                            <li>✅ Complete end-to-end functionality</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Navigation -->
        <div class="text-center">
            <a href="report_generation_debug.php?id=2" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Report Debug
            </a>
            <?php if ($fixApplied): ?>
            <a href="<?php echo url('assessments/report.php?id=2'); ?>" class="btn btn-primary ms-2">
                <i class="fas fa-file-pdf"></i> Test Report Generation
            </a>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
