-- Add certification_status column to students table if it doesn't exist
ALTER TABLE students
ADD COLUMN IF NOT EXISTS certification_status ENUM('pending', 'approved', 'blocked') DEFAULT 'pending';

-- Update existing students to have a default status
UPDATE students SET certification_status = 'pending' WHERE certification_status IS NULL; 