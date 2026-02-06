<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $role = $_POST['role'];

    // Update user details
    $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone_number=?, dob=?, gender=?, role=? WHERE id=?");
    $stmt->bind_param("ssssssi", $full_name, $email, $phone_number, $dob, $gender, $role, $id);

    if ($stmt->execute()) {
        // If password is provided, update it separately
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param("si", $password, $id);
            $stmt->execute();
        }
        
        $_SESSION['message'] = "User updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating user: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }

    header("Location: user_management.php");
    exit;
}

// Get user details for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    echo json_encode($user);
    exit;
}
?>
