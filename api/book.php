<?php
/**
 * Booking API Endpoint
 * Creates a new booking
 * POST /api/book.php
 * Body: JSON with room_id, check_in, check_out, guests, user_name, user_email, user_phone, special_requests
 */

session_start();
header('Content-Type: application/json');
include_once('../config.php');

try {
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests are allowed');
    }
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        // Try form data as fallback
        $input = $_POST;
    }
    
    // Validate required fields
    $required_fields = ['room_id', 'check_in', 'check_out', 'guests'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $room_id = intval($input['room_id']);
    $check_in = $input['check_in'];
    $check_out = $input['check_out'];
    $guests = intval($input['guests']);
    
    // Validate dates
    $checkin_date = new DateTime($check_in);
    $checkout_date = new DateTime($check_out);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    
    if ($checkin_date < $today) {
        throw new Exception('Check-in date cannot be in the past');
    }
    
    if ($checkout_date <= $checkin_date) {
        throw new Exception('Check-out date must be after check-in date');
    }
    
    // Calculate number of days
    $interval = $checkin_date->diff($checkout_date);
    $days = $interval->days;
    
    if ($days < 1) {
        throw new Exception('Minimum booking is 1 night');
    }
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('You must be logged in to make a booking');
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Fetch room details
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ? AND available = 1");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    
    if (!$room) {
        throw new Exception('Room not found or not available');
    }
    
    // Check if room is available for the selected dates
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM bookings 
        WHERE room_id = ? 
        AND status != 'cancelled'
        AND booking_date < ? 
        AND DATE_ADD(booking_date, INTERVAL days DAY) > ?
    ");
    $stmt->bind_param("iss", $room_id, $check_out, $check_in);
    $stmt->execute();
    $result = $stmt->get_result();
    $availability = $result->fetch_assoc();
    
    if ($availability['count'] > 0) {
        throw new Exception('Room is not available for the selected dates');
    }
    
    // Calculate total price
    $total_price = $room['price'] * $days;
    
    // Insert booking
    $stmt = $conn->prepare("
        INSERT INTO bookings (user_id, room_id, room_type, days, persons, booking_date, total_price, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->bind_param("iisiiss", $user_id, $room_id, $room['room_type'], $days, $guests, $check_in, $total_price);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create booking: ' . $stmt->error);
    }
    
    $booking_id = $conn->insert_id;

    // Mark this room as no longer available
    $updateRoom = $conn->prepare("UPDATE rooms SET available = 0 WHERE id = ?");
    $updateRoom->bind_param("i", $room_id);
    if (!$updateRoom->execute()) {
        throw new Exception('Booking created but failed to update room availability: ' . $updateRoom->error);
    }
    $updateRoom->close();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'booking_id' => $booking_id,
        'message' => 'Booking created successfully',
        'booking' => [
            'booking_id' => $booking_id,
            'room_type' => $room['room_type'],
            'check_in' => $check_in,
            'check_out' => $check_out,
            'days' => $days,
            'guests' => $guests,
            'total_price' => $total_price,
            'status' => 'pending'
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
