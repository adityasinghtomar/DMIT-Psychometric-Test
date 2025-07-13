<?php
/**
 * Privacy Policy - DMIT Psychometric Test System
 * Privacy policy and data protection information
 */

$pageTitle = 'Privacy Policy - ' . (defined('APP_NAME') ? APP_NAME : 'DMIT Psychometric Test System');
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
        .header-section { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 3rem 0; }
        .section-title { color: #28a745; border-bottom: 2px solid #28a745; padding-bottom: 0.5rem; margin-bottom: 1.5rem; }
        .last-updated { background: #f8f9fa; padding: 1rem; border-left: 4px solid #28a745; margin-bottom: 2rem; }
        .data-type { background: #e8f5e8; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1><i class="fas fa-user-shield"></i> Privacy Policy</h1>
                    <p class="lead">How we collect, use, and protect your personal information</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Last Updated -->
        <div class="last-updated">
            <strong><i class="fas fa-calendar-alt"></i> Last Updated:</strong> January 15, 2024
        </div>

        <!-- Introduction -->
        <section class="mb-5">
            <h2 class="section-title">1. Introduction</h2>
            <p>This Privacy Policy describes how the DMIT Psychometric Test System ("we," "our," or "us") collects, uses, and protects your personal information when you use our service.</p>
            <p>We are committed to protecting your privacy and ensuring the security of your personal data, including sensitive biometric information such as fingerprint patterns.</p>
        </section>

        <!-- Information We Collect -->
        <section class="mb-5">
            <h2 class="section-title">2. Information We Collect</h2>
            
            <div class="data-type">
                <h5><i class="fas fa-user"></i> Personal Information</h5>
                <ul>
                    <li>Name, email address, phone number</li>
                    <li>Date of birth and age</li>
                    <li>Gender</li>
                    <li>Educational information (school, grade/class)</li>
                    <li>Parent/guardian contact information (for minors)</li>
                </ul>
            </div>

            <div class="data-type">
                <h5><i class="fas fa-fingerprint"></i> Biometric Data</h5>
                <ul>
                    <li>Fingerprint patterns (arch, loop, whorl)</li>
                    <li>Ridge count measurements</li>
                    <li>Dermatoglyphic analysis data</li>
                    <li>Digital fingerprint images (if applicable)</li>
                </ul>
            </div>

            <div class="data-type">
                <h5><i class="fas fa-brain"></i> Assessment Data</h5>
                <ul>
                    <li>Intelligence assessment results</li>
                    <li>Personality profile data</li>
                    <li>Learning style preferences</li>
                    <li>Brain dominance analysis</li>
                    <li>Generated reports and recommendations</li>
                </ul>
            </div>

            <div class="data-type">
                <h5><i class="fas fa-server"></i> Technical Information</h5>
                <ul>
                    <li>IP address and device information</li>
                    <li>Browser type and version</li>
                    <li>Login timestamps and session data</li>
                    <li>System usage logs</li>
                </ul>
            </div>
        </section>

        <!-- How We Use Information -->
        <section class="mb-5">
            <h2 class="section-title">3. How We Use Your Information</h2>
            <p>We use your information for the following purposes:</p>
            <ul>
                <li><strong>Assessment Services:</strong> To conduct DMIT analysis and generate personalized reports</li>
                <li><strong>Account Management:</strong> To create and maintain your user account</li>
                <li><strong>Communication:</strong> To send you assessment results and important updates</li>
                <li><strong>Security:</strong> To protect against fraud and unauthorized access</li>
                <li><strong>Improvement:</strong> To enhance our services and user experience</li>
                <li><strong>Legal Compliance:</strong> To comply with applicable laws and regulations</li>
            </ul>
        </section>

        <!-- Data Sharing -->
        <section class="mb-5">
            <h2 class="section-title">4. Data Sharing and Disclosure</h2>
            <p><strong>We do not sell your personal information.</strong> We may share your information only in the following circumstances:</p>
            
            <h5>4.1 With Your Consent</h5>
            <p>We may share your information when you explicitly consent to such sharing.</p>
            
            <h5>4.2 Service Providers</h5>
            <p>We may share data with trusted third-party service providers who assist us in operating our service, subject to strict confidentiality agreements.</p>
            
            <h5>4.3 Legal Requirements</h5>
            <p>We may disclose information if required by law, court order, or government request.</p>
            
            <h5>4.4 Safety and Security</h5>
            <p>We may share information to protect the safety and security of our users or the public.</p>
        </section>

        <!-- Data Security -->
        <section class="mb-5">
            <h2 class="section-title">5. Data Security</h2>
            <p>We implement comprehensive security measures to protect your data:</p>
            <ul>
                <li><strong>Encryption:</strong> Data is encrypted in transit and at rest</li>
                <li><strong>Access Controls:</strong> Strict access controls and authentication</li>
                <li><strong>Regular Audits:</strong> Security audits and vulnerability assessments</li>
                <li><strong>Staff Training:</strong> Regular security training for all personnel</li>
                <li><strong>Secure Infrastructure:</strong> Industry-standard security protocols</li>
                <li><strong>Data Backup:</strong> Regular secure backups with encryption</li>
            </ul>
        </section>

        <!-- Data Retention -->
        <section class="mb-5">
            <h2 class="section-title">6. Data Retention</h2>
            <p>We retain your information for different periods based on the type of data:</p>
            <ul>
                <li><strong>Account Information:</strong> Until account deletion or as required by law</li>
                <li><strong>Assessment Data:</strong> Up to 7 years for research and validation purposes</li>
                <li><strong>Biometric Data:</strong> Processed and stored securely, deleted upon request where legally permissible</li>
                <li><strong>Technical Logs:</strong> Up to 2 years for security and system maintenance</li>
            </ul>
        </section>

        <!-- Your Rights -->
        <section class="mb-5">
            <h2 class="section-title">7. Your Rights</h2>
            <p>You have the following rights regarding your personal data:</p>
            <ul>
                <li><strong>Access:</strong> Request access to your personal information</li>
                <li><strong>Correction:</strong> Request correction of inaccurate data</li>
                <li><strong>Deletion:</strong> Request deletion of your data (subject to legal requirements)</li>
                <li><strong>Portability:</strong> Request a copy of your data in a portable format</li>
                <li><strong>Restriction:</strong> Request restriction of processing in certain circumstances</li>
                <li><strong>Objection:</strong> Object to processing based on legitimate interests</li>
                <li><strong>Withdrawal:</strong> Withdraw consent where processing is based on consent</li>
            </ul>
        </section>

        <!-- Children's Privacy -->
        <section class="mb-5">
            <h2 class="section-title">8. Children's Privacy</h2>
            <p>We take special care to protect children's privacy:</p>
            <ul>
                <li>For users under 18, we require parental consent</li>
                <li>We collect only necessary information for assessment purposes</li>
                <li>Parents can review, modify, or delete their child's information</li>
                <li>We do not use children's data for marketing purposes</li>
                <li>Additional safeguards are in place for processing children's data</li>
            </ul>
        </section>

        <!-- International Transfers -->
        <section class="mb-5">
            <h2 class="section-title">9. International Data Transfers</h2>
            <p>If we transfer your data internationally, we ensure appropriate safeguards are in place:</p>
            <ul>
                <li>Adequacy decisions by relevant authorities</li>
                <li>Standard contractual clauses</li>
                <li>Binding corporate rules</li>
                <li>Certification schemes</li>
            </ul>
        </section>

        <!-- Cookies and Tracking -->
        <section class="mb-5">
            <h2 class="section-title">10. Cookies and Tracking</h2>
            <p>We use cookies and similar technologies to:</p>
            <ul>
                <li>Maintain your login session</li>
                <li>Remember your preferences</li>
                <li>Analyze usage patterns</li>
                <li>Improve system performance</li>
            </ul>
            <p>You can control cookie settings through your browser preferences.</p>
        </section>

        <!-- Changes to Privacy Policy -->
        <section class="mb-5">
            <h2 class="section-title">11. Changes to This Privacy Policy</h2>
            <p>We may update this Privacy Policy from time to time. We will notify you of any material changes by:</p>
            <ul>
                <li>Posting the updated policy on our website</li>
                <li>Sending email notifications for significant changes</li>
                <li>Updating the "Last Updated" date</li>
            </ul>
        </section>

        <!-- Contact Information -->
        <section class="mb-5">
            <h2 class="section-title">12. Contact Us</h2>
            <p>If you have questions about this Privacy Policy or want to exercise your rights, contact us:</p>
            <div class="card">
                <div class="card-body">
                    <p class="mb-1"><strong>Privacy Officer:</strong> privacy@dmitpsychometric.com</p>
                    <p class="mb-1"><strong>Phone:</strong> +1 (555) 123-4567</p>
                    <p class="mb-1"><strong>Address:</strong> [Your Business Address]</p>
                    <p class="mb-0"><strong>Data Protection Officer:</strong> dpo@dmitpsychometric.com</p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <div class="text-center mt-5 pt-4 border-top">
            <p class="text-muted">
                <i class="fas fa-shield-alt"></i> 
                Your privacy is our priority. We are committed to protecting your personal information.
            </p>
            <div class="mt-3">
                <a href="terms.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-file-contract"></i> Terms of Service
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
