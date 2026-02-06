<?php
/**
 * Password Reset Script
 * This script resets the admin password to 'admin123'
 * Run once, then delete this file for security
 */

// Database connection
include 'config.php';

// New password: admin123
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update admin user
$email = 'nishika@gmail.com';
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    echo "<h2>✓ Password Reset Successful!</h2>";
    echo "<p>Admin password has been reset.</p>";
    echo "<p><strong>Login credentials:</strong></p>";
    echo "<ul>";
    echo "<li>Email: <code>nishika@gmail.com</code></li>";
    echo "<li>Password: <code>admin123</code></li>";
    echo "</ul>";
    echo "<p><a href='login/login.php'>Go to Login Page</a></p>";
    echo "<hr>";
    echo "<p style='color: red;'><strong>IMPORTANT:</strong> Delete this file (reset_admin_password.php) after use for security!</p>";
} else {
    echo "<h2>✗ Error</h2>";
    echo "<p>Failed to reset password: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>
