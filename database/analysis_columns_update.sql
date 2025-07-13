-- Analysis Columns Update for DMIT Psychometric Test System
-- Add missing columns to track analysis completion status

-- Add analysis tracking columns to assessment_subjects table
ALTER TABLE assessment_subjects 
ADD COLUMN IF NOT EXISTS analysis_complete TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS analysis_date TIMESTAMP NULL;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_analysis_complete ON assessment_subjects(analysis_complete);
CREATE INDEX IF NOT EXISTS idx_analysis_date ON assessment_subjects(analysis_date);

-- Update existing records to show analysis status based on existing data
UPDATE assessment_subjects s
SET analysis_complete = 1, analysis_date = CURRENT_TIMESTAMP
WHERE EXISTS (
    SELECT 1 FROM intelligence_scores i WHERE i.subject_id = s.id
) AND analysis_complete = 0;
