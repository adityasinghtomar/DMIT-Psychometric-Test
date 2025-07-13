<?php
/**
 * Edit Assessment - DMIT Psychometric Test System
 * Edit assessment subject information
 */

require_once '../config/config.php';

Security::requireAuth();

$subjectId = $_GET['id'] ?? 0;
$errors = [];
$success = false;

// Verify subject exists and belongs to current user
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("
        SELECT * FROM assessment_subjects 
        WHERE id = ? AND (user_id = ? OR ? = 'admin')
    ");
    $stmt->execute([$subjectId, $_SESSION['user_id'], $_SESSION['user_role']]);
    $subject = $stmt->fetch();
    
    if (!$subject) {
        redirect('list.php', 'Assessment subject not found.', 'error');
    }
    
} catch (Exception $e) {
    redirect('list.php', 'Error loading assessment subject.', 'error');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::verifyCSRFToken($csrfToken)) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        // Validate and sanitize input
        $subjectName = Security::sanitizeInput($_POST['subject_name'] ?? '');
        $dateOfBirth = $_POST['date_of_birth'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $parentName = Security::sanitizeInput($_POST['parent_name'] ?? '');
        $contactEmail = Security::sanitizeInput($_POST['contact_email'] ?? '');
        $contactPhone = Security::sanitizeInput($_POST['contact_phone'] ?? '');
        $schoolName = Security::sanitizeInput($_POST['school_name'] ?? '');
        $gradeClass = Security::sanitizeInput($_POST['grade_class'] ?? '');
        
        // Validation
        if (empty($subjectName)) {
            $errors[] = 'Subject name is required.';
        }
        
        if (empty($dateOfBirth)) {
            $errors[] = 'Date of birth is required.';
        } else {
            $birthDate = new DateTime($dateOfBirth);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;
            
            if ($age < 3 || $age > 25) {
                $errors[] = 'Age must be between 3 and 25 years for DMIT assessment.';
            }
        }
        
        if (empty($gender)) {
            $errors[] = 'Gender is required.';
        }
        
        if (!empty($contactEmail) && !Security::validateEmail($contactEmail)) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        if (empty($errors)) {
            try {
                // Calculate age at assessment
                $birthDate = new DateTime($dateOfBirth);
                $today = new DateTime();
                $ageAtAssessment = $today->diff($birthDate)->y;
                
                $stmt = $conn->prepare("
                    UPDATE assessment_subjects 
                    SET subject_name = ?, date_of_birth = ?, gender = ?, age_at_assessment = ?,
                        parent_name = ?, contact_email = ?, contact_phone = ?, 
                        school_name = ?, grade_class = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $subjectName, $dateOfBirth, $gender, $ageAtAssessment,
                    $parentName ?: null, $contactEmail ?: null, $contactPhone ?: null,
                    $schoolName ?: null, $gradeClass ?: null, $subjectId
                ]);
                
                // Log audit
                logAudit('assessment_subject_updated', 'assessment_subjects', $subjectId);
                
                redirect("view.php?id=$subjectId", 
                        'Assessment information updated successfully.', 
                        'success');
                
            } catch (Exception $e) {
                $errors[] = 'Failed to update assessment. Please try again.';
                error_log("Assessment update error: " . $e->getMessage());
            }
        }
    }
}

$pageTitle = 'Edit Assessment - ' . APP_NAME;
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="new.php">
                            <i class="fas fa-plus-circle"></i> New Assessment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="list.php">
                            <i class="fas fa-list"></i> View Assessments
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Assessment</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="view.php?id=<?php echo $subjectId; ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to View
                    </a>
                </div>
            </div>

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

            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-edit"></i> Edit Subject Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="editAssessmentForm">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">Basic Information</h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subject_name" class="form-label">Subject Name *</label>
                                    <input type="text" class="form-control" id="subject_name" name="subject_name" 
                                           value="<?php echo htmlspecialchars($subject['subject_name']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                           value="<?php echo htmlspecialchars($subject['date_of_birth']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender *</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" <?php echo $subject['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo $subject['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?php echo $subject['gender'] === 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">Contact Information</h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="parent_name" class="form-label">Parent/Guardian Name</label>
                                    <input type="text" class="form-control" id="parent_name" name="parent_name" 
                                           value="<?php echo htmlspecialchars($subject['parent_name'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_phone" class="form-label">Contact Phone</label>
                                    <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                                           value="<?php echo htmlspecialchars($subject['contact_phone'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="contact_email" class="form-label">Contact Email</label>
                                    <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                           value="<?php echo htmlspecialchars($subject['contact_email'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Educational Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">Educational Information</h6>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="school_name" class="form-label">School/Institution Name</label>
                                    <input type="text" class="form-control" id="school_name" name="school_name" 
                                           value="<?php echo htmlspecialchars($subject['school_name'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="grade_class" class="form-label">Grade/Class</label>
                                    <input type="text" class="form-control" id="grade_class" name="grade_class" 
                                           value="<?php echo htmlspecialchars($subject['grade_class'] ?? ''); ?>"
                                           placeholder="e.g., Grade 5, Class 10A">
                                </div>
                            </div>
                        </div>

                        <!-- Current Age Display -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Current Age:</strong> <span id="currentAge"><?php echo $subject['age_at_assessment']; ?></span> years
                                    <small class="d-block mt-1">Age will be recalculated based on the date of birth when you save.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="view.php?id=<?php echo $subjectId; ?>" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Update Assessment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Calculate and display age when date of birth changes
document.getElementById('date_of_birth').addEventListener('change', function() {
    const birthDate = new Date(this.value);
    const today = new Date();
    const age = Math.floor((today - birthDate) / (365.25 * 24 * 60 * 60 * 1000));
    
    document.getElementById('currentAge').textContent = age;
    
    // Validate age range
    if (age < 3 || age > 25) {
        this.setCustomValidity('Age must be between 3 and 25 years for DMIT assessment');
    } else {
        this.setCustomValidity('');
    }
});

// Form submission
document.getElementById('editAssessmentForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Updating...';
});

// Phone number formatting
document.getElementById('contact_phone').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length >= 6) {
        value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
    } else if (value.length >= 3) {
        value = value.replace(/(\d{3})(\d{3})/, '($1) $2');
    }
    this.value = value;
});
</script>

<?php include '../includes/footer.php'; ?>
