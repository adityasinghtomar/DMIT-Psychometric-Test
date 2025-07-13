<?php
/**
 * PDF Report Generator - DMIT Psychometric Test System
 * Generate professional PDF reports using HTML/CSS
 */

class ReportGenerator {
    
    private $conn;
    private $subjectData;
    private $analysisData;
    
    public function __construct($database) {
        $this->conn = $database->getConnection();
    }
    
    /**
     * Generate complete DMIT assessment report
     */
    public function generateReport($subjectId, $reportType = 'standard') {
        try {
            // Load all data
            $this->loadSubjectData($subjectId);
            $this->loadAnalysisData($subjectId);
            
            if (!$this->subjectData || !$this->analysisData) {
                throw new Exception('Incomplete data for report generation');
            }
            
            // Generate HTML report
            $htmlContent = $this->generateHTMLReport($reportType);
            
            // Save report record
            $reportId = $this->saveReportRecord($subjectId, $reportType, $htmlContent);
            
            return [
                'success' => true,
                'report_id' => $reportId,
                'html_content' => $htmlContent,
                'subject_name' => $this->subjectData['subject_name']
            ];
            
        } catch (Exception $e) {
            error_log("Report generation error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Load subject data
     */
    private function loadSubjectData($subjectId) {
        $stmt = $this->conn->prepare("
            SELECT s.*, u.first_name as created_by_first, u.last_name as created_by_last
            FROM assessment_subjects s
            LEFT JOIN users u ON s.user_id = u.id
            WHERE s.id = ?
        ");
        $stmt->execute([$subjectId]);
        $this->subjectData = $stmt->fetch();
    }
    
    /**
     * Load analysis data
     */
    private function loadAnalysisData($subjectId) {
        // Intelligence scores
        $stmt = $this->conn->prepare("SELECT * FROM intelligence_scores WHERE subject_id = ?");
        $stmt->execute([$subjectId]);
        $intelligence = $stmt->fetch();
        
        // Personality profile
        $stmt = $this->conn->prepare("SELECT * FROM personality_profiles WHERE subject_id = ?");
        $stmt->execute([$subjectId]);
        $personality = $stmt->fetch();
        
        // Brain dominance
        $stmt = $this->conn->prepare("SELECT * FROM brain_dominance WHERE subject_id = ?");
        $stmt->execute([$subjectId]);
        $brain = $stmt->fetch();
        
        // Learning styles
        $stmt = $this->conn->prepare("SELECT * FROM learning_styles WHERE subject_id = ?");
        $stmt->execute([$subjectId]);
        $learning = $stmt->fetch();
        
        // Quotient scores
        $stmt = $this->conn->prepare("SELECT * FROM quotient_scores WHERE subject_id = ?");
        $stmt->execute([$subjectId]);
        $quotients = $stmt->fetch();
        
        // Career recommendations
        $stmt = $this->conn->prepare("SELECT * FROM career_recommendations WHERE subject_id = ?");
        $stmt->execute([$subjectId]);
        $career = $stmt->fetch();
        
        if ($intelligence && $personality && $brain && $learning && $quotients) {
            $this->analysisData = [
                'intelligence' => $intelligence,
                'personality' => $personality,
                'brain' => $brain,
                'learning' => $learning,
                'quotients' => $quotients,
                'career' => $career
            ];
        }
    }
    
    /**
     * Generate HTML report content
     */
    private function generateHTMLReport($reportType) {
        $referenceId = generateReferenceId('DMIT');
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>DMIT Assessment Report - <?php echo htmlspecialchars($this->subjectData['subject_name']); ?></title>

            <!-- Print-specific meta tags to control browser print behavior -->
            <meta name="print-color-adjust" content="exact">
            <meta name="color-scheme" content="light">

            <!-- Additional print control -->
            <style type="text/css" media="print">
                /* Force hide browser headers/footers */
                @page {
                    margin: 15mm;
                    size: A4;
                    /* Remove browser headers/footers */
                    @top-left { content: ""; }
                    @top-center { content: ""; }
                    @top-right { content: ""; }
                    @bottom-left { content: ""; }
                    @bottom-center { content: ""; }
                    @bottom-right { content: ""; }
                }

                /* Hide any browser-generated content */
                body::before, body::after { display: none !important; }
                html::before, html::after { display: none !important; }
            </style>
            <style>
                /* Print-friendly CSS for DMIT Report */
                body {
                    font-family: 'Times New Roman', serif;
                    line-height: 1.4;
                    color: #000;
                    margin: 0;
                    padding: 15mm;
                    font-size: 12pt;
                    background: white;
                }

                /* Page setup for printing */
                @page {
                    size: A4;
                    margin: 15mm;
                }

                /* Header styling */
                .header {
                    text-align: center;
                    border-bottom: 2px solid #000;
                    padding-bottom: 15px;
                    margin-bottom: 25px;
                    page-break-after: avoid;
                }
                .logo {
                    font-size: 24pt;
                    color: #000;
                    margin-bottom: 8px;
                    font-weight: bold;
                }
                .report-title {
                    font-size: 18pt;
                    color: #000;
                    margin: 8px 0;
                    font-weight: bold;
                }

                /* Subject information */
                .subject-info {
                    background: #f5f5f5;
                    padding: 15px;
                    border: 1px solid #ccc;
                    margin-bottom: 25px;
                    page-break-inside: avoid;
                }
                .info-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 10px;
                }
                .info-item {
                    padding: 8px;
                    background: white;
                    border: 1px solid #ddd;
                    border-left: 3px solid #000;
                }
                .info-item strong {
                    font-weight: bold;
                }

                /* Section styling */
                .section {
                    margin-bottom: 30px;
                    page-break-inside: avoid;
                }
                .section-title {
                    font-size: 16pt;
                    color: #000;
                    border-bottom: 1px solid #000;
                    padding-bottom: 8px;
                    margin-bottom: 15px;
                    font-weight: bold;
                    page-break-after: avoid;
                }

                /* Intelligence scores grid */
                .intelligence-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 15px;
                    margin-bottom: 15px;
                }
                .intelligence-item {
                    background: #f9f9f9;
                    padding: 12px;
                    border: 1px solid #ccc;
                    text-align: center;
                    page-break-inside: avoid;
                }
                .score {
                    font-size: 20pt;
                    font-weight: bold;
                    color: #000;
                    display: block;
                    margin-bottom: 5px;
                }
                .intelligence-item h4 {
                    font-size: 11pt;
                    margin: 5px 0;
                    font-weight: bold;
                }
                .intelligence-item p {
                    font-size: 9pt;
                    margin: 0;
                    color: #555;
                }

                /* Personality section */
                .personality-section {
                    text-align: center;
                    background: #f9f9f9;
                    padding: 20px;
                    border: 1px solid #ccc;
                    page-break-inside: avoid;
                }
                .personality-icon {
                    font-size: 24pt;
                    margin-bottom: 10px;
                    color: #000;
                }

                /* Brain dominance visualization - simplified for print */
                .brain-visual {
                    text-align: center;
                    margin: 15px 0;
                    page-break-inside: avoid;
                }
                .brain-hemisphere {
                    width: 120px;
                    height: 120px;
                    border: 2px solid #000;
                    display: inline-block;
                    margin: 0 10px;
                    position: relative;
                    vertical-align: top;
                }
                .brain-left, .brain-right {
                    position: absolute;
                    top: 0;
                    width: 50%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    font-size: 10pt;
                    color: #000;
                }
                .brain-left {
                    left: 0;
                    background: #e0e0e0;
                    border-right: 1px solid #000;
                }
                .brain-right {
                    right: 0;
                    background: #f0f0f0;
                    border-left: 1px solid #000;
                }

                /* Quotient scores grid */
                .quotient-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 15px;
                    margin-bottom: 15px;
                }
                .quotient-item {
                    text-align: center;
                    background: #f9f9f9;
                    padding: 12px;
                    border: 1px solid #ccc;
                    page-break-inside: avoid;
                }
                    padding: 20px;
                    border-radius: 8px;
                }
                /* Career recommendations */
                .career-list {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 10px;
                    margin-bottom: 15px;
                }
                .career-item {
                    background: #f5f5f5;
                    padding: 8px 12px;
                    border: 1px solid #ccc;
                    border-left: 3px solid #000;
                    font-size: 10pt;
                    page-break-inside: avoid;
                }

                /* Footer styling */
                .footer {
                    margin-top: 30px;
                    padding-top: 15px;
                    border-top: 1px solid #000;
                    text-align: center;
                    color: #000;
                    font-size: 10pt;
                    page-break-inside: avoid;
                }

                /* Disclaimer */
                .disclaimer {
                    background: #f9f9f9;
                    border: 1px solid #ccc;
                    padding: 15px;
                    margin-top: 20px;
                    font-size: 9pt;
                    page-break-inside: avoid;
                }

                /* Tables for better data presentation */
                .data-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 15px 0;
                    font-size: 10pt;
                }
                .data-table th, .data-table td {
                    border: 1px solid #000;
                    padding: 8px;
                    text-align: left;
                }
                .data-table th {
                    background: #f0f0f0;
                    font-weight: bold;
                }

