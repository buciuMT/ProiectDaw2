/*
Migration: Create initial database schema
Created at: 2025-08-27 10:00:00
*/

-- Create Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'librarian', 'member') NOT NULL DEFAULT 'member',
    verified DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Books table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    published_year INT,
    genre VARCHAR(100),
    copies_total INT NOT NULL DEFAULT 1,
    copies_available INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- Create BookCategories table
CREATE TABLE IF NOT EXISTS book_categories (
    book_id INT,
    category_id INT,
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Create Loans table
CREATE TABLE IF NOT EXISTS loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    loan_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    status ENUM('active', 'returned', 'overdue') NOT NULL DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);/*
Migration: Create reservations table
Created at: 2025-08-27 10:30:00
*/

CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);/*
Migration: Add cover image to books table
Created at: 2025-08-27 11:00:00
*/

ALTER TABLE books ADD COLUMN cover_image VARCHAR(255) NULL;/*
Migration: Insert default users
Created at: 2025-08-27 12:00:00
*/

-- Insert default users
-- Password is 'password' hashed with password_hash()
INSERT IGNORE INTO users (name, email, password, role, verified) VALUES 
('Admin User', 'admin@lib4all.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '1970-01-01'),
('Librarian User', 'librarian@lib4all.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'librarian', '1970-01-01'),
('Member User', 'member@lib4all.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', '1970-01-01');/*
Migration: Add due date and return date to reservations table
Created at: 2025-08-28 23:26:06
*/

-- Add due_date and returned_at columns to reservations table
ALTER TABLE reservations ADD COLUMN due_date DATE NULL;
ALTER TABLE reservations ADD COLUMN returned_at TIMESTAMP NULL;

-- Add a loan period setting (in days) to the books table
-- This will allow us to calculate due dates based on when books are borrowed
ALTER TABLE books ADD COLUMN loan_period INT DEFAULT 14;/*
Migration: Recreate reservations table without problematic constraint
Created at: 2025-08-28 23:55:00
*/

-- Drop the old trigger if it exists
DROP TRIGGER IF EXISTS prevent_duplicate_active_reservations;

-- Create a new table with the same structure but without the unique constraint
CREATE TABLE reservations_new (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    due_date DATE NULL,
    returned_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Copy data from the old table to the new table
INSERT INTO reservations_new (id, user_id, book_id, reserved_at, status, due_date, returned_at)
SELECT id, user_id, book_id, reserved_at, status, due_date, returned_at FROM reservations;

-- Drop the old table
DROP TABLE reservations;

-- Rename the new table to the original name
RENAME TABLE reservations_new TO reservations;

-- Add indexes for performance
CREATE INDEX idx_reservations_user_id ON reservations(user_id);
CREATE INDEX idx_reservations_book_id ON reservations(book_id);
CREATE INDEX idx_reservations_status ON reservations(status);/*
Migration: Add trigger to prevent duplicate active reservations
Created at: 2025-08-28 23:58:00
*/

-- Drop the old trigger if it exists
DROP TRIGGER IF EXISTS prevent_duplicate_active_reservations;

DELIMITER $

CREATE TRIGGER prevent_duplicate_active_reservations 
BEFORE INSERT ON reservations
FOR EACH ROW
BEGIN
    IF NEW.status = 'active' THEN
        IF EXISTS (SELECT 1 FROM reservations WHERE user_id = NEW.user_id AND book_id = NEW.book_id AND status = 'active') THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'User already has an active reservation for this book';
        END IF;
    END IF;
END$

DELIMITER ;/*
Migration: Add trigger to prevent duplicate active reservations on update
Created at: 2025-08-28 23:59:00
*/

-- Drop the old trigger if it exists
DROP TRIGGER IF EXISTS prevent_duplicate_active_reservations_update;

DELIMITER $

CREATE TRIGGER prevent_duplicate_active_reservations_update
BEFORE UPDATE ON reservations
FOR EACH ROW
BEGIN
    IF NEW.status = 'active' THEN
        IF EXISTS (SELECT 1 FROM reservations WHERE user_id = NEW.user_id AND book_id = NEW.book_id AND status = 'active' AND id != NEW.id) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'User already has an active reservation for this book';
        END IF;
    END IF;
END$

DELIMITER ;
/*
Migration: Add verification token to users table
Created at: 2025-08-30
*/

-- Add verification_token column to users table
ALTER TABLE users ADD COLUMN verification_token VARCHAR(255) NULL UNIQUE;
/*
Migration: Create visits table for tracking page views
Created at: 2025-08-30
*/

CREATE TABLE IF NOT EXISTS visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    page_url VARCHAR(255) NOT NULL,
    is_logged_in BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);