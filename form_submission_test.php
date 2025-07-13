<?php
/**
 * Form Submission Test - DMIT Psychometric Test System
 * Test if form submission is working correctly
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';

$pageTitle = 'Form Submission Test - ' . APP_NAME;

$formSubmitted = false;
$postData = [];
$serverData = [];

// Capture all form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSubmitted = true;
    $postData = $_POST;
    $serverData = [
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
        'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? 'Not set',
        'CONTENT_LENGTH' => $_SERVER['CONTENT_LENGTH'] ?? 'Not set',
        'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? 'Not set',
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'Not set'
    ];
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
        <h1><i class="fas fa-paper-plane"></i> Form Submission Test</h1>
        <p class="lead">Test if form submission is working correctly</p>

        <?php if ($formSubmitted): ?>
        <!-- Form Submission Results -->
        <div class="alert alert-success">
            <h5><i class="fas fa-check-circle"></i> Form Submitted Successfully!</h5>
            <p>The form submission is working correctly. The issue is not with form submission itself.</p>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>POST Data Received</h5>
            </div>
            <div class="card-body">
                <pre><?php echo htmlspecialchars(print_r($postData, true)); ?></pre>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>Server Information</h5>
            </div>
            <div class="card-body">
                <pre><?php echo htmlspecialchars(print_r($serverData, true)); ?></pre>
            </div>
        </div>

        <div class="alert alert-info">
            <h6><i class="fas fa-lightbulb"></i> Conclusion:</h6>
            <p class="mb-0">Since form submission works, the issue is likely in the analysis processing or redirect logic in the actual analysis.php file.</p>
        </div>
        <?php endif; ?>

        <!-- Test Form (Same structure as analysis form) -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-vial"></i> Test Form (Same as Analysis Button)</h5>
            </div>
            <div class="card-body">
                <p>This form has the same structure as the "Perform DMIT Analysis" button:</p>
                
                <form method="POST" id="testForm">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <button type="submit" name="perform_analysis" class="btn btn-primary btn-lg" id="testBtn">
                        <i class="fas fa-cogs"></i> Test Form Submission
                    </button>
                </form>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        This tests if the form structure and submission mechanism works.
                    </small>
                </div>
            </div>
        </div>

        <!-- JavaScript Debug -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-code"></i> JavaScript Debug</h5>
            </div>
            <div class="card-body">
                <p>Test JavaScript behavior (same as analysis page):</p>
                
                <button type="button" class="btn btn-warning" id="jsTestBtn">
                    <i class="fas fa-play"></i> Test JavaScript Behavior
                </button>
                
                <div id="jsOutput" class="mt-3"></div>
            </div>
        </div>

        <!-- Network Debug -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-network-wired"></i> Network Debug Instructions</h5>
            </div>
            <div class="card-body">
                <h6>To debug the real analysis button:</h6>
                <ol>
                    <li><strong>Open Browser Developer Tools</strong> (F12)</li>
                    <li><strong>Go to Network tab</strong></li>
                    <li><strong>Go to the real analysis page:</strong>
                        <a href="<?php echo url('assessments/analysis.php?id=2'); ?>" class="btn btn-sm btn-primary ms-2" target="_blank">
                            Open Analysis Page
                        </a>
                    </li>
                    <li><strong>Click "Perform DMIT Analysis"</strong></li>
                    <li><strong>Watch the Network tab</strong> - see if any request is made</li>
                    <li><strong>Check Console tab</strong> - look for JavaScript errors</li>
                </ol>
                
                <div class="alert alert-warning mt-3">
                    <h6><i class="fas fa-exclamation-triangle"></i> What to Look For:</h6>
                    <ul class="mb-0">
                        <li><strong>No network request:</strong> JavaScript is preventing form submission</li>
                        <li><strong>Request hangs:</strong> Server-side issue (PHP timeout/error)</li>
                        <li><strong>Request completes but no redirect:</strong> Redirect function issue</li>
                        <li><strong>JavaScript errors:</strong> Script conflicts or errors</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Possible Solutions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-wrench"></i> Possible Solutions</h5>
            </div>
            <div class="card-body">
                <h6>Based on common issues:</h6>
                
                <div class="accordion" id="solutionsAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#solution1">
                                Solution 1: Remove JavaScript Button Disabling
                            </button>
                        </h2>
                        <div id="solution1" class="accordion-collapse collapse show" data-bs-parent="#solutionsAccordion">
                            <div class="accordion-body">
                                <p>The JavaScript might be preventing form submission. Try commenting out this code in analysis.php:</p>
                                <pre><code>// Comment out this JavaScript:
document.getElementById('analysisBtn')?.addEventListener('click', function() {
    this.disabled = true;
    this.innerHTML = '&lt;span class="spinner-border"&gt;&lt;/span&gt; Analyzing...';
});</code></pre>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#solution2">
                                Solution 2: Add Debug Output
                            </button>
                        </h2>
                        <div id="solution2" class="accordion-collapse collapse" data-bs-parent="#solutionsAccordion">
                            <div class="accordion-body">
                                <p>Add debug output at the start of analysis.php POST handling:</p>
                                <pre><code>if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['perform_analysis'])) {
    echo "DEBUG: Form submitted!&lt;br&gt;";
    flush();
    // ... rest of code
}</code></pre>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#solution3">
                                Solution 3: Increase PHP Timeout
                            </button>
                        </h2>
                        <div id="solution3" class="accordion-collapse collapse" data-bs-parent="#solutionsAccordion">
                            <div class="accordion-body">
                                <p>Add at the top of analysis.php:</p>
                                <pre><code>set_time_limit(300); // 5 minutes
ini_set('max_execution_time', 300);</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="text-center">
            <a href="<?php echo url('assessments/analysis.php?id=2'); ?>" class="btn btn-primary">
                <i class="fas fa-external-link-alt"></i> Test Real Analysis Page
            </a>
            <a href="analysis_button_debug.php?id=2" class="btn btn-secondary ms-2">
                <i class="fas fa-bug"></i> Debug Analysis Engine
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Test form submission behavior
        document.getElementById('testBtn')?.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Testing...';
        });

        // Test JavaScript behavior
        document.getElementById('jsTestBtn')?.addEventListener('click', function() {
            const output = document.getElementById('jsOutput');
            
            // Test if JavaScript is working
            output.innerHTML = '<div class="alert alert-info">✓ JavaScript is working correctly</div>';
            
            // Test button disabling
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Testing...';
            
            // Re-enable after 3 seconds
            setTimeout(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-play"></i> Test JavaScript Behavior';
                output.innerHTML += '<div class="alert alert-success">✓ Button disable/enable works correctly</div>';
            }, 3000);
        });

        // Log form submission attempts
        document.getElementById('testForm')?.addEventListener('submit', function(e) {
            console.log('Form submission started');
            console.log('Form data:', new FormData(this));
        });
    </script>
</body>
</html>
