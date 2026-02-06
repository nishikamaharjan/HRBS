<?php
session_start();
include '../config.php';

// Handle GET request - fetch room data for editing
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($room = $result->fetch_assoc()) {
        header('Content-Type: application/json');
        echo json_encode($room);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Room not found']);
    }
    
    $stmt->close();
    exit;
}

// Handle POST request - update room data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $room_number = $_POST['room_number'] ?? null;
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];
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

    // Get current room data
    $stmt = $conn->prepare("SELECT image_url FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_room = $result->fetch_assoc();
    $stmt->close();

    $image_url = $current_room['image_url']; // Keep existing image by default

    // Handle new image upload
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
            // Delete old image if it exists and is in uploads folder
            if (!empty($current_room['image_url']) && strpos($current_room['image_url'], 'uploads/rooms/') === 0) {
                $old_image_path = '../' . $current_room['image_url'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            $image_url = 'uploads/rooms/' . $unique_filename;
        } else {
            $_SESSION['message'] = "Failed to upload image.";
            $_SESSION['message_type'] = "error";
            header("Location: room_management.php");
            exit;
        }
    }

    // Update room in database
    $stmt = $conn->prepare("UPDATE rooms SET room_number = ?, room_type = ?, price = ?, capacity = ?, image_url = ?, description = ?, available = ? WHERE id = ?");
    $stmt->bind_param("ssdissii", $room_number, $room_type, $price, $capacity, $image_url, $description, $available, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Room updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating room: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }

    $stmt->close();
    header("Location: room_management.php");
    exit;
}
?>
