    <!-- Footer -->
    <footer class="bg-light text-center text-lg-start mt-5">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase"><?php echo APP_NAME; ?></h5>
                    <p>
                        Professional DMIT-based psychometric assessment system for career guidance and personality analysis.
                        Secure, reliable, and scientifically backed assessments.
                    </p>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Quick Links</h5>
                    <ul class="list-unstyled mb-0">
                        <?php if (Security::isAuthenticated()): ?>
                        <li><a href="assessments/new.php" class="text-dark">New Assessment</a></li>
                        <li><a href="assessments/list.php" class="text-dark">View Assessments</a></li>
                        <li><a href="reports/list.php" class="text-dark">Reports</a></li>
                        <li><a href="profile/settings.php" class="text-dark">Profile Settings</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Support</h5>
                    <ul class="list-unstyled mb-0">
                        <li><a href="help/user_guide.php" class="text-dark">User Guide</a></li>
                        <li><a href="help/faq.php" class="text-dark">FAQ</a></li>
                        <li><a href="help/contact.php" class="text-dark">Contact Support</a></li>
                        <li><a href="help/privacy.php" class="text-dark">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            <small>
                © <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved. 
                Version <?php echo APP_VERSION; ?> | 
                <i class="fas fa-shield-alt text-success"></i> Secure Platform |
                <i class="fas fa-lock text-primary"></i> Data Protected
            </small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Form validation
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form) return false;
            
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                }
            });
            
            return isValid;
        }
        
        // File upload preview
        function previewFile(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);
            
            if (file && preview) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }
        
        // AJAX form submission
        function submitFormAjax(formId, successCallback, errorCallback) {
            const form = document.getElementById(formId);
            if (!form) return;
            
            const formData = new FormData(form);
            formData.append('csrf_token', window.csrfToken);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-Token': window.csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (successCallback) successCallback(data);
                } else {
                    if (errorCallback) errorCallback(data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (errorCallback) errorCallback({error: 'Network error occurred'});
            });
        }
        
        // Show loading spinner
        function showLoading(buttonId) {
            const button = document.getElementById(buttonId);
            if (button) {
                button.disabled = true;
                button.innerHTML = '<span class="loading-spinner"></span> Processing...';
            }
        }
        
        // Hide loading spinner
        function hideLoading(buttonId, originalText) {
            const button = document.getElementById(buttonId);
            if (button) {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }
        
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
        
        // Confirm delete actions
        function confirmDelete(message = 'Are you sure you want to delete this item?') {
            return confirm(message);
        }
        
        // Format numbers
        function formatNumber(num, decimals = 2) {
            return parseFloat(num).toFixed(decimals);
        }
        
        // Validate email format
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        // Validate phone number (Indian format)
        function validatePhone(phone) {
            const re = /^[6-9]\d{9}$/;
            return re.test(phone);
        }
        
        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            const checks = [
                /.{8,}/, // At least 8 characters
                /[a-z]/, // Lowercase letter
                /[A-Z]/, // Uppercase letter
                /[0-9]/, // Number
                /[^A-Za-z0-9]/ // Special character
            ];
            
            checks.forEach(check => {
                if (check.test(password)) strength++;
            });
            
            const levels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            return {
                score: strength,
                level: levels[strength] || 'Very Weak'
            };
        }
        
        // Chart color schemes
        const chartColors = {
            primary: '#007bff',
            success: '#28a745',
            warning: '#ffc107',
            danger: '#dc3545',
            info: '#17a2b8',
            light: '#f8f9fa',
            dark: '#343a40',
            intelligence: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
            ]
        };
        
        // Print report function
        function printReport() {
            window.print();
        }
        
        // Download report as PDF
        function downloadPDF(reportId) {
            window.location.href = `reports/download.php?id=${reportId}`;
        }
        
        // Security: Disable right-click context menu on sensitive pages
        <?php if (isset($disableRightClick) && $disableRightClick): ?>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        
        // Disable F12, Ctrl+Shift+I, Ctrl+U
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.key === 'u')) {
                e.preventDefault();
            }
        });
        <?php endif; ?>
    </script>
    
    <!-- Page-specific JavaScript -->
    <?php if (isset($pageScript)): ?>
        <script src="<?php echo $pageScript; ?>"></script>
    <?php endif; ?>
    
    <?php if (isset($inlineScript)): ?>
        <script><?php echo $inlineScript; ?></script>
    <?php endif; ?>
</body>
</html>
