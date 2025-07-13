-- Password Resets Table for DMIT Psychometric Test System
-- This table stores password reset tokens and their expiration

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at),
    INDEX idx_created_at (created_at)
);

-- Add some sample data for testing (optional)
-- Note: These tokens are for demonstration only and should not be used in production

-- Clean up expired tokens (run this periodically)
-- DELETE FROM password_resets WHERE expires_at < NOW() OR used = 1;
