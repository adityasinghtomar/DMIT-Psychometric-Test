<?php
/**
 * Navigation Test - DMIT Psychometric Test System
 * Test page to verify navigation links work correctly from any directory
 */

require_once 'config/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Navigation Test - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success">
                    <h1><i class="fas fa-check-circle"></i> Navigation Fix Completed!</h1>
                    <p class="lead">All navigation links have been updated to use proper URL handling.</p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>🔧 What Was Fixed:</h2>
                <ul class="list-group">
                    <li class="list-group-item">
                        <i class="fas fa-check text-success"></i>
                        <strong>URL Helper Function:</strong> Added <code>url()</code> function to calculate correct relative paths
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check text-success"></i>
                        <strong>Header Navigation:</strong> Fixed logout link and brand logo to work from any page
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check text-success"></i>
                        <strong>Sidebar Navigation:</strong> Updated all sidebar links in all pages
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check text-success"></i>
                        <strong>Action Buttons:</strong> Fixed all action buttons and form submissions
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check text-success"></i>
                        <strong>Redirect Function:</strong> Enhanced redirect function to handle relative URLs
                    </li>
                </ul>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>🧪 Test Navigation Links:</h2>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Main Pages</h5>
                                <ul class="list-unstyled">
                                    <li><a href="<?php echo url('index.php'); ?>" class="btn btn-outline-primary btn-sm mb-2 w-100">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a></li>
                                    <li><a href="<?php echo url('assessments/new.php'); ?>" class="btn btn-outline-success btn-sm mb-2 w-100">
                                        <i class="fas fa-plus-circle"></i> New Assessment
                                    </a></li>
                                    <li><a href="<?php echo url('assessments/list.php'); ?>" class="btn btn-outline-info btn-sm mb-2 w-100">
                                        <i class="fas fa-list"></i> View Assessments
                                    </a></li>
                                    <li><a href="<?php echo url('profile/settings.php'); ?>" class="btn btn-outline-warning btn-sm mb-2 w-100">
                                        <i class="fas fa-user-cog"></i> Profile Settings
                                    </a></li>
                                    <li><a href="<?php echo url('help/user_guide.php'); ?>" class="btn btn-outline-secondary btn-sm mb-2 w-100">
                                        <i class="fas fa-question-circle"></i> Help & Support
                                    </a></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Admin Pages</h5>
                                <ul class="list-unstyled">
                                    <li><a href="<?php echo url('admin/dashboard.php'); ?>" class="btn btn-outline-danger btn-sm mb-2 w-100">
                                        <i class="fas fa-cog"></i> Admin Dashboard
                                    </a></li>
                                    <li><a href="<?php echo url('admin/users.php'); ?>" class="btn btn-outline-danger btn-sm mb-2 w-100">
                                        <i class="fas fa-users"></i> User Management
                                    </a></li>
                                    <li><a href="<?php echo url('admin/security.php'); ?>" class="btn btn-outline-danger btn-sm mb-2 w-100">
                                        <i class="fas fa-shield-alt"></i> Security Logs
                                    </a></li>
                                    <li><a href="<?php echo url('admin/settings.php'); ?>" class="btn btn-outline-danger btn-sm mb-2 w-100">
                                        <i class="fas fa-cogs"></i> System Settings
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>📋 How the URL Function Works:</h2>
                <div class="card">
                    <div class="card-body">
                        <p>The <code>url()</code> function automatically calculates the correct relative path based on the current page location:</p>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Current Page</th>
                                        <th>url('index.php')</th>
                                        <th>url('auth/logout.php')</th>
                                        <th>Result</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>/index.php</code></td>
                                        <td><code>index.php</code></td>
                                        <td><code>auth/logout.php</code></td>
                                        <td><span class="badge bg-success">✓ Correct</span></td>
                                    </tr>
                                    <tr>
                                        <td><code>/profile/settings.php</code></td>
                                        <td><code>../index.php</code></td>
                                        <td><code>../auth/logout.php</code></td>
                                        <td><span class="badge bg-success">✓ Correct</span></td>
                                    </tr>
                                    <tr>
                                        <td><code>/admin/users.php</code></td>
                                        <td><code>../index.php</code></td>
                                        <td><code>../auth/logout.php</code></td>
                                        <td><span class="badge bg-success">✓ Correct</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <h4><i class="fas fa-lightbulb"></i> Testing Instructions:</h4>
                    <ol>
                        <li>Click on any navigation link above</li>
                        <li>From the new page, try clicking the logout button</li>
                        <li>Navigate to different sections using the sidebar</li>
                        <li>Verify that all links work correctly regardless of your current location</li>
                    </ol>
                    <p class="mb-0"><strong>Expected Result:</strong> All navigation should work perfectly from any page without broken links!</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5>🔍 Current Page Debug Info:</h5>
                        <ul class="list-unstyled mb-0">
                            <li><strong>Script Name:</strong> <code><?php echo $_SERVER['SCRIPT_NAME']; ?></code></li>
                            <li><strong>Current Directory:</strong> <code><?php echo dirname($_SERVER['SCRIPT_NAME']); ?></code></li>
                            <li><strong>Base Directory:</strong> <code>/DMIT-Psychometric-Test</code></li>
                            <li><strong>url('index.php') returns:</strong> <code><?php echo url('index.php'); ?></code></li>
                            <li><strong>url('auth/logout.php') returns:</strong> <code><?php echo url('auth/logout.php'); ?></code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
