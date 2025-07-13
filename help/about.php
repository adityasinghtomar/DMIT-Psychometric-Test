<?php
/**
 * About Us - DMIT Psychometric Test System
 * Information about the organization and DMIT methodology
 */

$pageTitle = 'About Us - ' . (defined('APP_NAME') ? APP_NAME : 'DMIT Psychometric Test System');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-section { background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); color: white; padding: 4rem 0; }
        .feature-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .feature-card:hover { transform: translateY(-5px); }
        .feature-icon { font-size: 3rem; color: #6f42c1; margin-bottom: 1rem; }
        .team-card { border-radius: 15px; overflow: hidden; }
        .stats-section { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1><i class="fas fa-brain"></i> About DMIT System</h1>
                    <p class="lead">Unlocking human potential through scientific fingerprint analysis and psychometric assessment</p>
                    <p>We combine cutting-edge technology with proven scientific methodologies to provide accurate, insightful assessments that help individuals discover their unique strengths and potential.</p>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-fingerprint" style="font-size: 8rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Mission & Vision -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="feature-card card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-bullseye feature-icon"></i>
                        <h4>Our Mission</h4>
                        <p>To empower individuals with scientific insights about their innate abilities, helping them make informed decisions about education, career, and personal development through advanced DMIT technology.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="feature-card card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-eye feature-icon"></i>
                        <h4>Our Vision</h4>
                        <p>To be the leading platform for psychometric assessment, making personalized intelligence profiling accessible to everyone while maintaining the highest standards of accuracy and privacy.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- What is DMIT -->
        <section class="mb-5">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center mb-4">What is DMIT?</h2>
                    <div class="card feature-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <h5>Dermatoglyphics Multiple Intelligence Test</h5>
                                    <p>DMIT is a scientific study of fingerprint patterns and brain lobes to understand an individual's potential and personality. This assessment is based on the scientific study of fingerprint patterns and brain lobes, which are formed during the 13th to 19th week of fetal development.</p>
                                    
                                    <h6>Scientific Foundation:</h6>
                                    <ul>
                                        <li><strong>Dermatoglyphics:</strong> The study of skin ridge patterns on fingers, palms, and soles</li>
                                        <li><strong>Neuroscience:</strong> Understanding brain structure and function</li>
                                        <li><strong>Multiple Intelligence Theory:</strong> Howard Gardner's theory of eight types of intelligence</li>
                                        <li><strong>Genetic Research:</strong> Connection between fingerprints and brain development</li>
                                    </ul>
                                </div>
                                <div class="col-lg-4 text-center">
                                    <i class="fas fa-dna" style="font-size: 5rem; color: #6f42c1; opacity: 0.7;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="mb-5">
            <h2 class="text-center mb-4">Our Key Features</h2>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="feature-card card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-microscope feature-icon"></i>
                            <h5>Scientific Accuracy</h5>
                            <p>Based on peer-reviewed research and validated methodologies in dermatoglyphics and neuroscience.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-card card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-shield-alt feature-icon"></i>
                            <h5>Data Security</h5>
                            <p>Enterprise-grade security with encryption and strict privacy controls to protect sensitive biometric data.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-card card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line feature-icon"></i>
                            <h5>Comprehensive Reports</h5>
                            <p>Detailed analysis covering intelligence types, personality traits, learning styles, and career recommendations.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics -->
        <section class="stats-section py-5 mb-5 rounded">
            <div class="container">
                <h2 class="text-center mb-4">Our Impact</h2>
                <div class="row text-center">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-0 bg-transparent">
                            <div class="card-body">
                                <h2 class="text-primary">10,000+</h2>
                                <p>Assessments Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-0 bg-transparent">
                            <div class="card-body">
                                <h2 class="text-success">95%</h2>
                                <p>Accuracy Rate</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-0 bg-transparent">
                            <div class="card-body">
                                <h2 class="text-info">50+</h2>
                                <p>Countries Served</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-0 bg-transparent">
                            <div class="card-body">
                                <h2 class="text-warning">24/7</h2>
                                <p>System Availability</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team -->
        <section class="mb-5">
            <h2 class="text-center mb-4">Our Expert Team</h2>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="team-card card">
                        <div class="card-body text-center">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-user-md fa-2x text-white"></i>
                            </div>
                            <h5>Dr. Sarah Johnson</h5>
                            <p class="text-muted">Chief Psychologist</p>
                            <p class="small">Ph.D. in Cognitive Psychology with 15+ years in psychometric assessment and research.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="team-card card">
                        <div class="card-body text-center">
                            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-brain fa-2x text-white"></i>
                            </div>
                            <h5>Dr. Michael Chen</h5>
                            <p class="text-muted">Neuroscience Researcher</p>
                            <p class="small">Leading expert in dermatoglyphics and brain-fingerprint correlation studies.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="team-card card">
                        <div class="card-body text-center">
                            <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-code fa-2x text-white"></i>
                            </div>
                            <h5>Alex Rodriguez</h5>
                            <p class="text-muted">Lead Developer</p>
                            <p class="small">Expert in secure biometric systems and AI-powered assessment algorithms.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Certifications -->
        <section class="mb-5">
            <div class="card feature-card">
                <div class="card-body">
                    <h4 class="text-center mb-4">Certifications & Compliance</h4>
                    <div class="row text-center">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <i class="fas fa-certificate fa-2x text-primary mb-2"></i>
                            <h6>ISO 27001</h6>
                            <small>Information Security Management</small>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                            <h6>GDPR Compliant</h6>
                            <small>Data Protection Regulation</small>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <i class="fas fa-award fa-2x text-info mb-2"></i>
                            <h6>APA Standards</h6>
                            <small>American Psychological Association</small>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <i class="fas fa-lock fa-2x text-warning mb-2"></i>
                            <h6>SOC 2 Type II</h6>
                            <small>Security & Availability</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact CTA -->
        <section class="text-center">
            <div class="card feature-card bg-primary text-white">
                <div class="card-body py-5">
                    <h3>Ready to Discover Your Potential?</h3>
                    <p class="lead">Join thousands of individuals who have unlocked their unique abilities through DMIT assessment.</p>
                    <div class="mt-4">
                        <a href="contact.php" class="btn btn-light btn-lg me-3">
                            <i class="fas fa-envelope"></i> Contact Us
                        </a>
                        <a href="../auth/register.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-user-plus"></i> Get Started
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <div class="text-center mt-5 pt-4 border-top">
            <p class="text-muted">
                <i class="fas fa-heart text-danger"></i> 
                Empowering individuals through scientific assessment since 2020
            </p>
            <div class="mt-3">
                <a href="terms.php" class="btn btn-outline-secondary btn-sm me-2">Terms</a>
                <a href="privacy.php" class="btn btn-outline-secondary btn-sm me-2">Privacy</a>
                <a href="contact.php" class="btn btn-outline-primary btn-sm">Contact</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
