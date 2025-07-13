<?php
/**
 * New Assessment - DMIT Psychometric Test System
 * Create new psychometric assessment with biometric data collection
 */

require_once '../config/config.php';

Security::requireAuth();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::verifyCSRFToken($csrfToken)) {
        $errors[] = 'Invalid security token. Please try again.';
    }
    
    // Collect and sanitize form data
    $subjectName = Security::sanitizeInput($_POST['subject_name'] ?? '');
    $dateOfBirth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $parentName = Security::sanitizeInput($_POST['parent_name'] ?? '');
    $contactEmail = Security::sanitizeInput($_POST['contact_email'] ?? '');
    $contactPhone = Security::sanitizeInput($_POST['contact_phone'] ?? '');
    $schoolName = Security::sanitizeInput($_POST['school_name'] ?? '');
    $gradeClass = Security::sanitizeInput($_POST['grade_class'] ?? '');
    
    // Validate required fields
    $requiredErrors = validateRequired($_POST, ['subject_name', 'date_of_birth', 'gender']);
    $errors = array_merge($errors, $requiredErrors);
    
    // Validate date of birth
    if (!validateDate($dateOfBirth)) {
        $errors[] = 'Please enter a valid date of birth.';
    } else {
        $age = calculateAge($dateOfBirth);
        if ($age < 3 || $age > 25) {
            $errors[] = 'Age must be between 3 and 25 years for DMIT assessment.';
        }
    }
    
    // Validate email if provided
    if (!empty($contactEmail) && !Security::validateEmail($contactEmail)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    // Validate phone if provided
    if (!empty($contactPhone) && !Security::validatePhone($contactPhone)) {
        $errors[] = 'Please enter a valid 10-digit phone number.';
    }
    
    if (empty($errors)) {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            $userId = $_SESSION['user_id'];
            $ageAtAssessment = calculateAge($dateOfBirth);
            
            // Insert assessment subject
            $stmt = $conn->prepare("
                INSERT INTO assessment_subjects 
                (user_id, subject_name, date_of_birth, gender, age_at_assessment, 
                 parent_name, contact_email, contact_phone, school_name, grade_class) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId, $subjectName, $dateOfBirth, $gender, $ageAtAssessment,
                $parentName ?: null, $contactEmail ?: null, $contactPhone ?: null,
                $schoolName ?: null, $gradeClass ?: null
            ]);
            
            $subjectId = $conn->lastInsertId();
            
            // Log audit
            logAudit('assessment_created', 'assessment_subjects', $subjectId);
            
            $success = true;
            redirect("fingerprint_collection.php?id=$subjectId",
                    'Assessment subject created successfully. Please proceed with fingerprint collection.',
                    'success');
            
        } catch (Exception $e) {
            $errors[] = 'Failed to create assessment. Please try again.';
            error_log("Assessment creation error: " . $e->getMessage());
        }
    }
}

$pageTitle = 'New Assessment - ' . APP_NAME;
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('index.php'); ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo url('assessments/new.php'); ?>">
                            <i class="fas fa-plus-circle"></i> New Assessment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('assessments/list.php'); ?>">
                            <i class="fas fa-list"></i> View Assessments
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">New Assessment</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?php echo url('assessments/list.php'); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-list"></i> View All Assessments
                    </a>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> Please correct the following errors:</h5>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-user-plus"></i> Assessment Subject Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="assessmentForm">
                                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <label for="subject_name" class="form-label">Subject Name *</label>
                                        <input type="text" class="form-control" id="subject_name" name="subject_name" 
                                               value="<?php echo htmlspecialchars($_POST['subject_name'] ?? ''); ?>" 
                                               required maxlength="100">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="gender" class="form-label">Gender *</label>
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="male" <?php echo ($_POST['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                                            <option value="female" <?php echo ($_POST['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                                            <option value="other" <?php echo ($_POST['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                               value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>" 
                                               required max="<?php echo date('Y-m-d'); ?>">
                                        <div class="form-text">Age will be calculated automatically</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="calculated_age" class="form-label">Age</label>
                                        <input type="text" class="form-control" id="calculated_age" readonly 
                                               placeholder="Will be calculated from date of birth">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="parent_name" class="form-label">Parent/Guardian Name</label>
                                        <input type="text" class="form-control" id="parent_name" name="parent_name" 
                                               value="<?php echo htmlspecialchars($_POST['parent_name'] ?? ''); ?>" 
                                               maxlength="100">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="contact_email" class="form-label">Contact Email</label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                               value="<?php echo htmlspecialchars($_POST['contact_email'] ?? ''); ?>" 
                                               maxlength="100">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="contact_phone" class="form-label">Contact Phone</label>
                                        <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                                               value="<?php echo htmlspecialchars($_POST['contact_phone'] ?? ''); ?>" 
                                               pattern="[6-9][0-9]{9}" maxlength="15">
                                        <div class="form-text">10-digit mobile number</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="grade_class" class="form-label">Grade/Class</label>
                                        <input type="text" class="form-control" id="grade_class" name="grade_class" 
                                               value="<?php echo htmlspecialchars($_POST['grade_class'] ?? ''); ?>" 
                                               maxlength="20" placeholder="e.g., 10th Grade, Class 5">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="school_name" class="form-label">School/Institution Name</label>
                                    <input type="text" class="form-control" id="school_name" name="school_name" 
                                           value="<?php echo htmlspecialchars($_POST['school_name'] ?? ''); ?>" 
                                           maxlength="100">
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="../index.php" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-arrow-right"></i> Create & Continue to Fingerprints
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-12 mt-3 mt-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-info-circle"></i> Assessment Process</h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex align-items-center">
                                    <span class="badge bg-primary rounded-pill me-2">1</span>
                                    <strong>Subject Information</strong>
                                </div>
                                <div class="list-group-item d-flex align-items-center text-muted">
                                    <span class="badge bg-secondary rounded-pill me-2">2</span>
                                    Fingerprint Collection
                                </div>
                                <div class="list-group-item d-flex align-items-center text-muted">
                                    <span class="badge bg-secondary rounded-pill me-2">3</span>
                                    Analysis & Processing
                                </div>
                                <div class="list-group-item d-flex align-items-center text-muted">
                                    <span class="badge bg-secondary rounded-pill me-2">4</span>
                                    Report Generation
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h6><i class="fas fa-clock"></i> Estimated Time</h6>
                            <p class="small text-muted">
                                Complete assessment process typically takes 15-20 minutes including 
                                fingerprint collection and data entry.
                            </p>
                            
                            <h6><i class="fas fa-shield-alt"></i> Data Security</h6>
                            <p class="small text-muted">
                                All biometric data is encrypted and stored securely. Personal information 
                                is protected according to privacy regulations.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Calculate age when date of birth changes
document.getElementById('date_of_birth').addEventListener('change', function() {
    const dob = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    
    document.getElementById('calculated_age').value = age + ' years';
    
    // Validate age range
    if (age < 3 || age > 25) {
        this.setCustomValidity('Age must be between 3 and 25 years for DMIT assessment');
    } else {
        this.setCustomValidity('');
    }
});

// Form submission
document.getElementById('assessmentForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Creating...';
});

// Phone number validation
document.getElementById('contact_phone').addEventListener('input', function() {
    const phone = this.value;
    if (phone && !/^[6-9][0-9]{9}$/.test(phone)) {
        this.setCustomValidity('Please enter a valid 10-digit mobile number starting with 6-9');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
