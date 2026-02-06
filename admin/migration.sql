-- Migration script to add room_number and capacity columns to rooms table
-- Run this in phpMyAdmin or MySQL command line

USE hrbs;

-- Add room_number column if it doesn't exist
ALTER TABLE rooms 
ADD COLUMN IF NOT EXISTS room_number VARCHAR(50) DEFAULT NULL AFTER id;

-- Add capacity column if it doesn't exist
ALTER TABLE rooms 
ADD COLUMN IF NOT EXISTS capacity INT DEFAULT NULL AFTER price;

-- Verify the changes
DESCRIBE rooms;
