<?php
/**
 * User Guide - DMIT Psychometric Test System
 * Help and documentation for users
 */

require_once '../config/config.php';

Security::requireAuth();

$pageTitle = 'User Guide - ' . APP_NAME;
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('index.php'); ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('assessments/new.php'); ?>">
                            <i class="fas fa-plus-circle"></i> New Assessment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('assessments/list.php'); ?>">
                            <i class="fas fa-list"></i> View Assessments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('profile/settings.php'); ?>">
                            <i class="fas fa-user-cog"></i> Profile Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo url('help/user_guide.php'); ?>">
                            <i class="fas fa-question-circle"></i> Help & Support
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">User Guide & Help</h1>
            </div>

            <!-- Quick Help -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-play-circle fa-3x text-primary mb-3"></i>
                            <h5>Getting Started</h5>
                            <p>Learn how to create your first assessment and navigate the system.</p>
                            <a href="#getting-started" class="btn btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-fingerprint fa-3x text-success mb-3"></i>
                            <h5>DMIT Process</h5>
                            <p>Understand the fingerprint collection and analysis process.</p>
                            <a href="#dmit-process" class="btn btn-outline-success">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-file-pdf fa-3x text-info mb-3"></i>
                            <h5>Reports</h5>
                            <p>How to generate, view, and understand your assessment reports.</p>
                            <a href="#reports" class="btn btn-outline-info">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Getting Started -->
            <div class="card mb-4" id="getting-started">
                <div class="card-header">
                    <h4><i class="fas fa-play-circle"></i> Getting Started</h4>
                </div>
                <div class="card-body">
                    <h5>1. Creating Your First Assessment</h5>
                    <ol>
                        <li>Click on <strong>"New Assessment"</strong> from the dashboard or sidebar</li>
                        <li>Fill in the subject's basic information (name, age, gender, etc.)</li>
                        <li>Click <strong>"Create & Continue to Fingerprints"</strong></li>
                        <li>Proceed to fingerprint data collection</li>
                    </ol>

                    <h5>2. Navigation</h5>
                    <ul>
                        <li><strong>Dashboard:</strong> Overview of your assessments and quick actions</li>
                        <li><strong>New Assessment:</strong> Create a new psychometric assessment</li>
                        <li><strong>View Assessments:</strong> List all your assessments with status</li>
                        <li><strong>Profile Settings:</strong> Manage your account information</li>
                        <li><strong>Help & Support:</strong> This help section</li>
                    </ul>

                    <h5>3. User Roles</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-user"></i> User</h6>
                                <p>Can create and manage their own assessments</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-user-tie"></i> Counselor</h6>
                                <p>Can create assessments for multiple subjects</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-user-shield"></i> Admin</h6>
                                <p>Full system access and user management</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DMIT Process -->
            <div class="card mb-4" id="dmit-process">
                <div class="card-header">
                    <h4><i class="fas fa-fingerprint"></i> DMIT Assessment Process</h4>
                </div>
                <div class="card-body">
                    <h5>What is DMIT?</h5>
                    <p>
                        Dermatoglyphics Multiple Intelligence Test (DMIT) is a scientific study of fingerprint patterns 
                        and brain lobes to understand an individual's potential and personality.
                    </p>

                    <h5>Assessment Steps</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <strong>1</strong>
                                </div>
                                <h6 class="mt-2">Subject Info</h6>
                                <p class="small">Basic demographic information</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <strong>2</strong>
                                </div>
                                <h6 class="mt-2">Fingerprints</h6>
                                <p class="small">Collect pattern types and ridge counts</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <strong>3</strong>
                                </div>
                                <h6 class="mt-2">Analysis</h6>
                                <p class="small">AI-powered psychometric analysis</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <strong>4</strong>
                                </div>
                                <h6 class="mt-2">Report</h6>
                                <p class="small">Comprehensive PDF report</p>
                            </div>
                        </div>
                    </div>

                    <h5>Fingerprint Pattern Types</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="alert alert-light">
                                <h6>Arch</h6>
                                <p>Simple curved pattern without loops or whorls</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-light">
                                <h6>Loop</h6>
                                <p>Pattern that curves back on itself</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-light">
                                <h6>Whorl</h6>
                                <p>Circular or spiral pattern</p>
                            </div>
                        </div>
                    </div>

                    <h5>Ridge Count Guidelines</h5>
                    <ul>
                        <li><strong>Low:</strong> 0-15 ridges</li>
                        <li><strong>Medium:</strong> 16-25 ridges</li>
                        <li><strong>High:</strong> 26+ ridges</li>
                    </ul>
                </div>
            </div>

            <!-- Reports -->
            <div class="card mb-4" id="reports">
                <div class="card-header">
                    <h4><i class="fas fa-file-pdf"></i> Understanding Reports</h4>
                </div>
                <div class="card-body">
                    <h5>Report Sections</h5>
                    <div class="accordion" id="reportAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#intelligence">
                                    Multiple Intelligence Analysis
                                </button>
                            </h2>
                            <div id="intelligence" class="accordion-collapse collapse show" data-bs-parent="#reportAccordion">
                                <div class="accordion-body">
                                    <p>Based on Howard Gardner's theory, this section analyzes 8 types of intelligence:</p>
                                    <ul>
                                        <li><strong>Linguistic:</strong> Word smart - language and verbal skills</li>
                                        <li><strong>Logical-Mathematical:</strong> Number smart - logical reasoning</li>
                                        <li><strong>Spatial:</strong> Picture smart - visual and spatial awareness</li>
                                        <li><strong>Bodily-Kinesthetic:</strong> Body smart - physical coordination</li>
                                        <li><strong>Musical:</strong> Music smart - musical and rhythmic ability</li>
                                        <li><strong>Interpersonal:</strong> People smart - understanding others</li>
                                        <li><strong>Intrapersonal:</strong> Self smart - self-awareness</li>
                                        <li><strong>Naturalist:</strong> Nature smart - understanding nature</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#personality">
                                    Personality Profile (DISC)
                                </button>
                            </h2>
                            <div id="personality" class="accordion-collapse collapse" data-bs-parent="#reportAccordion">
                                <div class="accordion-body">
                                    <p>DISC personality assessment with animal representations:</p>
                                    <ul>
                                        <li><strong>Eagle (D - Dominance):</strong> Direct, decisive, goal-oriented</li>
                                        <li><strong>Peacock (I - Influence):</strong> Enthusiastic, optimistic, people-oriented</li>
                                        <li><strong>Dove (S - Steadiness):</strong> Patient, loyal, team-oriented</li>
                                        <li><strong>Owl (C - Compliance):</strong> Analytical, precise, quality-focused</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#brain">
                                    Brain Dominance & Learning Styles
                                </button>
                            </h2>
                            <div id="brain" class="accordion-collapse collapse" data-bs-parent="#reportAccordion">
                                <div class="accordion-body">
                                    <p><strong>Brain Dominance:</strong></p>
                                    <ul>
                                        <li><strong>Left Brain:</strong> Logical, analytical, sequential thinking</li>
                                        <li><strong>Right Brain:</strong> Creative, intuitive, holistic thinking</li>
                                    </ul>
                                    <p><strong>Learning Styles (VAK):</strong></p>
                                    <ul>
                                        <li><strong>Visual:</strong> Learning through seeing</li>
                                        <li><strong>Auditory:</strong> Learning through hearing</li>
                                        <li><strong>Kinesthetic:</strong> Learning through doing</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4">Report Actions</h5>
                    <ul>
                        <li><strong>View:</strong> Display the report in your browser</li>
                        <li><strong>Print:</strong> Print the report directly</li>
                        <li><strong>Download:</strong> Save the report as an HTML file</li>
                        <li><strong>Regenerate:</strong> Create a new version of the report</li>
                    </ul>
                </div>
            </div>

            <!-- FAQ -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4><i class="fas fa-question-circle"></i> Frequently Asked Questions</h4>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How accurate is DMIT?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    DMIT is based on scientific research in dermatoglyphics and neuroscience. While it provides valuable insights into potential and tendencies, it should be used as guidance rather than absolute prediction. Individual effort, environment, and choices significantly impact actual achievements.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What age is suitable for DMIT?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    DMIT can be performed from age 3 to 25 years. The optimal age is between 3-16 years when brain development is most active and educational decisions are being made.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    How long does the assessment take?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    The complete process typically takes 15-20 minutes including data entry, fingerprint collection, analysis, and report generation. The actual fingerprint collection takes about 5-10 minutes.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-headset"></i> Need More Help?</h4>
                </div>
                <div class="card-body">
                    <p>If you need additional assistance or have questions not covered in this guide:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-envelope"></i> Email Support</h6>
                                <p>Send us an email at: <strong>support@dmitpsychometric.com</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <h6><i class="fas fa-phone"></i> Phone Support</h6>
                                <p>Call us at: <strong>+1 (555) 123-4567</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
