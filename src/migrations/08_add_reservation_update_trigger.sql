/*
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