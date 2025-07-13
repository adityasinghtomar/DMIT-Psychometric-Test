<?php
/**
 * Test Header Fix - DMIT Psychometric Test System
 * Simple test page to verify header overlap is fixed
 */

require_once 'config/config.php';

Security::requireAuth();

$pageTitle = 'Test Header Fix - ' . APP_NAME;
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

    <!-- Force proper spacing with important declarations -->
    <style>
        body {
            padding-top: 70px !important;
        }
        .container-fluid {
            margin-top: 0 !important;
            padding-top: 20px !important;
        }
        .main-content {
            margin-top: 0 !important;
            padding-top: 30px !important;
        }
        .test-header {
            background: #f8f9fa;
            border: 2px solid #007bff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
    </style>

    <div class="container-fluid" style="margin-top: 0 !important; padding-top: 20px !important;">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="assessments/new.php">
                                <i class="fas fa-plus-circle"></i> New Assessment
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="assessments/list.php">
                                <i class="fas fa-list"></i> View Assessments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="assessments/report.php?id=2">
                                <i class="fas fa-file-pdf"></i> Test Report
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content" style="margin-top: 0 !important; padding-top: 30px !important;">
                
                <!-- Test Header Visibility -->
                <div class="test-header">
                    <h1 class="h2 mb-3">
                        <i class="fas fa-check-circle text-success"></i> 
                        Header Fix Test Page
                    </h1>
                    <p class="lead">If you can see this header clearly without any overlap from the navbar, the fix is working!</p>
                </div>

                <!-- Visual Test -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-eye"></i> Visual Test</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> What to Check:</h6>
                                    <ul class="mb-0">
                                        <li>✅ This blue header box should be fully visible</li>
                                        <li>✅ No part should be hidden behind the navbar</li>
                                        <li>✅ There should be proper spacing from the top</li>
                                        <li>✅ The sidebar should not overlap any content</li>
                                    </ul>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h6>Spacing Test:</h6>
                                        <div style="background: linear-gradient(to bottom, #ff0000, #00ff00); height: 100px; border: 1px solid #000;">
                                            <div class="text-center pt-4">
                                                <strong>Gradient Test Box</strong><br>
                                                <small>Should be fully visible</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Content Test:</h6>
                                        <p>This is a test paragraph to verify that all content is properly positioned and visible. The text should not be cut off or hidden behind any fixed elements.</p>
                                        <button class="btn btn-success">Test Button</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Test Results -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Test Results</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>✅ If Working Correctly:</h6>
                                        <ul>
                                            <li>Blue header box fully visible</li>
                                            <li>Proper spacing from navbar</li>
                                            <li>Gradient test box complete</li>
                                            <li>All text readable</li>
                                            <li>No overlapping elements</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>❌ If Still Broken:</h6>
                                        <ul>
                                            <li>Header partially hidden</li>
                                            <li>Content behind navbar</li>
                                            <li>Gradient box cut off</li>
                                            <li>Text not fully visible</li>
                                            <li>Overlapping elements</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Test -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-link"></i> Navigation Test</h5>
                            </div>
                            <div class="card-body">
                                <p>Test the actual report page to see if the fix works there too:</p>
                                <div class="d-grid gap-2 d-md-flex">
                                    <a href="<?php echo url('assessments/report.php?id=2'); ?>" class="btn btn-primary">
                                        <i class="fas fa-file-pdf"></i> Test Report Page
                                    </a>
                                    <a href="<?php echo url('assessments/list.php'); ?>" class="btn btn-secondary">
                                        <i class="fas fa-list"></i> Test List Page
                                    </a>
                                    <a href="<?php echo url('index.php'); ?>" class="btn btn-info">
                                        <i class="fas fa-home"></i> Test Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Browser Info -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-browser"></i> Browser Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>User Agent:</strong> <code id="userAgent"></code></p>
                                <p><strong>Viewport:</strong> <span id="viewport"></span></p>
                                <p><strong>Screen:</strong> <span id="screen"></span></p>
                                
                                <div class="alert alert-warning mt-3">
                                    <h6><i class="fas fa-exclamation-triangle"></i> If Still Having Issues:</h6>
                                    <ol>
                                        <li>Try refreshing the page (Ctrl+F5)</li>
                                        <li>Clear browser cache</li>
                                        <li>Try a different browser</li>
                                        <li>Check browser console for errors (F12)</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Display browser information
        document.getElementById('userAgent').textContent = navigator.userAgent;
        document.getElementById('viewport').textContent = window.innerWidth + ' x ' + window.innerHeight;
        document.getElementById('screen').textContent = screen.width + ' x ' + screen.height;
        
        // Update viewport on resize
        window.addEventListener('resize', function() {
            document.getElementById('viewport').textContent = window.innerWidth + ' x ' + window.innerHeight;
        });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
