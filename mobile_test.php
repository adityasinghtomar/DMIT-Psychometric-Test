<?php
/**
 * Mobile Responsiveness Test - DMIT Psychometric Test System
 * Test page to demonstrate mobile-friendly features
 */

require_once 'config/config.php';

// Allow access without authentication for testing
$pageTitle = 'Mobile Test - ' . APP_NAME;
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
    
    <style>
        .mobile-demo {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        
        .feature-card {
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .device-frame {
            border: 3px solid #333;
            border-radius: 20px;
            padding: 20px;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        @media (max-width: 768px) {
            .mobile-demo {
                padding: 1rem 0;
            }
            
            .device-frame {
                border-radius: 10px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="mobile-demo">
        <div class="container">
            <div class="text-center">
                <h1><i class="fas fa-mobile-alt"></i> DMIT System - Mobile Ready</h1>
                <p class="lead">Fully responsive design that works perfectly on all devices</p>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Mobile Features -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center mb-4">📱 Mobile-Friendly Features</h2>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-expand-arrows-alt fa-3x text-primary mb-3"></i>
                        <h5>Responsive Layout</h5>
                        <p>Bootstrap 5 grid system ensures perfect layout on all screen sizes</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-bars fa-3x text-success mb-3"></i>
                        <h5>Mobile Navigation</h5>
                        <p>Collapsible sidebar with hamburger menu for easy mobile navigation</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-table fa-3x text-info mb-3"></i>
                        <h5>Responsive Tables</h5>
                        <p>Tables scroll horizontally and hide less important columns on mobile</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-fingerprint fa-3x text-warning mb-3"></i>
                        <h5>Touch-Friendly Forms</h5>
                        <p>Optimized form layouts and input sizes for mobile interaction</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-bar fa-3x text-danger mb-3"></i>
                        <h5>Adaptive Charts</h5>
                        <p>Charts and visualizations automatically resize for mobile screens</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-file-pdf fa-3x text-secondary mb-3"></i>
                        <h5>Mobile Reports</h5>
                        <p>PDF reports are optimized for viewing on mobile devices</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsive Demo -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center mb-4">📊 Responsive Components Demo</h2>
            </div>
            
            <!-- Statistics Cards -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-header">Assessments</div>
                    <div class="card-body">
                        <h4 class="card-title">25</h4>
                        <p class="card-text">Total completed</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-header">Users</div>
                    <div class="card-body">
                        <h4 class="card-title">12</h4>
                        <p class="card-text">Active users</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-white bg-info">
                    <div class="card-header">Reports</div>
                    <div class="card-body">
                        <h4 class="card-title">18</h4>
                        <p class="card-text">Generated</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-header">Pending</div>
                    <div class="card-body">
                        <h4 class="card-title">3</h4>
                        <p class="card-text">Awaiting analysis</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsive Table -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>📋 Responsive Table Example</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Age</th>
                                        <th class="d-none d-md-table-cell">Date</th>
                                        <th class="d-none d-lg-table-cell">Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>John Doe</td>
                                        <td>15</td>
                                        <td class="d-none d-md-table-cell">Jan 15, 2024</td>
                                        <td class="d-none d-lg-table-cell"><span class="badge bg-success">Complete</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">View</button>
                                                <button class="btn btn-outline-secondary d-none d-sm-inline">Edit</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jane Smith</td>
                                        <td>12</td>
                                        <td class="d-none d-md-table-cell">Jan 14, 2024</td>
                                        <td class="d-none d-lg-table-cell"><span class="badge bg-warning">Pending</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-success">Analyze</button>
                                                <button class="btn btn-outline-secondary d-none d-sm-inline">Edit</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Form Example -->
        <div class="row mb-5">
            <div class="col-lg-8 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>📝 Mobile-Optimized Form</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Subject Name</label>
                                    <input type="text" class="form-control" placeholder="Enter name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Age</label>
                                    <input type="number" class="form-control" placeholder="Age">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gender</label>
                                    <select class="form-select">
                                        <option>Select Gender</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-secondary me-md-2">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Assessment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-12 mt-3 mt-lg-0">
                <div class="card">
                    <div class="card-header">
                        <h6>💡 Mobile Tips</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Touch-friendly buttons</li>
                            <li><i class="fas fa-check text-success"></i> Proper input spacing</li>
                            <li><i class="fas fa-check text-success"></i> Responsive layout</li>
                            <li><i class="fas fa-check text-success"></i> Mobile keyboards</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Instructions -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> How to Test Mobile Responsiveness</h5>
                    <ol>
                        <li><strong>Browser DevTools:</strong> Press F12 and click the mobile device icon</li>
                        <li><strong>Resize Window:</strong> Drag your browser window to different sizes</li>
                        <li><strong>Real Device:</strong> Access the system from your phone or tablet</li>
                        <li><strong>Different Orientations:</strong> Test both portrait and landscape modes</li>
                    </ol>
                    <p class="mb-0"><strong>Breakpoints:</strong> The system adapts at 576px, 768px, 992px, and 1200px screen widths.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
