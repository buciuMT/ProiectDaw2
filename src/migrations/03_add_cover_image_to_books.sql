-- Migration: Add cover image to books table
-- Created at: 2025-08-27 11:00:00

ALTER TABLE books ADD COLUMN cover_image VARCHAR(255) NULL;