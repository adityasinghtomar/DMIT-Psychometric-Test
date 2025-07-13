<?php
/**
 * Assessment Analysis - DMIT Psychometric Test System
 * Perform psychometric analysis and display results
 */

require_once '../config/config.php';

Security::requireAuth();

$subjectId = $_GET['id'] ?? 0;
$errors = [];
$analysisResults = null;

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

// Check if analysis should be performed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['perform_analysis'])) {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!Security::verifyCSRFToken($csrfToken)) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        try {
            $assessmentEngine = new AssessmentEngine($database);
            $analysisResults = $assessmentEngine->performAnalysis($subjectId);
            
            if ($analysisResults['success']) {
                // Log audit
                logAudit('assessment_analysis_completed', 'assessment_subjects', $subjectId);
                
                redirect("report.php?id=$subjectId", 
                        'Analysis completed successfully. Report is ready.', 
                        'success');
            } else {
                $errors[] = $analysisResults['error'] ?? 'Analysis failed. Please try again.';
            }
            
        } catch (Exception $e) {
            $errors[] = 'Analysis failed: ' . $e->getMessage();
            error_log("Analysis error: " . $e->getMessage());
        }
    }
}

// Load existing analysis results if available
try {
    $stmt = $conn->prepare("
        SELECT 
            i.*, p.primary_type, p.secondary_type, p.disc_d, p.disc_i, p.disc_s, p.disc_c,
            b.left_brain_percent, b.right_brain_percent, b.dominance_type,
            l.visual_percent, l.auditory_percent, l.kinesthetic_percent, l.primary_style,
            q.iq_score, q.eq_score, q.cq_score, q.aq_score, q.overall_score
        FROM intelligence_scores i
        LEFT JOIN personality_profiles p ON i.subject_id = p.subject_id
        LEFT JOIN brain_dominance b ON i.subject_id = b.subject_id
        LEFT JOIN learning_styles l ON i.subject_id = l.subject_id
        LEFT JOIN quotient_scores q ON i.subject_id = q.subject_id
        WHERE i.subject_id = ?
    ");
    $stmt->execute([$subjectId]);
    $existingAnalysis = $stmt->fetch();
    
    // Check if fingerprint data exists
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM fingerprint_data WHERE subject_id = ?");
    $stmt->execute([$subjectId]);
    $fingerprintCount = $stmt->fetch()['count'];
    
} catch (Exception $e) {
    error_log("Error loading analysis data: " . $e->getMessage());
    $existingAnalysis = null;
    $fingerprintCount = 0;
}

$pageTitle = 'Assessment Analysis - ' . APP_NAME;
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
                <h1 class="h2">Assessment Analysis</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?php echo url('assessments/fingerprint_collection.php?id=' . $subjectId); ?>" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="fas fa-fingerprint"></i> Edit Fingerprints
                    </a>
                    <?php if ($existingAnalysis): ?>
                    <a href="report.php?id=<?php echo $subjectId; ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-file-pdf"></i> View Report
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> Analysis Errors:</h5>
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

            <?php if ($fingerprintCount < 8): ?>
                <!-- Fingerprint Data Required -->
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Fingerprint Data Required</h5>
                    <p>At least 8 fingerprint patterns are required for accurate analysis. Currently collected: <?php echo $fingerprintCount; ?>/10</p>
                    <a href="<?php echo url('assessments/fingerprint_collection.php?id=' . $subjectId); ?>" class="btn btn-warning">
                        <i class="fas fa-fingerprint"></i> Complete Fingerprint Collection
                    </a>
                </div>
            <?php elseif (!$existingAnalysis): ?>
                <!-- Perform Analysis -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-brain"></i> Ready for Analysis</h5>
                    </div>
                    <div class="card-body">
                        <p>Fingerprint data collection is complete. Click the button below to perform DMIT psychometric analysis.</p>
                        
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                            <button type="submit" name="perform_analysis" class="btn btn-primary btn-lg" id="analysisBtn">
                                <i class="fas fa-cogs"></i> Perform DMIT Analysis
                            </button>
                        </form>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Analysis will calculate multiple intelligence scores, personality profile, brain dominance, 
                                learning styles, and career recommendations based on fingerprint patterns.
                            </small>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Analysis Results -->
                <div class="row">
                    <!-- Multiple Intelligence Scores -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-brain"></i> Multiple Intelligence Scores</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="intelligenceChart" width="400" height="300"></canvas>
                                
                                <div class="mt-3">
                                    <h6>Dominant Intelligence: <span class="badge bg-primary"><?php echo ucfirst(str_replace('_', ' ', $existingAnalysis['dominant_intelligence'])); ?></span></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Personality Profile -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-user-circle"></i> Personality Profile</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="personality-icon mb-3">
                                    <?php
                                    $icons = [
                                        'eagle' => 'fas fa-feather-alt text-warning',
                                        'peacock' => 'fas fa-feather text-info',
                                        'dove' => 'fas fa-dove text-success',
                                        'owl' => 'fas fa-eye text-secondary'
                                    ];
                                    $icon = $icons[$existingAnalysis['primary_type']] ?? 'fas fa-user';
                                    ?>
                                    <i class="<?php echo $icon; ?>" style="font-size: 4rem;"></i>
                                </div>
                                <h4><?php echo ucfirst($existingAnalysis['primary_type']); ?></h4>
                                <?php if ($existingAnalysis['secondary_type']): ?>
                                    <p class="text-muted">Secondary: <?php echo ucfirst($existingAnalysis['secondary_type']); ?></p>
                                <?php endif; ?>
                                
                                <canvas id="discChart" width="300" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Brain Dominance -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-brain"></i> Brain Dominance</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="brain-hemisphere mx-auto mb-3">
                                    <div class="brain-left" style="width: <?php echo $existingAnalysis['left_brain_percent']; ?>%;"></div>
                                    <div class="brain-right" style="width: <?php echo $existingAnalysis['right_brain_percent']; ?>%;"></div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <h6>Left Brain</h6>
                                        <h4 class="text-primary"><?php echo $existingAnalysis['left_brain_percent']; ?>%</h4>
                                    </div>
                                    <div class="col-6">
                                        <h6>Right Brain</h6>
                                        <h4 class="text-success"><?php echo $existingAnalysis['right_brain_percent']; ?>%</h4>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <span class="badge bg-info"><?php echo ucfirst($existingAnalysis['dominance_type']); ?> Dominant</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Learning Styles -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-graduation-cap"></i> Learning Styles (VAK)</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="vakChart" width="400" height="300"></canvas>
                                
                                <div class="mt-3">
                                    <h6>Primary Style: <span class="badge bg-success"><?php echo ucfirst($existingAnalysis['primary_style']); ?></span></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quotient Scores -->
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-chart-bar"></i> Quotient Scores</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-2">
                                        <h3 class="text-primary"><?php echo $existingAnalysis['iq_score']; ?></h3>
                                        <p>IQ Score</p>
                                    </div>
                                    <div class="col-md-2">
                                        <h3 class="text-success"><?php echo $existingAnalysis['eq_score']; ?></h3>
                                        <p>EQ Score</p>
                                    </div>
                                    <div class="col-md-2">
                                        <h3 class="text-warning"><?php echo $existingAnalysis['cq_score']; ?></h3>
                                        <p>CQ Score</p>
                                    </div>
                                    <div class="col-md-2">
                                        <h3 class="text-info"><?php echo $existingAnalysis['aq_score']; ?></h3>
                                        <p>AQ Score</p>
                                    </div>
                                    <div class="col-md-4">
                                        <h3 class="text-dark"><?php echo $existingAnalysis['overall_score']; ?></h3>
                                        <p><strong>Overall Score</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <form method="POST" class="me-2">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        <button type="submit" name="perform_analysis" class="btn btn-outline-primary">
                            <i class="fas fa-redo"></i> Re-analyze
                        </button>
                    </form>
                    <a href="report.php?id=<?php echo $subjectId; ?>" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> Generate Report
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php if ($existingAnalysis): ?>
<script>
// Intelligence Chart
const intelligenceCtx = document.getElementById('intelligenceChart').getContext('2d');
new Chart(intelligenceCtx, {
    type: 'radar',
    data: {
        labels: ['Linguistic', 'Logical-Math', 'Spatial', 'Kinesthetic', 'Musical', 'Interpersonal', 'Intrapersonal', 'Naturalist'],
        datasets: [{
            label: 'Intelligence Scores',
            data: [
                <?php echo $existingAnalysis['linguistic']; ?>,
                <?php echo $existingAnalysis['logical_math']; ?>,
                <?php echo $existingAnalysis['spatial']; ?>,
                <?php echo $existingAnalysis['kinesthetic']; ?>,
                <?php echo $existingAnalysis['musical']; ?>,
                <?php echo $existingAnalysis['interpersonal']; ?>,
                <?php echo $existingAnalysis['intrapersonal']; ?>,
                <?php echo $existingAnalysis['naturalist']; ?>
            ],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        scales: {
            r: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});

// DISC Chart
const discCtx = document.getElementById('discChart').getContext('2d');
new Chart(discCtx, {
    type: 'doughnut',
    data: {
        labels: ['Dominance', 'Influence', 'Steadiness', 'Compliance'],
        datasets: [{
            data: [
                <?php echo $existingAnalysis['disc_d']; ?>,
                <?php echo $existingAnalysis['disc_i']; ?>,
                <?php echo $existingAnalysis['disc_s']; ?>,
                <?php echo $existingAnalysis['disc_c']; ?>
            ],
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// VAK Chart
const vakCtx = document.getElementById('vakChart').getContext('2d');
new Chart(vakCtx, {
    type: 'bar',
    data: {
        labels: ['Visual', 'Auditory', 'Kinesthetic'],
        datasets: [{
            label: 'Learning Style %',
            data: [
                <?php echo $existingAnalysis['visual_percent']; ?>,
                <?php echo $existingAnalysis['auditory_percent']; ?>,
                <?php echo $existingAnalysis['kinesthetic_percent']; ?>
            ],
            backgroundColor: ['#FF9F40', '#FF6384', '#4BC0C0']
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});
</script>
<?php endif; ?>

<script>
// Analysis button loading state - FIXED VERSION
document.getElementById('analysisBtn')?.addEventListener('click', function(e) {
    // Don't prevent default - let form submit
    // Just change the button appearance
    setTimeout(() => {
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Analyzing...';
    }, 100); // Small delay to ensure form submission starts
});

// Alternative: Handle form submission instead of button click
const analysisForm = document.querySelector('form[method="POST"]');
if (analysisForm && document.getElementById('analysisBtn')) {
    analysisForm.addEventListener('submit', function(e) {
        const btn = document.getElementById('analysisBtn');
        if (btn) {
            setTimeout(() => {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Analyzing...';
            }, 50);
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>
