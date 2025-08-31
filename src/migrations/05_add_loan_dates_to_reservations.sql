/*
Migration: Add due date and return date to reservations table
Created at: 2025-08-28 23:26:06
*/

-- Add due_date and returned_at columns to reservations table
ALTER TABLE reservations ADD COLUMN due_date DATE NULL;
ALTER TABLE reservations ADD COLUMN returned_at TIMESTAMP NULL;

-- Add a loan period setting (in days) to the books table
-- This will allow us to calculate due dates based on when books are borrowed
ALTER TABLE books ADD COLUMN loan_period INT DEFAULT 14;