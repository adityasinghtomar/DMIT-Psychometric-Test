<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DMIT Psychometric Test System - Professional career assessment and guidance">
    <meta name="author" content="DMIT Psychometric System">
    <title><?php echo isset($pageTitle) ? $pageTitle : APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Chart.js for reports -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <style>
        /* Navbar is now regular (not sticky) */

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        
        .sidebar .nav-link {
            font-weight: 500;
            color: #333;
            padding: 10px 15px;
        }
        
        .sidebar .nav-link:hover {
            color: #007bff;
            background-color: #f8f9fa;
        }
        
        .sidebar .nav-link.active {
            color: #007bff;
            background-color: #e3f2fd;
        }

        /* Main content area adjustments */
        .main-content {
            margin-top: 0;
            min-height: calc(100vh - 156px);
            padding-bottom: 100px;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            body {
                padding-top: 56px;
            }
            .sidebar {
                position: relative;
                top: 0;
                height: auto;
                padding: 10px 0;
            }
            .main-content {
                margin-left: 0;
                min-height: calc(100vh - 100px);
            }
        }
        
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
            font-size: 1rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
        }
        
        .navbar .form-control {
            padding: .75rem 1rem;
            border-width: 0;
            border-radius: 0;
        }
        
        .form-control-dark {
            color: #fff;
            background-color: rgba(255, 255, 255, .1);
            border-color: rgba(255, 255, 255, .1);
        }
        
        .form-control-dark:focus {
            border-color: transparent;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, .25);
        }
        
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        
        .alert {
            border-radius: 0.375rem;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
        }
        
        .fingerprint-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        
        .fingerprint-item {
            text-align: center;
            padding: 10px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .fingerprint-item.uploaded {
            border-color: #28a745;
            background-color: #f8fff9;
        }
        
        .fingerprint-preview {
            max-width: 80px;
            max-height: 80px;
            border-radius: 4px;
        }
        
        .intelligence-chart {
            max-height: 400px;
        }
        
        .personality-profile {
            text-align: center;
            padding: 20px;
        }
        
        .personality-icon {
            font-size: 4rem;
            margin-bottom: 15px;
        }
        
        .brain-hemisphere {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }
        
        .brain-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 50%;
            height: 100%;
            background-color: #007bff;
        }
        
        .brain-right {
            position: absolute;
            right: 0;
            top: 0;
            width: 50%;
            height: 100%;
            background-color: #28a745;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .security-badge {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            z-index: 1000;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 767.98px) {
            .sidebar {
                position: relative;
                height: auto;
                padding: 0;
            }

            .fingerprint-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            /* Mobile-specific improvements */
            .container-fluid {
                padding-left: 10px;
                padding-right: 10px;
            }

            .card {
                margin-bottom: 1rem;
            }

            .btn-toolbar {
                flex-direction: column;
                gap: 0.5rem;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }

            .btn-group {
                width: 100%;
            }

            .btn-group .btn {
                flex: 1;
            }

            /* Hide less important columns on mobile */
            .table th:nth-child(n+4),
            .table td:nth-child(n+4) {
                display: none;
            }

            /* Stack form elements */
            .row .col-md-6,
            .row .col-md-4,
            .row .col-md-3 {
                margin-bottom: 1rem;
            }

            /* Improve quick action buttons */
            .col-md-3.mb-3 {
                margin-bottom: 1rem !important;
            }

            .col-md-3.mb-3 a {
                min-height: 100px;
                padding: 1rem;
            }

            /* Better spacing for mobile */
            .px-md-4 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            /* Improve modal on mobile */
            .modal-dialog {
                margin: 0.5rem;
            }

            /* Better form spacing */
            .form-label {
                font-weight: 600;
                margin-bottom: 0.25rem;
            }

            /* Improve fingerprint collection on mobile */
            .fingerprint-item {
                min-height: 100px;
                padding: 8px;
            }

            /* Better alert spacing */
            .alert {
                margin-bottom: 1rem;
                padding: 0.75rem;
            }
        }

        @media (max-width: 575.98px) {
            /* Extra small devices */
            .col-md-3 {
                margin-bottom: 1rem;
            }

            .card-header h5,
            .card-header h6 {
                font-size: 1rem;
            }

            .btn-sm {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }

            /* Stack statistics cards */
            .col-md-3.mb-3 {
                margin-bottom: 1rem !important;
            }

            /* Hide even more table columns on very small screens */
            .table th:nth-child(n+3),
            .table td:nth-child(n+3) {
                display: none;
            }

            /* Improve navigation */
            .navbar-brand {
                font-size: 1rem;
            }

            /* Better form layout */
            .row .col-md-8,
            .row .col-md-6,
            .row .col-md-4 {
                margin-bottom: 0.75rem;
            }
        }
    </style>
    
    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="<?php echo Security::generateCSRFToken(); ?>">
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-dark bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="<?php echo url('index.php'); ?>">
            <i class="fas fa-brain"></i> <?php echo APP_NAME; ?>
        </a>
        
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <?php if (Security::isAuthenticated()): ?>
                    <span class="navbar-text me-3">
                        Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                        <span class="badge bg-secondary"><?php echo ucfirst($_SESSION['user_role'] ?? 'user'); ?></span>
                    </span>
                    <a class="nav-link px-3" href="<?php echo url('auth/logout.php'); ?>">
                        <i class="fas fa-sign-out-alt"></i> Sign out
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Security Badge -->
    <div class="security-badge">
        <i class="fas fa-shield-alt"></i> Secure
    </div>
    
    <!-- JavaScript for security -->
    <script>
        // CSRF token for AJAX requests
        window.csrfToken = '<?php echo Security::generateCSRFToken(); ?>';
        
        // Session timeout warning
        let sessionTimeout = <?php echo SESSION_LIFETIME; ?> * 1000; // Convert to milliseconds
        let warningTime = sessionTimeout - (5 * 60 * 1000); // 5 minutes before expiry
        
        setTimeout(function() {
            if (confirm('Your session will expire in 5 minutes. Click OK to extend your session.')) {
                // Make AJAX request to extend session
                fetch('auth/extend_session.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': window.csrfToken
                    }
                });
            }
        }, warningTime);
        
        // Auto logout on session expiry
        setTimeout(function() {
            alert('Your session has expired. You will be redirected to the login page.');
            window.location.href = '<?php echo url('auth/logout.php'); ?>';
        }, sessionTimeout);
    </script>
