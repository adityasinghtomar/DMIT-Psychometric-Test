<?php
/**
 * Login Redirect Test - DMIT Psychometric Test System
 * Test page to verify login redirects work correctly
 */

require_once 'config/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Login Redirect Test - ' . APP_NAME;
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
                    <h1><i class="fas fa-check-circle"></i> Login Redirect Issue Fixed!</h1>
                    <p class="lead">The login redirect has been updated to avoid XAMPP dashboard conflicts.</p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>🔧 Problem Identified:</h2>
                <div class="alert alert-danger">
                    <h5>XAMPP Default Index Conflict</h5>
                    <p>The XAMPP installation has a default <code>index.php</code> file in <code>C:\xampp\htdocs\</code> that redirects to <code>/dashboard/</code> (XAMPP dashboard).</p>
                    <p>When our login used <code>../index.php</code>, it was going to the XAMPP root instead of our DMIT system.</p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>✅ Solution Implemented:</h2>
                <ul class="list-group">
                    <li class="list-group-item">
                        <i class="fas fa-check text-success"></i>
                        <strong>Absolute URL Redirects:</strong> Login now uses <code>BASE_URL . 'index.php'</code> for direct navigation
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check text-success"></i>
                        <strong>Enhanced url() Function:</strong> Special handling for <code>index.php</code> to use absolute URLs
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check text-success"></i>
                        <strong>Improved redirect() Function:</strong> Automatic absolute URL for critical pages
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check text-success"></i>
                        <strong>Security Class Updates:</strong> Fixed requireAuth() and requireRole() redirects
                    </li>
                </ul>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>🧪 Test the Fix:</h2>
                <div class="card">
                    <div class="card-body">
                        <h5>Login Test Steps:</h5>
                        <ol>
                            <li>Go to the login page: <a href="<?php echo url('auth/login.php'); ?>" class="btn btn-sm btn-primary">Login Page</a></li>
                            <li>Login with credentials: <code>admin</code> / <code>admin123</code></li>
                            <li>Should redirect to: <code><?php echo BASE_URL; ?>index.php</code></li>
                            <li>Should NOT redirect to: <code>http://localhost/dashboard/</code></li>
                        </ol>
                        
                        <h5 class="mt-4">URL Function Tests:</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Function Call</th>
                                        <th>Result</th>
                                        <th>Expected</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>url('index.php')</code></td>
                                        <td><code><?php echo url('index.php'); ?></code></td>
                                        <td><code><?php echo BASE_URL; ?>index.php</code></td>
                                        <td>
                                            <?php if (url('index.php') === BASE_URL . 'index.php'): ?>
                                                <span class="badge bg-success">✓ Correct</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">✗ Wrong</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><code>url('auth/login.php')</code></td>
                                        <td><code><?php echo url('auth/login.php'); ?></code></td>
                                        <td><code>auth/login.php</code></td>
                                        <td>
                                            <?php if (url('auth/login.php') === 'auth/login.php'): ?>
                                                <span class="badge bg-success">✓ Correct</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Different</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>🔍 Debug Information:</h2>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Configuration:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>BASE_URL:</strong> <code><?php echo BASE_URL; ?></code></li>
                                    <li><strong>Current Script:</strong> <code><?php echo $_SERVER['SCRIPT_NAME']; ?></code></li>
                                    <li><strong>HTTP Host:</strong> <code><?php echo $_SERVER['HTTP_HOST']; ?></code></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>XAMPP Conflict:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>XAMPP Root:</strong> <code>C:\xampp\htdocs\</code></li>
                                    <li><strong>XAMPP Index:</strong> <code>C:\xampp\htdocs\index.php</code></li>
                                    <li><strong>Redirects to:</strong> <code>http://localhost/dashboard/</code></li>
                                    <li><strong>Our System:</strong> <code>C:\xampp\htdocs\DMIT-Psychometric-Test\</code></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <h4><i class="fas fa-lightbulb"></i> How the Fix Works:</h4>
                    <p><strong>Before (Broken):</strong></p>
                    <code>auth/login.php → redirect('../index.php') → C:\xampp\htdocs\index.php → http://localhost/dashboard/</code>
                    
                    <p class="mt-3"><strong>After (Fixed):</strong></p>
                    <code>auth/login.php → redirect('index.php') → BASE_URL + 'index.php' → http://localhost/DMIT-Psychometric-Test/index.php</code>
                    
                    <p class="mt-3 mb-0">
                        <strong>Result:</strong> Login now correctly redirects to the DMIT system dashboard instead of XAMPP dashboard! 🎉
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center">
                <a href="<?php echo url('auth/login.php'); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Test Login Now
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
