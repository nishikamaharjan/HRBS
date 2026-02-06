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
        
        // Update booking status to cancelled
        $updateQuery = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE booking_id = ?");
        $updateQuery->bind_param("i", $booking_id);
        
        if ($updateQuery->execute()) {
            // Create notification for the user
            $title = "Booking Rejected";
            $message = "We regret to inform you that your booking for {$room_type} (Booking ID: #{$booking_id}) has been rejected. Please contact us if you have any questions or would like to make another booking.";
            createNotification($conn, $user_id, $title, $message, 'booking_rejected', $booking_id);
            
            $_SESSION['message'] = "Booking rejected successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error rejecting booking: " . $conn->error;
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
