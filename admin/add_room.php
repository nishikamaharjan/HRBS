<?php
session_start();
include '../config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $room_number = $_POST['room_number'] ?? null;
    $room_type = $_POST['room_type'] ?? null;
    $price = $_POST['price'] ?? null;
    $capacity = $_POST['capacity'] ?? null;
    $description = $_POST['description'] ?? null;
    $available = isset($_POST['available']) ? 1 : 0;
    
    // Validate required fields
    if (empty($room_type) || empty($price)) {
        $_SESSION['message'] = "Room type and price are required.";
        $_SESSION['message_type'] = "error";
        header("Location: room_management.php");
        exit;
    }
    
    // Validate price
    if (!is_numeric($price) || $price < 0) {
        $_SESSION['message'] = "Please enter a valid price.";
        $_SESSION['message_type'] = "error";
        header("Location: room_management.php");
        exit;
    }
    
    // Handle image upload
    $image_url = null;
    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['room_image'];
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $file_type = mime_content_type($file['tmp_name']);
        
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['message'] = "Only JPG and PNG images are allowed.";
            $_SESSION['message_type'] = "error";
            header("Location: room_management.php");
            exit;
        }
        
        // Validate file size (5MB max)
        $max_size = 5 * 1024 * 1024; // 5MB in bytes
        if ($file['size'] > $max_size) {
            $_SESSION['message'] = "Image size must be less than 5MB.";
            $_SESSION['message_type'] = "error";
            header("Location: room_management.php");
            exit;
        }
        
        // Create uploads directory if it doesn't exist
        $upload_dir = '../uploads/rooms/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $unique_filename = 'room_' . uniqid() . '_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $unique_filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $image_url = 'uploads/rooms/' . $unique_filename;
        } else {
            $_SESSION['message'] = "Failed to upload image.";
            $_SESSION['message_type'] = "error";
            header("Location: room_management.php");
            exit;
        }
    }
    
    // Insert into database
    try {
        $stmt = $conn->prepare("INSERT INTO rooms (room_number, room_type, price, capacity, image_url, description, available) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdissi", $room_number, $room_type, $price, $capacity, $image_url, $description, $available);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Room added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding room: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
        
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['message'] = "Database error: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: room_management.php");
    exit;
}
?>
