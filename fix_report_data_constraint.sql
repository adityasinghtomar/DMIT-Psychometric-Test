-- Fix Report Data Constraint - DMIT Psychometric Test System
-- Remove JSON validation constraint from report_data column to allow HTML content

-- The current constraint expects JSON but we need to store HTML content
-- Error: CONSTRAINT `assessment_reports.report_data` failed

-- Step 1: Remove the JSON validation constraint
ALTER TABLE `assessment_reports` 
DROP CHECK `assessment_reports.report_data`;

-- Step 2: Modify the column to allow HTML content without JSON constraint
ALTER TABLE `assessment_reports` 
MODIFY COLUMN `report_data` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL;

-- Verification query to check the constraint is removed
-- SHOW CREATE TABLE assessment_reports;
