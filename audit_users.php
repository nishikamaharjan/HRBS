<?php
include 'config.php';

echo "=== DATABASE USER AUDIT ===\n";
echo "Connection Charset: " . $conn->character_set_name() . "\n\n";

$result = $conn->query("SELECT id, email, role, password, LENGTH(password) as pass_len, LENGTH(email) as email_len FROM users");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "User ID: " . $row['id'] . "\n";
        echo "Email: '" . $row['email'] . "' (Length: " . $row['email_len'] . ")\n";
        echo "Role: '" . $row['role'] . "'\n";
        echo "Password: '" . substr($row['password'], 0, 10) . "...(" . substr($row['password'], -5) . ")' (Length: " . $row['pass_len'] . ")\n";
        
        // Check for specific issues
        if ($row['pass_len'] < 60) echo "[WARN] Password too short for bcrypt!\n";
        if (preg_match('/\s/', $row['email'])) echo "[WARN] Email contains whitespace!\n";
        if (strpos($row['password'], '$2y$') === false && $row['pass_len'] >= 60) echo "[WARN] Password might not be standard bcrypt!\n";
        
        echo "---------------------------\n";
    }
} else {
    echo "Error querying users: " . $conn->error;
}
$conn->close();
?>
