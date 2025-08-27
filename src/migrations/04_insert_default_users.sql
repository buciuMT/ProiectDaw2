-- Migration: Insert default users
-- Created at: 2025-08-27 12:00:00

-- Insert default users
-- Password is 'password' hashed with password_hash()
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@lib4all.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Librarian User', 'librarian@lib4all.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'librarian'),
('Member User', 'member@lib4all.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member');