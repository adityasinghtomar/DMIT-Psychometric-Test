<?php
/**
 * Test Redirect from Analysis - DMIT Psychometric Test System
 * Test if redirect works from assessments directory
 */

require_once 'config/config.php';

$pageTitle = 'Test Redirect - ' . APP_NAME;

// Test different redirect scenarios
if ($_POST['test_redirect'] ?? false) {
    $redirectType = $_POST['redirect_type'] ?? '';
    
    switch ($redirectType) {
        case 'report_relative':
            // Test relative redirect (same as analysis.php does)
            redirect("assessments/report.php?id=1", 'Test redirect to report (relative)', 'success');
            break;
            
        case 'report_absolute':
            // Test absolute redirect
            redirect(BASE_URL . "assessments/report.php?id=1", 'Test redirect to report (absolute)', 'success');
            break;
            
        case 'list_relative':
            // Test redirect to list
            redirect("assessments/list.php", 'Test redirect to list', 'success');
            break;
            
        case 'index':
            // Test redirect to index
            redirect("index.php", 'Test redirect to index', 'success');
            break;
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
</head>
<body>
    <div class="container my-5">
        <h1><i class="fas fa-route"></i> Test Redirect from Analysis</h1>
        <p class="lead">Test different redirect scenarios to identify the issue</p>

        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> Testing Redirect Function</h5>
            <p>The analysis.php page uses this redirect after successful analysis:</p>
            <code>redirect("report.php?id=$subjectId", 'Analysis completed successfully. Report is ready.', 'success');</code>
            <p class="mt-2 mb-0">Let's test if this redirect pattern works correctly.</p>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Test Redirects</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="d-grid gap-2">
                                <button type="submit" name="test_redirect" value="1" onclick="setRedirectType('report_relative')" class="btn btn-primary">
                                    <i class="fas fa-file-pdf"></i> Test Report Redirect (Relative)
                                </button>
                                
                                <button type="submit" name="test_redirect" value="1" onclick="setRedirectType('report_absolute')" class="btn btn-outline-primary">
                                    <i class="fas fa-file-pdf"></i> Test Report Redirect (Absolute)
                                </button>
                                
                                <button type="submit" name="test_redirect" value="1" onclick="setRedirectType('list_relative')" class="btn btn-success">
                                    <i class="fas fa-list"></i> Test List Redirect
                                </button>
                                
                                <button type="submit" name="test_redirect" value="1" onclick="setRedirectType('index')" class="btn btn-info">
                                    <i class="fas fa-home"></i> Test Index Redirect
                                </button>
                            </div>
                            <input type="hidden" name="redirect_type" id="redirectType">
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Expected Results</h5>
                    </div>
                    <div class="card-body">
                        <ul class="small">
                            <li><strong>Report (Relative):</strong> Should go to assessments/report.php?id=1</li>
                            <li><strong>Report (Absolute):</strong> Should go to assessments/report.php?id=1</li>
                            <li><strong>List:</strong> Should go to assessments/list.php</li>
                            <li><strong>Index:</strong> Should go to index.php</li>
                        </ul>
                        
                        <div class="alert alert-warning mt-3">
                            <small>
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Note:</strong> If any of these redirects fail or hang, we've found the issue!
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Debug Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Current Environment:</h6>
                        <ul class="small">
                            <li><strong>Script:</strong> <?php echo $_SERVER['SCRIPT_NAME']; ?></li>
                            <li><strong>Directory:</strong> <?php echo dirname($_SERVER['SCRIPT_NAME']); ?></li>
                            <li><strong>Base URL:</strong> <?php echo BASE_URL; ?></li>
                            <li><strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI']; ?></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>URL Function Tests:</h6>
                        <ul class="small">
                            <li><strong>url('assessments/report.php?id=1'):</strong> <?php echo url('assessments/report.php?id=1'); ?></li>
                            <li><strong>url('assessments/list.php'):</strong> <?php echo url('assessments/list.php'); ?></li>
                            <li><strong>url('index.php'):</strong> <?php echo url('index.php'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Troubleshooting Steps</h5>
            </div>
            <div class="card-body">
                <ol>
                    <li><strong>Test the debug analysis:</strong>
                        <a href="analysis_button_debug.php?id=2" class="btn btn-sm btn-primary ms-2">Debug Analysis</a>
                    </li>
                    <li><strong>Check if redirects work:</strong> Use the buttons above</li>
                    <li><strong>Compare with real analysis:</strong>
                        <a href="<?php echo url('assessments/analysis.php?id=2'); ?>" class="btn btn-sm btn-secondary ms-2">Real Analysis</a>
                    </li>
                    <li><strong>Check browser console:</strong> Look for JavaScript errors</li>
                </ol>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setRedirectType(type) {
            document.getElementById('redirectType').value = type;
        }
    </script>
</body>
</html>
