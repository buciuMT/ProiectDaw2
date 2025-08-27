-- Migration: Add cover image to books table
ALTER TABLE books ADD COLUMN cover_image VARCHAR(255) NULL;