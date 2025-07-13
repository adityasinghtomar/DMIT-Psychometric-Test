<?php
/**
 * DMIT Assessment Engine
 * Core algorithms for psychometric analysis based on fingerprint data
 */

class AssessmentEngine {
    
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database->getConnection();
    }
    
    /**
     * Perform complete DMIT analysis for a subject
     */
    public function performAnalysis($subjectId) {
        try {
            $this->conn->beginTransaction();
            
            // Get fingerprint data
            $fingerprintData = $this->getFingerprintData($subjectId);
            
            if (empty($fingerprintData)) {
                throw new Exception('No fingerprint data found for analysis');
            }
            
            // Calculate multiple intelligence scores
            $intelligenceScores = $this->calculateIntelligenceScores($fingerprintData);
            $this->saveIntelligenceScores($subjectId, $intelligenceScores);
            
            // Calculate personality profile
            $personalityProfile = $this->calculatePersonalityProfile($fingerprintData);
            $this->savePersonalityProfile($subjectId, $personalityProfile);
            
            // Calculate brain dominance
            $brainDominance = $this->calculateBrainDominance($fingerprintData);
            $this->saveBrainDominance($subjectId, $brainDominance);
            
            // Calculate learning styles
            $learningStyles = $this->calculateLearningStyles($fingerprintData);
            $this->saveLearningStyles($subjectId, $learningStyles);
            
            // Calculate quotient scores
            $quotientScores = $this->calculateQuotientScores($intelligenceScores, $personalityProfile);
            $this->saveQuotientScores($subjectId, $quotientScores);
            
            // Generate career recommendations
            $careerRecommendations = $this->generateCareerRecommendations($intelligenceScores, $personalityProfile);
            $this->saveCareerRecommendations($subjectId, $careerRecommendations);
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'intelligence_scores' => $intelligenceScores,
                'personality_profile' => $personalityProfile,
                'brain_dominance' => $brainDominance,
                'learning_styles' => $learningStyles,
                'quotient_scores' => $quotientScores,
                'career_recommendations' => $careerRecommendations
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Assessment analysis error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Get fingerprint data for analysis
     */
    private function getFingerprintData($subjectId) {
        $stmt = $this->conn->prepare("
            SELECT finger_position, pattern_type, ridge_count 
            FROM fingerprint_data 
            WHERE subject_id = ?
        ");
        $stmt->execute([$subjectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Calculate Multiple Intelligence scores based on DMIT principles
     */
    private function calculateIntelligenceScores($fingerprintData) {
        $scores = [
            'linguistic' => 0,
            'logical_math' => 0,
            'spatial' => 0,
            'kinesthetic' => 0,
            'musical' => 0,
            'interpersonal' => 0,
            'intrapersonal' => 0,
            'naturalist' => 0
        ];
        
        // DMIT mapping based on finger positions and patterns
        $fingerMapping = [
            'left_thumb' => ['kinesthetic', 'interpersonal'],
            'left_index' => ['linguistic', 'logical_math'],
            'left_middle' => ['musical', 'spatial'],
            'left_ring' => ['naturalist', 'intrapersonal'],
            'left_little' => ['interpersonal', 'linguistic'],
            'right_thumb' => ['kinesthetic', 'spatial'],
            'right_index' => ['logical_math', 'naturalist'],
            'right_middle' => ['musical', 'kinesthetic'],
            'right_ring' => ['intrapersonal', 'spatial'],
            'right_little' => ['linguistic', 'interpersonal']
        ];
        
        foreach ($fingerprintData as $finger) {
            $position = $finger['finger_position'];
            $pattern = $finger['pattern_type'];
            $ridgeCount = (int)$finger['ridge_count'];
            
            if (isset($fingerMapping[$position])) {
                $intelligences = $fingerMapping[$position];
                
                // Pattern type scoring
                $patternScore = 0;
                switch ($pattern) {
                    case 'whorl': $patternScore = 3; break;
                    case 'loop': $patternScore = 2; break;
                    case 'arch': $patternScore = 1; break;
                }
                
                // Ridge count scoring
                $ridgeScore = min($ridgeCount / 5, 10); // Normalize to 0-10
                
                $fingerScore = ($patternScore * 3) + $ridgeScore;
                
                // Distribute score to associated intelligences
                foreach ($intelligences as $intelligence) {
                    $scores[$intelligence] += $fingerScore;
                }
            }
        }
        
        // Normalize scores to percentages
        $maxScore = max($scores);
        if ($maxScore > 0) {
            foreach ($scores as $key => $score) {
                $scores[$key] = round(($score / $maxScore) * 100, 2);
            }
        }
        
        // Determine dominant intelligence
        $dominantIntelligence = array_keys($scores, max($scores))[0];
        $scores['dominant_intelligence'] = $dominantIntelligence;
        
        return $scores;
    }
    
    /**
     * Calculate personality profile using DISC model
     */
    private function calculatePersonalityProfile($fingerprintData) {
        $discScores = ['d' => 0, 'i' => 0, 's' => 0, 'c' => 0];
        
        foreach ($fingerprintData as $finger) {
            $ridgeCount = (int)$finger['ridge_count'];
            $pattern = $finger['pattern_type'];
            
            // DISC mapping based on DMIT principles
            switch ($finger['finger_position']) {
                case 'left_thumb':
                case 'right_thumb':
                    $discScores['d'] += ($pattern === 'whorl') ? 3 : 1;
                    break;
                case 'left_index':
                case 'right_index':
                    $discScores['i'] += ($ridgeCount > 20) ? 3 : 1;
                    break;
                case 'left_middle':
                case 'right_middle':
                    $discScores['s'] += ($pattern === 'loop') ? 3 : 1;
                    break;
                case 'left_ring':
                case 'right_ring':
                    $discScores['c'] += ($ridgeCount < 15) ? 3 : 1;
                    break;
            }
        }
        
        // Normalize to percentages
        $total = array_sum($discScores);
        if ($total > 0) {
            foreach ($discScores as $key => $score) {
                $discScores[$key] = round(($score / $total) * 100, 2);
            }
        }
        
        // Determine primary personality type
        $maxScore = max($discScores);
        $primaryType = array_keys($discScores, $maxScore)[0];
        
        $animalMapping = [
            'd' => 'eagle',
            'i' => 'peacock', 
            's' => 'dove',
            'c' => 'owl'
        ];
        
        return [
            'primary_type' => $animalMapping[$primaryType],
            'secondary_type' => $this->getSecondaryType($discScores, $animalMapping),
            'disc_d' => $discScores['d'],
            'disc_i' => $discScores['i'],
            'disc_s' => $discScores['s'],
            'disc_c' => $discScores['c'],
            'traits' => $this->getPersonalityTraits($animalMapping[$primaryType])
        ];
    }
    
    /**
     * Calculate brain dominance
     */
    private function calculateBrainDominance($fingerprintData) {
        $leftBrainScore = 0;
        $rightBrainScore = 0;
        
        foreach ($fingerprintData as $finger) {
            $ridgeCount = (int)$finger['ridge_count'];
            $pattern = $finger['pattern_type'];
            $position = $finger['finger_position'];
            
            $score = ($pattern === 'whorl') ? 3 : (($pattern === 'loop') ? 2 : 1);
            $score += min($ridgeCount / 10, 3);
            
            if (strpos($position, 'left_') === 0) {
                $rightBrainScore += $score; // Left hand = right brain
            } else {
                $leftBrainScore += $score; // Right hand = left brain
            }
        }
        
        $total = $leftBrainScore + $rightBrainScore;
        $leftPercent = round(($leftBrainScore / $total) * 100, 2);
        $rightPercent = round(($rightBrainScore / $total) * 100, 2);
        
        $dominanceType = 'balanced';
        if (abs($leftPercent - $rightPercent) > 10) {
            $dominanceType = ($leftPercent > $rightPercent) ? 'left' : 'right';
        }
        
        return [
            'left_brain_percent' => $leftPercent,
            'right_brain_percent' => $rightPercent,
            'dominance_type' => $dominanceType,
            'characteristics' => $this->getBrainCharacteristics($dominanceType)
        ];
    }
    
    /**
     * Calculate learning styles (VAK)
     */
    private function calculateLearningStyles($fingerprintData) {
        $vakScores = ['visual' => 0, 'auditory' => 0, 'kinesthetic' => 0];
        
        foreach ($fingerprintData as $finger) {
            $ridgeCount = (int)$finger['ridge_count'];
            $pattern = $finger['pattern_type'];
            
            // VAK mapping based on finger analysis
            switch ($finger['finger_position']) {
                case 'left_index':
                case 'right_index':
                    $vakScores['visual'] += ($ridgeCount > 15) ? 3 : 1;
                    break;
                case 'left_middle':
                case 'right_middle':
                    $vakScores['auditory'] += ($pattern === 'loop') ? 3 : 1;
                    break;
                case 'left_thumb':
                case 'right_thumb':
                    $vakScores['kinesthetic'] += ($pattern === 'whorl') ? 3 : 1;
                    break;
            }
        }
        
        // Normalize to percentages
        $total = array_sum($vakScores);
        if ($total > 0) {
            foreach ($vakScores as $key => $score) {
                $vakScores[$key] = round(($score / $total) * 100, 2);
            }
        }
        
        $primaryStyle = array_keys($vakScores, max($vakScores))[0];
        
        return [
            'visual_percent' => $vakScores['visual'],
            'auditory_percent' => $vakScores['auditory'],
            'kinesthetic_percent' => $vakScores['kinesthetic'],
            'primary_style' => $primaryStyle,
            'learning_tips' => $this->getLearningTips($primaryStyle)
        ];
    }
    
    /**
     * Calculate quotient scores
     */
    private function calculateQuotientScores($intelligenceScores, $personalityProfile) {
        // IQ based on logical and linguistic intelligence
        $iq = round(100 + (($intelligenceScores['logical_math'] + $intelligenceScores['linguistic']) / 2 - 50) * 0.6);
        
        // EQ based on interpersonal and intrapersonal intelligence
        $eq = round(100 + (($intelligenceScores['interpersonal'] + $intelligenceScores['intrapersonal']) / 2 - 50) * 0.6);
        
        // CQ based on spatial and naturalist intelligence
        $cq = round(100 + (($intelligenceScores['spatial'] + $intelligenceScores['naturalist']) / 2 - 50) * 0.6);
        
        // AQ based on kinesthetic and musical intelligence
        $aq = round(100 + (($intelligenceScores['kinesthetic'] + $intelligenceScores['musical']) / 2 - 50) * 0.6);
        
        $overallScore = round(($iq + $eq + $cq + $aq) / 4, 2);
        
        return [
            'iq_score' => max(70, min(130, $iq)),
            'eq_score' => max(70, min(130, $eq)),
            'cq_score' => max(70, min(130, $cq)),
            'aq_score' => max(70, min(130, $aq)),
            'overall_score' => $overallScore
        ];
    }
    
    /**
     * Generate career recommendations
     */
    private function generateCareerRecommendations($intelligenceScores, $personalityProfile) {
        $careerMapping = [
            'linguistic' => ['Writer', 'Journalist', 'Teacher', 'Lawyer', 'Translator'],
            'logical_math' => ['Engineer', 'Scientist', 'Mathematician', 'Programmer', 'Analyst'],
            'spatial' => ['Architect', 'Designer', 'Artist', 'Pilot', 'Surgeon'],
            'kinesthetic' => ['Athlete', 'Dancer', 'Mechanic', 'Physical Therapist', 'Chef'],
            'musical' => ['Musician', 'Composer', 'Music Teacher', 'Sound Engineer', 'Music Therapist'],
            'interpersonal' => ['Counselor', 'Sales Manager', 'HR Professional', 'Social Worker', 'Politician'],
            'intrapersonal' => ['Psychologist', 'Researcher', 'Philosopher', 'Writer', 'Entrepreneur'],
            'naturalist' => ['Biologist', 'Environmental Scientist', 'Veterinarian', 'Farmer', 'Geologist']
        ];
        
        $recommendedCareers = [];
        $topIntelligences = array_slice(array_keys($intelligenceScores), 0, 3, true);
        
        foreach ($topIntelligences as $intelligence) {
            if (isset($careerMapping[$intelligence])) {
                $recommendedCareers = array_merge($recommendedCareers, $careerMapping[$intelligence]);
            }
        }
        
        $recommendedCareers = array_unique($recommendedCareers);
        
        return [
            'recommended_streams' => $this->getRecommendedStreams($intelligenceScores),
            'career_roles' => array_slice($recommendedCareers, 0, 10),
            'riasec_scores' => $this->calculateRiasecScores($intelligenceScores),
            'suitability_percent' => round(max($intelligenceScores) * 0.8, 2)
        ];
    }
    
    // Helper methods for saving data
    private function saveIntelligenceScores($subjectId, $scores) {
        $stmt = $this->conn->prepare("
            INSERT INTO intelligence_scores 
            (subject_id, linguistic, logical_math, spatial, kinesthetic, musical, interpersonal, intrapersonal, naturalist, dominant_intelligence) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            linguistic=VALUES(linguistic), logical_math=VALUES(logical_math), spatial=VALUES(spatial),
            kinesthetic=VALUES(kinesthetic), musical=VALUES(musical), interpersonal=VALUES(interpersonal),
            intrapersonal=VALUES(intrapersonal), naturalist=VALUES(naturalist), dominant_intelligence=VALUES(dominant_intelligence)
        ");
        
        $stmt->execute([
            $subjectId, $scores['linguistic'], $scores['logical_math'], $scores['spatial'],
            $scores['kinesthetic'], $scores['musical'], $scores['interpersonal'],
            $scores['intrapersonal'], $scores['naturalist'], $scores['dominant_intelligence']
        ]);
    }
    
    // Additional helper methods would continue here...
    // For brevity, I'll include the key ones
    
    private function getPersonalityTraits($type) {
        $traits = [
            'eagle' => ['Leadership', 'Decisive', 'Goal-oriented', 'Competitive'],
            'peacock' => ['Enthusiastic', 'Optimistic', 'Persuasive', 'Social'],
            'dove' => ['Supportive', 'Patient', 'Loyal', 'Team-oriented'],
            'owl' => ['Analytical', 'Precise', 'Systematic', 'Quality-focused']
        ];
        
        return $traits[$type] ?? [];
    }
    
    private function getBrainCharacteristics($type) {
        $characteristics = [
            'left' => ['Logical thinking', 'Sequential processing', 'Analytical', 'Detail-oriented'],
            'right' => ['Creative thinking', 'Intuitive', 'Holistic', 'Visual processing'],
            'balanced' => ['Flexible thinking', 'Adaptable', 'Well-rounded', 'Versatile']
        ];
        
        return $characteristics[$type] ?? [];
    }
    
    private function getLearningTips($style) {
        $tips = [
            'visual' => ['Use charts and diagrams', 'Color-code information', 'Watch videos', 'Create mind maps'],
            'auditory' => ['Listen to lectures', 'Discuss topics', 'Use mnemonics', 'Read aloud'],
            'kinesthetic' => ['Hands-on activities', 'Take breaks', 'Use movement', 'Practice skills']
        ];
        
        return $tips[$style] ?? [];
    }
    
    private function getRecommendedStreams($intelligenceScores) {
        $streams = [];
        $dominant = array_keys($intelligenceScores, max($intelligenceScores))[0];
        
        $streamMapping = [
            'linguistic' => ['Arts & Literature', 'Journalism', 'Law'],
            'logical_math' => ['Engineering', 'Science', 'Technology', 'Mathematics'],
            'spatial' => ['Architecture', 'Design', 'Fine Arts'],
            'kinesthetic' => ['Sports', 'Physical Education', 'Medicine'],
            'musical' => ['Music', 'Performing Arts'],
            'interpersonal' => ['Psychology', 'Social Work', 'Management'],
            'intrapersonal' => ['Philosophy', 'Research', 'Entrepreneurship'],
            'naturalist' => ['Environmental Science', 'Biology', 'Agriculture']
        ];
        
        return $streamMapping[$dominant] ?? ['General Studies'];
    }
    
    private function calculateRiasecScores($intelligenceScores) {
        return [
            'realistic' => round(($intelligenceScores['kinesthetic'] + $intelligenceScores['naturalist']) / 2, 2),
            'investigative' => round(($intelligenceScores['logical_math'] + $intelligenceScores['intrapersonal']) / 2, 2),
            'artistic' => round(($intelligenceScores['musical'] + $intelligenceScores['spatial']) / 2, 2),
            'social' => round(($intelligenceScores['interpersonal'] + $intelligenceScores['linguistic']) / 2, 2),
            'enterprising' => round(($intelligenceScores['interpersonal'] + $intelligenceScores['logical_math']) / 2, 2),
            'conventional' => round(($intelligenceScores['logical_math'] + $intelligenceScores['linguistic']) / 2, 2)
        ];
    }
    
    private function getSecondaryType($discScores, $animalMapping) {
        arsort($discScores);
        $types = array_keys($discScores);
        return isset($types[1]) ? $animalMapping[$types[1]] : null;
    }
    
    // Additional save methods
    private function savePersonalityProfile($subjectId, $profile) {
        $stmt = $this->conn->prepare("
            INSERT INTO personality_profiles 
            (subject_id, primary_type, secondary_type, disc_d, disc_i, disc_s, disc_c, traits) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            primary_type=VALUES(primary_type), secondary_type=VALUES(secondary_type),
            disc_d=VALUES(disc_d), disc_i=VALUES(disc_i), disc_s=VALUES(disc_s), disc_c=VALUES(disc_c),
            traits=VALUES(traits)
        ");
        
        $stmt->execute([
            $subjectId, $profile['primary_type'], $profile['secondary_type'],
            $profile['disc_d'], $profile['disc_i'], $profile['disc_s'], $profile['disc_c'],
            json_encode($profile['traits'])
        ]);
    }
    
    private function saveBrainDominance($subjectId, $dominance) {
        $stmt = $this->conn->prepare("
            INSERT INTO brain_dominance 
            (subject_id, left_brain_percent, right_brain_percent, dominance_type, characteristics) 
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            left_brain_percent=VALUES(left_brain_percent), right_brain_percent=VALUES(right_brain_percent),
            dominance_type=VALUES(dominance_type), characteristics=VALUES(characteristics)
        ");
        
        $stmt->execute([
            $subjectId, $dominance['left_brain_percent'], $dominance['right_brain_percent'],
            $dominance['dominance_type'], json_encode($dominance['characteristics'])
        ]);
    }
    
    private function saveLearningStyles($subjectId, $styles) {
        $stmt = $this->conn->prepare("
            INSERT INTO learning_styles 
            (subject_id, visual_percent, auditory_percent, kinesthetic_percent, primary_style, learning_tips) 
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            visual_percent=VALUES(visual_percent), auditory_percent=VALUES(auditory_percent),
            kinesthetic_percent=VALUES(kinesthetic_percent), primary_style=VALUES(primary_style),
            learning_tips=VALUES(learning_tips)
        ");
        
        $stmt->execute([
            $subjectId, $styles['visual_percent'], $styles['auditory_percent'],
            $styles['kinesthetic_percent'], $styles['primary_style'], json_encode($styles['learning_tips'])
        ]);
    }
    
    private function saveQuotientScores($subjectId, $scores) {
        $stmt = $this->conn->prepare("
            INSERT INTO quotient_scores 
            (subject_id, iq_score, eq_score, cq_score, aq_score, overall_score) 
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            iq_score=VALUES(iq_score), eq_score=VALUES(eq_score), cq_score=VALUES(cq_score),
            aq_score=VALUES(aq_score), overall_score=VALUES(overall_score)
        ");
        
        $stmt->execute([
            $subjectId, $scores['iq_score'], $scores['eq_score'],
            $scores['cq_score'], $scores['aq_score'], $scores['overall_score']
        ]);
    }
    
    private function saveCareerRecommendations($subjectId, $recommendations) {
        $stmt = $this->conn->prepare("
            INSERT INTO career_recommendations 
            (subject_id, recommended_streams, career_roles, riasec_scores, suitability_percent) 
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            recommended_streams=VALUES(recommended_streams), career_roles=VALUES(career_roles),
            riasec_scores=VALUES(riasec_scores), suitability_percent=VALUES(suitability_percent)
        ");
        
        $stmt->execute([
            $subjectId, json_encode($recommendations['recommended_streams']),
            json_encode($recommendations['career_roles']), json_encode($recommendations['riasec_scores']),
            $recommendations['suitability_percent']
        ]);
    }
}
?>
