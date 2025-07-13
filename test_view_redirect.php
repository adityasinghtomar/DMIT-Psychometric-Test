<?php
/**
 * Test View Redirect - DMIT Psychometric Test System
 * Debug page to test view.php redirect issues
 */

require_once 'config/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Test View Redirect - ' . APP_NAME;
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
        <h1><i class="fas fa-bug"></i> View Redirect Debug</h1>
        
        <div class="alert alert-warning">
            <h4>Problem:</h4>
            <p>Update Assessment redirects to <code>http://localhost/DMIT-Psychometric-Test/view.php?id=1</code> which shows 404</p>
            <p>Expected: <code>http://localhost/DMIT-Psychometric-Test/assessments/view.php?id=1</code></p>
        </div>

        <h2>File Existence Check:</h2>
        <ul>
            <li><strong>assessments/view.php exists:</strong> 
                <?php echo file_exists('assessments/view.php') ? '✅ Yes' : '❌ No'; ?>
            </li>
            <li><strong>view.php in root exists:</strong> 
                <?php echo file_exists('view.php') ? '✅ Yes' : '❌ No'; ?>
            </li>
        </ul>

        <h2>URL Function Tests:</h2>
        <ul>
            <li><strong>url('assessments/view.php?id=1'):</strong> <code><?php echo url('assessments/view.php?id=1'); ?></code></li>
            <li><strong>From assessments/ directory, url('view.php?id=1') should be:</strong> <code>view.php?id=1</code></li>
        </ul>

        <h2>Test Links:</h2>
        <div class="row">
            <div class="col-md-6">
                <h5>Direct Links:</h5>
                <ul>
                    <li><a href="assessments/view.php?id=1" target="_blank">assessments/view.php?id=1</a></li>
                    <li><a href="view.php?id=1" target="_blank">view.php?id=1 (should be 404)</a></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5>Using URL Function:</h5>
                <ul>
                    <li><a href="<?php echo url('assessments/view.php?id=1'); ?>" target="_blank">url('assessments/view.php?id=1')</a></li>
                </ul>
            </div>
        </div>

        <h2>Redirect Function Test:</h2>
        <p>The issue might be in the redirect function. Let's check what happens when we redirect from assessments/edit.php to view.php?id=1</p>
        
        <div class="alert alert-info">
            <h5>Analysis:</h5>
            <p>When edit.php (in assessments/) redirects to "view.php?id=1", the redirect function might be processing it incorrectly.</p>
            <p>The redirect function calls url() which might be calculating the wrong path.</p>
        </div>

        <h2>Solution:</h2>
        <p>The redirect in edit.php should be relative to the same directory since both edit.php and view.php are in assessments/</p>
        
        <div class="card">
            <div class="card-body">
                <h6>Current (Problematic):</h6>
                <code>redirect("view.php?id=$subjectId");</code>
                
                <h6 class="mt-3">Should be:</h6>
                <code>header('Location: view.php?id=' . $subjectId); exit();</code>
                <p class="small text-muted">Or ensure the redirect function handles same-directory redirects correctly</p>
            </div>
        </div>

        <div class="mt-4">
            <a href="assessments/edit.php?id=1" class="btn btn-primary">Test Edit Page</a>
            <a href="assessments/view.php?id=1" class="btn btn-success">Test View Page</a>
        </div>
    </div>
</body>
</html>
