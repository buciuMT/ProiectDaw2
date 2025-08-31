/*
Migration: Insert default users
Created at: 2025-08-27 12:00:00
*/

-- Insert default users
-- Password is 'password' hashed with password_hash()
INSERT IGNORE INTO users (name, email, password, role, verified) VALUES 
('Admin User', 'admin@lib4all.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '1970-01-01'),
('Librarian User', 'librarian@lib4all.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'librarian', '1970-01-01'),
('Member User', 'member@lib4all.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', '1970-01-01');