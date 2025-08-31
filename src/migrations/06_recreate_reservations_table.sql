/*
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
CREATE INDEX idx_reservations_status ON reservations(status);