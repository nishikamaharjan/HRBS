<?php
session_start();
include '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['available'])) {
    $id = intval($_POST['id']);
    $available = intval($_POST['available']);
    
    // Validate available value (should be 0 or 1)
    if ($available !== 0 && $available !== 1) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid availability value'
        ]);
        exit;
    }
    
    $stmt = $conn->prepare("UPDATE rooms SET available = ? WHERE id = ?");
    $stmt->bind_param("ii", $available, $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Room availability updated successfully',
            'available' => $available
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error updating availability: ' . $conn->error
        ]);
    }
    
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>
