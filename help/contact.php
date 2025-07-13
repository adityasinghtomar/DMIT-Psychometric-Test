<?php
/**
 * Contact Us - DMIT Psychometric Test System
 * Contact information and support form
 */

$pageTitle = 'Contact Us - ' . (defined('APP_NAME') ? APP_NAME : 'DMIT Psychometric Test System');

$success = false;
$errors = [];

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (empty($subject)) $errors[] = 'Subject is required.';
    if (empty($message)) $errors[] = 'Message is required.';
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (empty($errors)) {
        // In a real application, you would send an email or save to database
        // For demo purposes, we'll just show success
        $success = true;
    }
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
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-section { background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%); color: white; padding: 3rem 0; }
        .contact-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .contact-icon { font-size: 2rem; color: #17a2b8; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1><i class="fas fa-envelope"></i> Contact Us</h1>
                    <p class="lead">Get in touch with our support team</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <!-- Contact Information -->
            <div class="col-lg-4 mb-4">
                <div class="contact-card card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-phone contact-icon"></i>
                        <h5>Phone Support</h5>
                        <p>+1 (555) 123-4567</p>
                        <small class="text-muted">Mon-Fri: 9:00 AM - 6:00 PM</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="contact-card card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-envelope contact-icon"></i>
                        <h5>Email Support</h5>
                        <p>support@dmitpsychometric.com</p>
                        <small class="text-muted">Response within 24 hours</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="contact-card card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt contact-icon"></i>
                        <h5>Office Address</h5>
                        <p>123 Psychology Street<br>Research City, RC 12345</p>
                        <small class="text-muted">Visit by appointment</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card contact-card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-paper-plane"></i> Send us a Message</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle"></i> Message Sent!</h5>
                                <p class="mb-0">Thank you for contacting us. We'll get back to you within 24 hours.</p>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject *</label>
                                    <select class="form-select" id="subject" name="subject" required>
                                        <option value="">Select a subject</option>
                                        <option value="technical_support" <?php echo ($_POST['subject'] ?? '') === 'technical_support' ? 'selected' : ''; ?>>Technical Support</option>
                                        <option value="assessment_question" <?php echo ($_POST['subject'] ?? '') === 'assessment_question' ? 'selected' : ''; ?>>Assessment Question</option>
                                        <option value="account_issue" <?php echo ($_POST['subject'] ?? '') === 'account_issue' ? 'selected' : ''; ?>>Account Issue</option>
                                        <option value="billing_inquiry" <?php echo ($_POST['subject'] ?? '') === 'billing_inquiry' ? 'selected' : ''; ?>>Billing Inquiry</option>
                                        <option value="feature_request" <?php echo ($_POST['subject'] ?? '') === 'feature_request' ? 'selected' : ''; ?>>Feature Request</option>
                                        <option value="general_inquiry" <?php echo ($_POST['subject'] ?? '') === 'general_inquiry' ? 'selected' : ''; ?>>General Inquiry</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" 
                                              placeholder="Please describe your question or issue in detail..." required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send Message
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="col-lg-4">
                <div class="card contact-card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-info-circle"></i> Quick Help</h5>
                    </div>
                    <div class="card-body">
                        <h6>Frequently Asked Questions</h6>
                        <ul class="list-unstyled">
                            <li><a href="../help/user_guide.php#faq" class="text-decoration-none">
                                <i class="fas fa-question-circle"></i> How accurate is DMIT?
                            </a></li>
                            <li><a href="../help/user_guide.php#dmit-process" class="text-decoration-none">
                                <i class="fas fa-fingerprint"></i> DMIT Process Guide
                            </a></li>
                            <li><a href="../help/user_guide.php#reports" class="text-decoration-none">
                                <i class="fas fa-file-pdf"></i> Understanding Reports
                            </a></li>
                        </ul>
                        
                        <h6 class="mt-4">Emergency Support</h6>
                        <p class="small">For urgent technical issues affecting assessments in progress:</p>
                        <p class="small"><strong>Emergency Line:</strong> +1 (555) 911-DMIT</p>
                        
                        <h6 class="mt-4">Business Hours</h6>
                        <ul class="list-unstyled small">
                            <li><strong>Monday - Friday:</strong> 9:00 AM - 6:00 PM</li>
                            <li><strong>Saturday:</strong> 10:00 AM - 4:00 PM</li>
                            <li><strong>Sunday:</strong> Closed</li>
                            <li><strong>Holidays:</strong> Limited support</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card contact-card mt-4">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-users"></i> Follow Us</h5>
                    </div>
                    <div class="card-body text-center">
                        <a href="#" class="btn btn-outline-primary btn-sm me-2 mb-2">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <a href="#" class="btn btn-outline-info btn-sm me-2 mb-2">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm me-2 mb-2">
                            <i class="fab fa-linkedin"></i> LinkedIn
                        </a>
                        <a href="#" class="btn btn-outline-danger btn-sm mb-2">
                            <i class="fab fa-youtube"></i> YouTube
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-5 pt-4 border-top">
            <p class="text-muted">
                <i class="fas fa-headset"></i> 
                We're here to help! Don't hesitate to reach out with any questions.
            </p>
            <div class="mt-3">
                <a href="terms.php" class="btn btn-outline-secondary btn-sm me-2">Terms of Service</a>
                <a href="privacy.php" class="btn btn-outline-secondary btn-sm me-2">Privacy Policy</a>
                <a href="javascript:history.back()" class="btn btn-outline-primary btn-sm">Go Back</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
