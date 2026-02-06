<?php
session_start();
include '../config.php';
require_once '../notification_helper.php';

// Check if user is logged in and is admin
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
//     header("Location: ../login/login.php");
//     exit;
// }

if (isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
    
    // Check if booking exists and is pending
    $checkQuery = $conn->prepare("SELECT * FROM bookings WHERE booking_id = ? AND status = 'pending'");
    $checkQuery->bind_param("i", $booking_id);
    $checkQuery->execute();
    $result = $checkQuery->get_result();
    
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        $user_id = $booking['user_id'];
        $room_type = $booking['room_type'];
        
        // Update booking status to confirmed
        $updateQuery = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE booking_id = ?");
        $updateQuery->bind_param("i", $booking_id);
        
        if ($updateQuery->execute()) {
            // Create notification for the user
            $title = "Booking Accepted!";
            $message = "Great news! Your booking for {$room_type} (Booking ID: #{$booking_id}) has been accepted and confirmed. We look forward to hosting you!";
            createNotification($conn, $user_id, $title, $message, 'booking_accepted', $booking_id);
            
            $_SESSION['message'] = "Booking accepted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error accepting booking: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
        
        $updateQuery->close();
    } else {
        $_SESSION['message'] = "Booking not found or already processed!";
        $_SESSION['message_type'] = "error";
    }
    
    $checkQuery->close();
} else {
    $_SESSION['message'] = "Invalid booking ID!";
    $_SESSION['message_type'] = "error";
}

header("Location: manage_bookings.php");
exit;
?>
