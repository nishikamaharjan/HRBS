-- Password Reset SQL Script
-- This script resets passwords for old users to known values for testing

-- OPTION 1: Reset admin password to 'admin123'
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE email = 'nishika@gmail.com';

-- OPTION 2: Reset all old user passwords to 'password123'
-- Uncomment the lines below if you want to reset all users

-- UPDATE users 
-- SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
-- WHERE id IN (1, 2, 3, 8, 9, 11, 13);

-- OPTION 3: Create a new admin account with known password
-- Uncomment the lines below to create new admin

-- INSERT INTO users (full_name, email, phone_number, dob, gender, password, role)
-- VALUES (
--     'Test Admin',
--     'admin@test.com',
--     '9800000000',
--     '1990-01-01',
--     'Male',
--     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
--     'Admin'
-- );

-- After running this script, you can login with:
-- Email: nishika@gmail.com (or admin@test.com for new account)
-- Password: admin123

-- NOTE: The hash '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
-- corresponds to the password 'admin123'