                /* Print-specific styles */
                @media print {
                    /* Hide browser headers and footers */
                    @page {
                        size: A4;
                        margin: 15mm;
                        /* Remove browser headers/footers */
                        margin-top: 15mm;
                        margin-bottom: 15mm;
                    }

                    body {
                        margin: 0;
                        padding: 0;
                        font-size: 11pt;
                        line-height: 1.3;
                        background: white !important;
                        -webkit-print-color-adjust: exact;
                    }

                    /* Hide all browser UI elements */
                    body::before,
                    body::after {
                        display: none !important;
                    }

                    .section {
                        page-break-inside: avoid;
                        margin-bottom: 20px;
                    }
                    .header {
                        page-break-after: avoid;
                    }
                    .section-title {
                        page-break-after: avoid;
                    }
                    .intelligence-grid {
                        grid-template-columns: repeat(2, 1fr);
                        gap: 10px;
                    }
                    .quotient-grid {
                        grid-template-columns: repeat(2, 1fr);
                        gap: 10px;
                    }
                    .career-list {
                        grid-template-columns: 1fr;
                        gap: 5px;
                    }
                    .brain-visual {
                        page-break-inside: avoid;
                    }

                    /* Ensure only our footer shows */
                    .footer {
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        background: white;
                        border-top: 1px solid #000;
                        padding: 10px;
                        font-size: 9pt;
                        text-align: center;
                    }

                    /* Ensure no color backgrounds in print */
                    * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                    }
                }
            </style>
        </head>
        <body>
            <!-- Header -->
            <div class="header">
                <div class="logo">🧠 DMIT Psychometric Assessment</div>
                <div class="report-title">Comprehensive Career Guidance Report</div>
                <p><strong>Reference ID:</strong> <?php echo $referenceId; ?></p>
                <p><strong>Generated on:</strong> <?php echo date('F d, Y'); ?></p>
            </div>

            <!-- Subject Information -->
            <div class="subject-info">
                <h2>Subject Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Name:</strong><br>
                        <?php echo htmlspecialchars($this->subjectData['subject_name']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Age:</strong><br>
                        <?php echo $this->subjectData['age_at_assessment']; ?> years
                    </div>
                    <div class="info-item">
                        <strong>Gender:</strong><br>
                        <?php echo ucfirst($this->subjectData['gender']); ?>
                    </div>
                    <div class="info-item">
                        <strong>Date of Birth:</strong><br>
                        <?php echo formatDate($this->subjectData['date_of_birth'], 'M d, Y'); ?>
                    </div>
                    <?php if ($this->subjectData['school_name']): ?>
                    <div class="info-item">
                        <strong>School:</strong><br>
                        <?php echo htmlspecialchars($this->subjectData['school_name']); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($this->subjectData['parent_name']): ?>
                    <div class="info-item">
                        <strong>Parent/Guardian:</strong><br>
                        <?php echo htmlspecialchars($this->subjectData['parent_name']); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Multiple Intelligence Scores -->
            <div class="section">
                <h2 class="section-title">Multiple Intelligence Analysis</h2>
                <div class="intelligence-grid">
                    <div class="intelligence-item">
                        <div class="score"><?php echo $this->analysisData['intelligence']['linguistic']; ?>%</div>
                        <h4>Linguistic Intelligence</h4>
                        <p>Word smart - ability with language and words</p>
                    </div>
                    <div class="intelligence-item">
                        <div class="score"><?php echo $this->analysisData['intelligence']['logical_math']; ?>%</div>
                        <h4>Logical-Mathematical</h4>
                        <p>Number smart - logical and mathematical ability</p>
                    </div>
                    <div class="intelligence-item">
                        <div class="score"><?php echo $this->analysisData['intelligence']['spatial']; ?>%</div>
                        <h4>Spatial Intelligence</h4>
                        <p>Picture smart - visual and spatial awareness</p>
                    </div>
                    <div class="intelligence-item">
                        <div class="score"><?php echo $this->analysisData['intelligence']['kinesthetic']; ?>%</div>
                        <h4>Bodily-Kinesthetic</h4>
                        <p>Body smart - physical and movement skills</p>
                    </div>
                    <div class="intelligence-item">
                        <div class="score"><?php echo $this->analysisData['intelligence']['musical']; ?>%</div>
                        <h4>Musical Intelligence</h4>
                        <p>Music smart - musical and rhythmic ability</p>
                    </div>
                    <div class="intelligence-item">
                        <div class="score"><?php echo $this->analysisData['intelligence']['interpersonal']; ?>%</div>
                        <h4>Interpersonal</h4>
                        <p>People smart - understanding others</p>
                    </div>
                    <div class="intelligence-item">
                        <div class="score"><?php echo $this->analysisData['intelligence']['intrapersonal']; ?>%</div>
                        <h4>Intrapersonal</h4>
                        <p>Self smart - understanding yourself</p>
                    </div>
                    <div class="intelligence-item">
                        <div class="score"><?php echo $this->analysisData['intelligence']['naturalist']; ?>%</div>
                        <h4>Naturalist Intelligence</h4>
                        <p>Nature smart - understanding nature</p>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <h3>Dominant Intelligence: 
                        <span style="color: #007bff;">
                            <?php echo ucfirst(str_replace('_', '-', $this->analysisData['intelligence']['dominant_intelligence'])); ?>
                        </span>
                    </h3>
                </div>
            </div>

            <!-- Personality Profile -->
            <div class="section">
                <h2 class="section-title">Personality Profile</h2>
                <div class="personality-section">
                    <div class="personality-icon">
                        <?php
                        $icons = [
                            'eagle' => '🦅',
                            'peacock' => '🦚', 
                            'dove' => '🕊️',
                            'owl' => '🦉'
                        ];
                        echo $icons[$this->analysisData['personality']['primary_type']] ?? '👤';
                        ?>
                    </div>
                    <h3>Primary Type: <?php echo ucfirst($this->analysisData['personality']['primary_type']); ?></h3>
                    <?php if ($this->analysisData['personality']['secondary_type']): ?>
                        <p>Secondary Type: <?php echo ucfirst($this->analysisData['personality']['secondary_type']); ?></p>
                    <?php endif; ?>
                    
                    <div style="margin-top: 30px;">
                        <h4>DISC Profile</h4>
                        <div class="quotient-grid">
                            <div class="quotient-item">
                                <div class="score" style="color: #dc3545;"><?php echo $this->analysisData['personality']['disc_d']; ?>%</div>
                                <h5>Dominance</h5>
                            </div>
                            <div class="quotient-item">
                                <div class="score" style="color: #ffc107;"><?php echo $this->analysisData['personality']['disc_i']; ?>%</div>
                                <h5>Influence</h5>
                            </div>
                            <div class="quotient-item">
                                <div class="score" style="color: #28a745;"><?php echo $this->analysisData['personality']['disc_s']; ?>%</div>
                                <h5>Steadiness</h5>
                            </div>
                            <div class="quotient-item">
                                <div class="score" style="color: #17a2b8;"><?php echo $this->analysisData['personality']['disc_c']; ?>%</div>
                                <h5>Compliance</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Brain Dominance -->
            <div class="section">
                <h2 class="section-title">Brain Dominance Analysis</h2>
                <div style="text-align: center;">
                    <div class="brain-visual">
                        <div class="brain-hemisphere">
                            <div class="brain-left" style="width: <?php echo $this->analysisData['brain']['left_brain_percent']; ?>%;">
                                <?php echo $this->analysisData['brain']['left_brain_percent']; ?>%
                            </div>
                            <div class="brain-right" style="width: <?php echo $this->analysisData['brain']['right_brain_percent']; ?>%;">
                                <?php echo $this->analysisData['brain']['right_brain_percent']; ?>%
                            </div>
                        </div>
                    </div>
                    <h3>Dominance Type: <?php echo ucfirst($this->analysisData['brain']['dominance_type']); ?> Brain Dominant</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
                        <div>
                            <h4>Left Brain (<?php echo $this->analysisData['brain']['left_brain_percent']; ?>%)</h4>
                            <p>Logical, analytical, sequential thinking</p>
                        </div>
                        <div>
                            <h4>Right Brain (<?php echo $this->analysisData['brain']['right_brain_percent']; ?>%)</h4>
                            <p>Creative, intuitive, holistic thinking</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Learning Styles -->
            <div class="section">
                <h2 class="section-title">Learning Styles (VAK Analysis)</h2>
                <div class="quotient-grid" style="grid-template-columns: repeat(3, 1fr);">
                    <div class="quotient-item">
                        <div class="score" style="color: #ff6b6b;"><?php echo $this->analysisData['learning']['visual_percent']; ?>%</div>
                        <h4>Visual Learning</h4>
                        <p>Learning through seeing</p>
                    </div>
                    <div class="quotient-item">
                        <div class="score" style="color: #4ecdc4;"><?php echo $this->analysisData['learning']['auditory_percent']; ?>%</div>
                        <h4>Auditory Learning</h4>
                        <p>Learning through hearing</p>
                    </div>
                    <div class="quotient-item">
                        <div class="score" style="color: #45b7d1;"><?php echo $this->analysisData['learning']['kinesthetic_percent']; ?>%</div>
                        <h4>Kinesthetic Learning</h4>
                        <p>Learning through doing</p>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <h3>Primary Learning Style: 
                        <span style="color: #007bff;">
                            <?php echo ucfirst($this->analysisData['learning']['primary_style']); ?>
                        </span>
                    </h3>
                </div>
            </div>

            <!-- Quotient Scores -->
            <div class="section">
                <h2 class="section-title">Quotient Scores</h2>
                <div class="quotient-grid">
                    <div class="quotient-item">
                        <div class="score" style="color: #007bff;"><?php echo $this->analysisData['quotients']['iq_score']; ?></div>
                        <h4>IQ Score</h4>
                        <p>Intelligence Quotient</p>
                    </div>
                    <div class="quotient-item">
                        <div class="score" style="color: #28a745;"><?php echo $this->analysisData['quotients']['eq_score']; ?></div>
                        <h4>EQ Score</h4>
                        <p>Emotional Quotient</p>
                    </div>
                    <div class="quotient-item">
                        <div class="score" style="color: #ffc107;"><?php echo $this->analysisData['quotients']['cq_score']; ?></div>
                        <h4>CQ Score</h4>
                        <p>Creative Quotient</p>
                    </div>
                    <div class="quotient-item">
                        <div class="score" style="color: #17a2b8;"><?php echo $this->analysisData['quotients']['aq_score']; ?></div>
                        <h4>AQ Score</h4>
                        <p>Adversity Quotient</p>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 30px;">
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                        <h3>Overall Score: <span style="color: #007bff; font-size: 1.5em;"><?php echo $this->analysisData['quotients']['overall_score']; ?></span></h3>
                    </div>
                </div>
            </div>

            <!-- Career Recommendations -->
            <?php if ($this->analysisData['career']): ?>
            <div class="section">
                <h2 class="section-title">Career Recommendations</h2>
                
                <?php 
                $streams = json_decode($this->analysisData['career']['recommended_streams'], true);
                $roles = json_decode($this->analysisData['career']['career_roles'], true);
                ?>
                
                <?php if ($streams): ?>
                <div style="margin-bottom: 30px;">
                    <h3>Recommended Career Streams</h3>
                    <div class="career-list">
                        <?php foreach ($streams as $stream): ?>
                            <div class="career-item">
                                <strong><?php echo htmlspecialchars($stream); ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($roles): ?>
                <div>
                    <h3>Suitable Career Roles</h3>
                    <div class="career-list">
                        <?php foreach (array_slice($roles, 0, 10) as $role): ?>
                            <div class="career-item">
                                <?php echo htmlspecialchars($role); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div style="text-align: center; margin-top: 30px;">
                    <div style="background: #e3f2fd; padding: 15px; border-radius: 8px;">
                        <h4>Career Suitability: <?php echo $this->analysisData['career']['suitability_percent']; ?>%</h4>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Disclaimer -->
            <div class="disclaimer">
                <h3>Important Disclaimer</h3>
                <p>
                    This report is based on Dermatoglyphics Multiple Intelligence Test (DMIT) analysis and represents 
                    inborn potential and tendencies. The results should be used as guidance for educational and career 
                    planning but do not guarantee success in any particular field. Individual effort, training, 
                    environment, and personal choices play crucial roles in determining actual achievements and career success.
                </p>
                <p>
                    This assessment does not replace professional psychological evaluation, medical advice, or academic counseling. 
                    For specific concerns about learning difficulties or career decisions, please consult qualified professionals.
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p><strong><?php echo APP_NAME; ?></strong></p>
                <p>Professional DMIT Assessment & Career Guidance</p>
                <p>Report generated on <?php echo date('F d, Y \a\t g:i A'); ?></p>
                <p>Reference ID: <?php echo $referenceId; ?></p>
            </div>
        </body>
        </html>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Save report record in database
     */
    private function saveReportRecord($subjectId, $reportType, $htmlContent) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO assessment_reports 
                (subject_id, report_type, report_status, report_data, generated_by) 
                VALUES (?, ?, 'completed', ?, ?)
                ON DUPLICATE KEY UPDATE
                report_status = 'completed', report_data = VALUES(report_data), 
                generated_at = CURRENT_TIMESTAMP
            ");
            
            $stmt->execute([
                $subjectId, 
                $reportType, 
                $htmlContent, 
                $_SESSION['user_id']
            ]);
            
            return $this->conn->lastInsertId() ?: $this->getExistingReportId($subjectId);
            
        } catch (Exception $e) {
            error_log("Error saving report record: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get existing report ID
     */
    private function getExistingReportId($subjectId) {
        $stmt = $this->conn->prepare("
            SELECT id FROM assessment_reports 
            WHERE subject_id = ? 
            ORDER BY generated_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$subjectId]);
        $result = $stmt->fetch();
        return $result ? $result['id'] : null;
    }
}
?>
