<?php
/**
 * Fingerprint Collection - DMIT Psychometric Test System
 * Collect and analyze fingerprint data for psychometric assessment
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::verifyCSRFToken($csrfToken)) {
        $errors[] = 'Invalid security token. Please try again.';
    }
    
    $fingerprintData = [];
    $fingers = [
        'left_thumb', 'left_index', 'left_middle', 'left_ring', 'left_little',
        'right_thumb', 'right_index', 'right_middle', 'right_ring', 'right_little'
    ];
    
    foreach ($fingers as $finger) {
        $patternType = $_POST[$finger . '_pattern'] ?? '';
        $ridgeCount = $_POST[$finger . '_ridge'] ?? '';
        
        if (!empty($patternType) && !empty($ridgeCount)) {
            $fingerprintData[$finger] = [
                'pattern_type' => $patternType,
                'ridge_count' => (int)$ridgeCount
            ];
        }
    }
    
    if (count($fingerprintData) < 8) {
        $errors[] = 'Please provide data for at least 8 fingers.';
    }
    
    if (empty($errors)) {
        try {
            $conn->beginTransaction();
            
            // Delete existing fingerprint data
            $stmt = $conn->prepare("DELETE FROM fingerprint_data WHERE subject_id = ?");
            $stmt->execute([$subjectId]);
            
            // Insert new fingerprint data
            foreach ($fingerprintData as $finger => $data) {
                $stmt = $conn->prepare("
                    INSERT INTO fingerprint_data (subject_id, finger_position, pattern_type, ridge_count) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $subjectId, 
                    $finger, 
                    $data['pattern_type'], 
                    $data['ridge_count']
                ]);
            }
            
            $conn->commit();
            
            // Log audit
            logAudit('fingerprint_data_collected', 'fingerprint_data', $subjectId);
            
            redirect("analysis.php?id=$subjectId", 
                    'Fingerprint data collected successfully. Proceeding to analysis.', 
                    'success');
            
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = 'Failed to save fingerprint data. Please try again.';
            error_log("Fingerprint collection error: " . $e->getMessage());
        }
    }
}

// Load existing fingerprint data if any
$existingData = [];
try {
    $stmt = $conn->prepare("SELECT * FROM fingerprint_data WHERE subject_id = ?");
    $stmt->execute([$subjectId]);
    $existing = $stmt->fetchAll();
    
    foreach ($existing as $row) {
        $existingData[$row['finger_position']] = $row;
    }
} catch (Exception $e) {
    error_log("Error loading existing fingerprint data: " . $e->getMessage());
}

$pageTitle = 'Fingerprint Collection - ' . APP_NAME;
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
                <h1 class="h2">Fingerprint Collection</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="list.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Assessments
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

            <!-- Subject Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-user"></i> Subject Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3"><strong>Name:</strong> <?php echo htmlspecialchars($subject['subject_name']); ?></div>
                        <div class="col-md-3"><strong>Age:</strong> <?php echo $subject['age_at_assessment']; ?> years</div>
                        <div class="col-md-3"><strong>Gender:</strong> <?php echo ucfirst($subject['gender']); ?></div>
                        <div class="col-md-3"><strong>Date:</strong> <?php echo formatDate($subject['created_at'], 'M d, Y'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Fingerprint Collection Form -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-fingerprint"></i> Fingerprint Data Collection</h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="fingerprintForm">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        
                        <div class="row">
                            <!-- Left Hand -->
                            <div class="col-md-6">
                                <h6 class="text-center mb-3"><i class="fas fa-hand-paper"></i> Left Hand</h6>
                                
                                <?php 
                                $leftFingers = [
                                    'left_thumb' => 'Thumb',
                                    'left_index' => 'Index',
                                    'left_middle' => 'Middle',
                                    'left_ring' => 'Ring',
                                    'left_little' => 'Little'
                                ];
                                
                                foreach ($leftFingers as $finger => $label): 
                                    $existing = $existingData[$finger] ?? null;
                                ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6><?php echo $label; ?> Finger</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Pattern Type</label>
                                                <select class="form-select" name="<?php echo $finger; ?>_pattern" required>
                                                    <option value="">Select Pattern</option>
                                                    <option value="arch" <?php echo ($existing && $existing['pattern_type'] === 'arch') ? 'selected' : ''; ?>>Arch</option>
                                                    <option value="loop" <?php echo ($existing && $existing['pattern_type'] === 'loop') ? 'selected' : ''; ?>>Loop</option>
                                                    <option value="whorl" <?php echo ($existing && $existing['pattern_type'] === 'whorl') ? 'selected' : ''; ?>>Whorl</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Ridge Count</label>
                                                <input type="number" class="form-control" name="<?php echo $finger; ?>_ridge" 
                                                       min="0" max="50" 
                                                       value="<?php echo $existing ? $existing['ridge_count'] : ''; ?>" 
                                                       required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Right Hand -->
                            <div class="col-md-6">
                                <h6 class="text-center mb-3"><i class="fas fa-hand-paper fa-flip-horizontal"></i> Right Hand</h6>
                                
                                <?php 
                                $rightFingers = [
                                    'right_thumb' => 'Thumb',
                                    'right_index' => 'Index',
                                    'right_middle' => 'Middle',
                                    'right_ring' => 'Ring',
                                    'right_little' => 'Little'
                                ];
                                
                                foreach ($rightFingers as $finger => $label): 
                                    $existing = $existingData[$finger] ?? null;
                                ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6><?php echo $label; ?> Finger</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Pattern Type</label>
                                                <select class="form-select" name="<?php echo $finger; ?>_pattern" required>
                                                    <option value="">Select Pattern</option>
                                                    <option value="arch" <?php echo ($existing && $existing['pattern_type'] === 'arch') ? 'selected' : ''; ?>>Arch</option>
                                                    <option value="loop" <?php echo ($existing && $existing['pattern_type'] === 'loop') ? 'selected' : ''; ?>>Loop</option>
                                                    <option value="whorl" <?php echo ($existing && $existing['pattern_type'] === 'whorl') ? 'selected' : ''; ?>>Whorl</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Ridge Count</label>
                                                <input type="number" class="form-control" name="<?php echo $finger; ?>_ridge" 
                                                       min="0" max="50" 
                                                       value="<?php echo $existing ? $existing['ridge_count'] : ''; ?>" 
                                                       required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="new.php" class="btn btn-secondary me-md-2">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-arrow-right"></i> Proceed to Analysis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Information Panel -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-info-circle"></i> Pattern Types</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><strong>Arch:</strong> Simple curved pattern</li>
                                <li><strong>Loop:</strong> Pattern that curves back on itself</li>
                                <li><strong>Whorl:</strong> Circular or spiral pattern</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-chart-line"></i> Ridge Count Guidelines</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><strong>Low:</strong> 0-15 ridges</li>
                                <li><strong>Medium:</strong> 16-25 ridges</li>
                                <li><strong>High:</strong> 26+ ridges</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Form submission
document.getElementById('fingerprintForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processing...';
});

// Auto-save functionality (optional)
let autoSaveTimer;
document.querySelectorAll('select, input[type="number"]').forEach(element => {
    element.addEventListener('change', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            // Could implement auto-save here
            console.log('Data changed, could auto-save');
        }, 2000);
    });
});
</script>

<?php include '../includes/footer.php'; ?>
