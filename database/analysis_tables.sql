-- Analysis Tables for DMIT Psychometric Test System
-- Tables to store analysis results from the AssessmentEngine

-- Intelligence Scores Table
CREATE TABLE IF NOT EXISTS intelligence_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    linguistic DECIMAL(5,2) DEFAULT 0,
    logical_math DECIMAL(5,2) DEFAULT 0,
    spatial DECIMAL(5,2) DEFAULT 0,
    kinesthetic DECIMAL(5,2) DEFAULT 0,
    musical DECIMAL(5,2) DEFAULT 0,
    interpersonal DECIMAL(5,2) DEFAULT 0,
    intrapersonal DECIMAL(5,2) DEFAULT 0,
    naturalist DECIMAL(5,2) DEFAULT 0,
    dominant_intelligence VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subject_intelligence (subject_id)
);

-- Personality Profiles Table
CREATE TABLE IF NOT EXISTS personality_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    primary_type VARCHAR(20) NOT NULL,
    secondary_type VARCHAR(20),
    disc_d DECIMAL(5,2) DEFAULT 0,
    disc_i DECIMAL(5,2) DEFAULT 0,
    disc_s DECIMAL(5,2) DEFAULT 0,
    disc_c DECIMAL(5,2) DEFAULT 0,
    traits JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subject_personality (subject_id)
);

-- Brain Dominance Table
CREATE TABLE IF NOT EXISTS brain_dominance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    left_brain_percent DECIMAL(5,2) DEFAULT 0,
    right_brain_percent DECIMAL(5,2) DEFAULT 0,
    dominance_type VARCHAR(20) NOT NULL,
    characteristics JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subject_brain (subject_id)
);

-- Learning Styles Table
CREATE TABLE IF NOT EXISTS learning_styles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    visual_percent DECIMAL(5,2) DEFAULT 0,
    auditory_percent DECIMAL(5,2) DEFAULT 0,
    kinesthetic_percent DECIMAL(5,2) DEFAULT 0,
    primary_style VARCHAR(20) NOT NULL,
    learning_tips JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subject_learning (subject_id)
);

-- Quotient Scores Table
CREATE TABLE IF NOT EXISTS quotient_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    iq_score INT DEFAULT 100,
    eq_score INT DEFAULT 100,
    cq_score INT DEFAULT 100,
    aq_score INT DEFAULT 100,
    overall_score DECIMAL(5,2) DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subject_quotient (subject_id)
);

-- Career Recommendations Table
CREATE TABLE IF NOT EXISTS career_recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    recommended_streams JSON,
    career_roles JSON,
    riasec_scores JSON,
    suitability_percent DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (subject_id) REFERENCES assessment_subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subject_career (subject_id)
);

-- Update assessment_subjects table to track analysis completion
ALTER TABLE assessment_subjects 
ADD COLUMN IF NOT EXISTS analysis_complete TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS analysis_date TIMESTAMP NULL;

-- Indexes for better performance
CREATE INDEX idx_intelligence_subject ON intelligence_scores(subject_id);
CREATE INDEX idx_personality_subject ON personality_profiles(subject_id);
CREATE INDEX idx_brain_subject ON brain_dominance(subject_id);
CREATE INDEX idx_learning_subject ON learning_styles(subject_id);
CREATE INDEX idx_quotient_subject ON quotient_scores(subject_id);
CREATE INDEX idx_career_subject ON career_recommendations(subject_id);
CREATE INDEX idx_analysis_complete ON assessment_subjects(analysis_complete);
CREATE INDEX idx_analysis_date ON assessment_subjects(analysis_date);
