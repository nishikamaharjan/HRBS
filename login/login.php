<?php
session_start();
ob_start(); // Start output buffering to prevent "headers already sent" errors

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim inputs to remove whitespace, but keep raw password for fallback check
    $email = trim($_POST['email']);
    $password_trimmed = trim($_POST['password']);
    $password_raw = $_POST['password'];

    // Connect to the database
    // Database connection
    include '../config.php';

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL query to fetch user by email (Case-insensitive & Whitespace handling)
    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE LOWER(TRIM(email)) = LOWER(TRIM(?))");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Debug logging
    error_log("=== LOGIN ATTEMPT ===");
    error_log("Email: " . $email);
    error_log("Input Length: " . strlen($password_trimmed));
    error_log("Query returned rows: " . $result->num_rows);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $stored_hash = $user['password'];

        // Debug logging - user found
        error_log("User found - ID: " . $user['id']);
        error_log("Stored Hash: " . substr($stored_hash, 0, 20) . "...");
        
        $login_success = false;
        
        // STRATEGY 1: Verify Trimmed Password (Standard)
        if (password_verify((string)$password_trimmed, (string)$stored_hash)) {
            $login_success = true;
            error_log("Password verify result: SUCCESS (Trimmed)");
        } 
        // STRATEGY 2: Verify Raw Password (Fallback for users with accidental spaces)
        elseif (password_verify($password_raw, $stored_hash)) {
            $login_success = true;
            error_log("Password verify result: SUCCESS (Raw Input)");
        }
        // STRATEGY 3: Legacy MD5 Check
        elseif (md5($password_trimmed) === $stored_hash || md5($password_raw) === $stored_hash) {
            // Legacy MD5 password detected
            error_log("Legacy MD5 password detected. Migrating to bcrypt...");
            
            // Re-hash password (use trimmed version for the new hash to clean it up)
            $new_hash = password_hash($password_trimmed, PASSWORD_DEFAULT);
            
            // Update database with new hash
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_hash, $user['id']);
            if ($update_stmt->execute()) {
                error_log("Password migration successful for User ID: " . $user['id']);
                $user['password'] = $new_hash;
                $login_success = true;
            } else {
                error_log("Password migration failed: " . $update_stmt->error);
            }
            $update_stmt->close();
        } else {
            error_log("Password verify result: FAILED");
        }

        if ($login_success) {
            // Store user data in session
            $_SESSION['user_id'] = $user['id']; // Store user ID in session
            $_SESSION['full_name'] = $user['full_name']; // Store full name in session
            
            // Normalize role for session consistency
            // admin/dashboard.php expects 'Admin', so we force it if the user is an admin
            if (strtolower(trim($user['role'])) === 'admin') {
                $_SESSION['role'] = 'Admin';
            } else {
                $_SESSION['role'] = $user['role']; // Store role in session as is for non-admins
            }

            // Close database connections before redirect
            $stmt->close();
            $conn->close();

            // Clear any output buffer and redirect based on role
            ob_end_clean();
            
            // Normalize role to lower case for comparison
            if ($_SESSION['role'] === 'Admin') {
                error_log("Login successful - Redirecting admin to: ../admin/dashboard.php");
                header('Location: ../admin/dashboard.php');
                exit();
            } else {
                error_log("Login successful - Redirecting user to: ../index.html");
                header('Location: ../index.html');
                exit();
            }
        } else {
            error_log("Login failed - Invalid password");
            $stmt->close();
            $conn->close();
            ob_end_clean();
            header('Location: login.php?error=Invalid%20password');
            exit();
        }
    } else {
        error_log("Login failed - Email not found or multiple records");
        $stmt->close();
        $conn->close();
        ob_end_clean();
        header('Location: login.php?error=Invalid%20email');
        exit();
    }
}

// Flush output buffer if we're displaying the form
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1>Login</h1>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form> <br>
            <p>
                <a href="forgotpassword.php" class="forgot-password-link">Forgot Password?</a>
            </p>
            <p>Don't have an account? <a href="register.php">Sign up here</a>.</p>
        </div>
    </div>
</body>
</html>

