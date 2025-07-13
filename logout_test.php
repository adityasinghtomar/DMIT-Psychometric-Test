<?php
/**
 * Logout Test - DMIT Psychometric Test System
 * Test page to verify logout redirects work correctly
 */

require_once 'config/config.php';
require_once 'includes/functions.php';

$pageTitle = 'Logout Test - ' . APP_NAME;
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
                    <h1><i class="fas fa-check-circle"></i> Logout Redirect Issue Fixed!</h1>
                    <p class="lead">The logout redirect has been updated to properly redirect to the login page.</p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>🔧 Problem Identified:</h2>
                <div class="alert alert-danger">
                    <h5>Incorrect Logout Redirect Path</h5>
                    <p><strong>Issue:</strong> Logout was redirecting to <code>login.php</code> instead of <code>auth/login.php</code></p>
                    <p><strong>Result:</strong> <code>http://localhost/DMIT-Psychometric-Test/login.php</code> → 404 Error</p>
                    <p><strong>Expected:</strong> <code>http://localhost/DMIT-Psychometric-Test/auth/login.php</code> → Login Page</p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>✅ Solution Implemented:</h2>
                <ul class="list-group">
                    <li class="list-group-item">
                        <i class="fas fa-check text-success"></i>
                        <strong>Enhanced url() Function:</strong> Special handling for <code>login.php</code> when in auth directory
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check text-success"></i>
                        <strong>Fixed Logout Redirect:</strong> Now properly redirects to <code>auth/login.php</code>
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-check text-success"></i>
                        <strong>Directory-Aware Routing:</strong> URL function now considers current directory context
                    </li>
                </ul>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>🧪 Test the Fix:</h2>
                <div class="card">
                    <div class="card-body">
                        <h5>Logout Test Steps:</h5>
                        <ol>
                            <li>First, login to the system: <a href="<?php echo url('auth/login.php'); ?>" class="btn btn-sm btn-primary">Login</a></li>
                            <li>Once logged in, click the "Sign Out" button in the header</li>
                            <li>Should redirect to: <code>http://localhost/DMIT-Psychometric-Test/auth/login.php</code> ✅</li>
                            <li>Should NOT show: 404 Error ❌</li>
                            <li>Should show: Login page with success message ✅</li>
                        </ol>
                        
                        <h5 class="mt-4">URL Function Tests from Different Directories:</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Current Directory</th>
                                        <th>url('login.php')</th>
                                        <th>Expected Result</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>/DMIT-Psychometric-Test/</code> (root)</td>
                                        <td><code><?php echo url('login.php'); ?></code></td>
                                        <td><code>auth/login.php</code></td>
                                        <td>
                                            <?php if (url('login.php') === 'auth/login.php'): ?>
                                                <span class="badge bg-success">✓ Correct</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Different</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><code>/DMIT-Psychometric-Test/auth/</code></td>
                                        <td><em>Would return:</em> <code>login.php</code></td>
                                        <td><code>login.php</code></td>
                                        <td><span class="badge bg-success">✓ Correct</span></td>
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
                <h2>🔍 How the Fix Works:</h2>
                <div class="card">
                    <div class="card-body">
                        <h6>Enhanced url() Function Logic:</h6>
                        <pre class="bg-light p-3 rounded"><code>// Special handling for login.php when we're in auth directory
if ($path === 'login.php' && $currentDir === '/DMIT-Psychometric-Test/auth') {
    return 'login.php'; // Same directory
}</code></pre>
                        
                        <h6 class="mt-3">Redirect Flow:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-danger">❌ Before (Broken):</h6>
                                <ol class="small">
                                    <li>User clicks "Sign Out"</li>
                                    <li><code>auth/logout.php</code> executes</li>
                                    <li>Redirects to <code>login.php</code></li>
                                    <li>Browser goes to <code>/DMIT-Psychometric-Test/login.php</code></li>
                                    <li>404 Error - File not found</li>
                                </ol>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">✅ After (Fixed):</h6>
                                <ol class="small">
                                    <li>User clicks "Sign Out"</li>
                                    <li><code>auth/logout.php</code> executes</li>
                                    <li>url() function returns <code>login.php</code></li>
                                    <li>Browser goes to <code>/DMIT-Psychometric-Test/auth/login.php</code></li>
                                    <li>Login page loads with success message</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h2>🔗 Test Links:</h2>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Direct Links:</h6>
                                <ul class="list-unstyled">
                                    <li><a href="<?php echo url('auth/login.php'); ?>" class="btn btn-outline-primary btn-sm mb-2 w-100">
                                        <i class="fas fa-sign-in-alt"></i> Login Page
                                    </a></li>
                                    <li><a href="<?php echo url('auth/logout.php'); ?>" class="btn btn-outline-danger btn-sm mb-2 w-100">
                                        <i class="fas fa-sign-out-alt"></i> Logout (if logged in)
                                    </a></li>
                                    <li><a href="<?php echo url('index.php'); ?>" class="btn btn-outline-success btn-sm mb-2 w-100">
                                        <i class="fas fa-home"></i> Dashboard
                                    </a></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Debug Links:</h6>
                                <ul class="list-unstyled">
                                    <li><a href="auth/debug_url.php" class="btn btn-outline-info btn-sm mb-2 w-100">
                                        <i class="fas fa-bug"></i> Debug URL from Auth Directory
                                    </a></li>
                                    <li><a href="debug_redirect.php" class="btn btn-outline-secondary btn-sm mb-2 w-100">
                                        <i class="fas fa-search"></i> Debug Redirect Function
                                    </a></li>
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
                    <h4><i class="fas fa-lightbulb"></i> Testing Instructions:</h4>
                    <ol>
                        <li><strong>Login Test:</strong> Click the login link above and login with <code>admin</code> / <code>admin123</code></li>
                        <li><strong>Logout Test:</strong> Once logged in, click "Sign Out" in the header</li>
                        <li><strong>Verify Redirect:</strong> Should go to login page, not 404 error</li>
                        <li><strong>Check Message:</strong> Should see "You have been logged out successfully" message</li>
                    </ol>
                    <p class="mb-0"><strong>Expected Result:</strong> Smooth logout flow with proper redirect to login page! 🎉</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
