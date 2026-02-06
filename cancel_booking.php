<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Verify the booking belongs to the user and is pending
        $stmt = $conn->prepare("SELECT status FROM bookings WHERE booking_id = ? AND user_id = ? AND status = 'pending'");
        $stmt->bind_param("ii", $booking_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Invalid booking or booking cannot be cancelled.");
        }

        // Update booking status to cancelled
        $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE booking_id = ?");
        $stmt->bind_param("i", $booking_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error cancelling booking.");
        }

        $_SESSION['success_message'] = "Booking cancelled successfully!";
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
}

header("Location: profile.php");
exit;
?>
