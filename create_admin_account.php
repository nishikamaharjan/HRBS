<?php
// create_admin_account.php
require_once 'config.php';

$full_name = 'New Admin';
$email = 'newadmin@gmail.com';
$password = 'admin123'; // The password you want to use
$phone_number = '9800000000';
$dob = '1990-01-01';
$gender = 'Other';
$role = 'Admin';

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if email already exists
$check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "User with email $email already exists. Deleting...<br>";
    $delete_stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
    $delete_stmt->bind_param("s", $email);
    if ($delete_stmt->execute()) {
        echo "Deleted existing user.<br>";
    } else {
        die("Delete failed: " . $conn->error);
    }
}
// Always insert new user
$stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, dob, gender, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $full_name, $email, $phone_number, $dob, $gender, $hashed_password, $role);

if ($stmt->execute()) {
    echo "Admin account created successfully.<br>";
    echo "Email: $email<br>";
    echo "Password: $password<br>";
} else {
    echo "Error creating admin account: " . $conn->error . "<br>";
}
$stmt->close();
/*
} else {
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, dob, gender, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $full_name, $email, $phone_number, $dob, $gender, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "Admin account created successfully.\n";
        echo "Email: $email\n";
        echo "Password: $password\n";
    } else {
        echo "Error creating admin account: " . $conn->error . "\n";
    }
    $stmt->close();
}
*/

$check_stmt->close();
$conn->close();
?>
