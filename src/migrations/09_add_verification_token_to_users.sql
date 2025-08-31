
/*
Migration: Add verification token to users table
Created at: 2025-08-30
*/

-- Add verification_token column to users table
ALTER TABLE users ADD COLUMN verification_token VARCHAR(255) NULL UNIQUE;
