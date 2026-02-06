<?php
// test_login_debug.php - Diagnostic script to test login without redirects
session_start();

// Enable error display for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Login Debug Test</h2>";
echo "<p>This script tests login authentication with visible debug output.</p>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim inputs to remove whitespace
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    echo "<h3>Debug Information:</h3>";
    echo "<pre>";
    echo "Email entered: " . htmlspecialchars($email) . "\n";
    echo "Password length: " . strlen($password) . "\n";
    echo "Email length: " . strlen($email) . "\n";
    echo "\n";

    // Database connection
    include '../config.php';

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    echo "Database connection: SUCCESS\n\n";

    // Prepare SQL query to fetch user by email
    $sql = $conn->prepare("SELECT id, full_name, password, role, created_at FROM users WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $result = $sql->get_result();

    echo "Query executed\n";
    echo "Rows returned: " . $result->num_rows . "\n\n";

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        echo "USER FOUND:\n";
        echo "  ID: " . $user['id'] . "\n";
        echo "  Name: " . htmlspecialchars($user['full_name']) . "\n";
        echo "  Role: " . $user['role'] . "\n";
        echo "  Created: " . $user['created_at'] . "\n";
        echo "  Password hash (first 40 chars): " . substr($user['password'], 0, 40) . "...\n";
        echo "  Password hash length: " . strlen($user['password']) . "\n";
        echo "  Password hash algorithm: " . (strpos($user['password'], '$2y$') === 0 ? 'bcrypt' : 'unknown') . "\n\n";

        // Test password verification
        echo "TESTING PASSWORD VERIFICATION:\n";
        $verify_result = password_verify($password, $user['password']);
        echo "  password_verify() result: " . ($verify_result ? 'SUCCESS ✓' : 'FAILED ✗') . "\n\n";

        if ($verify_result) {
            echo "<strong style='color: green;'>✓ LOGIN WOULD SUCCEED</strong>\n";
            echo "Would redirect to: " . ($user['role'] === 'Admin' ? '../admin/dashboard.php' : '../index.html') . "\n";
        } else {
            echo "<strong style='color: red;'>✗ LOGIN WOULD FAIL - Password verification failed</strong>\n";
            
            // Additional debugging
            echo "\nADDITIONAL DEBUGGING:\n";
            echo "  Testing with trimmed password: " . (password_verify(trim($password), $user['password']) ? 'SUCCESS' : 'FAILED') . "\n";
            echo "  Password hash valid format: " . (password_get_info($user['password'])['algo'] !== null ? 'YES' : 'NO') . "\n";
            
            $hash_info = password_get_info($user['password']);
            echo "  Hash info: ";
            print_r($hash_info);
        }
    } elseif ($result->num_rows === 0) {
        echo "<strong style='color: red;'>✗ EMAIL NOT FOUND IN DATABASE</strong>\n\n";
        
        // Try to find similar emails
        echo "Searching for similar emails...\n";
        $like_email = '%' . $email . '%';
        $search_sql = $conn->prepare("SELECT email FROM users WHERE email LIKE ? LIMIT 5");
        $search_sql->bind_param("s", $like_email);
        $search_sql->execute();
        $search_result = $search_sql->get_result();
        
        if ($search_result->num_rows > 0) {
            echo "Similar emails found:\n";
            while ($row = $search_result->fetch_assoc()) {
                echo "  - " . htmlspecialchars($row['email']) . "\n";
            }
        } else {
            echo "No similar emails found.\n";
        }
    } else {
        echo "<strong style='color: red;'>✗ MULTIPLE RECORDS FOUND (Database error)</strong>\n";
    }

    echo "</pre>";

    // Close connection
    $sql->close();
    $conn->close();
} else {
    // Show form
    ?>
    <form method="POST" style="max-width: 400px; margin: 20px 0;">
        <div style="margin-bottom: 15px;">
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required style="width: 100%; padding: 8px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 8px;">
        </div>
        <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">Test Login</button>
    </form>
    
    <h3>Test Accounts from Database:</h3>
    <ul>
        <li><strong>Admin:</strong> nishika@gmail.com (ID: 3, created: 2024-12-03)</li>
        <li><strong>User:</strong> geleknamgyal51@gmail.com (ID: 1, created: 2024-12-03)</li>
        <li><strong>User:</strong> hisimaharjan1@gmail.com (ID: 2, created: 2024-12-03)</li>
    </ul>
    <p><em>Note: You'll need to know the actual passwords for these accounts to test.</em></p>
    <?php
}
?>
