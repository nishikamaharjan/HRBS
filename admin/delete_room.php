<?php
session_start();
include '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Check if room has active bookings
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM bookings 
        WHERE room_id = ? 
        AND status IN ('pending', 'confirmed')
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking_check = $result->fetch_assoc();
    
    if ($booking_check['count'] > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete room with active bookings. Please cancel or complete all bookings first.'
        ]);
        $stmt->close();
        exit;
    }
    
    // Delete the room
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Room deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting room: ' . $conn->error
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
