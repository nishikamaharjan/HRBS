<?php
/**
 * Rooms API Endpoint
 * Returns available rooms based on filters
 * GET /api/rooms.php?checkin=YYYY-MM-DD&checkout=YYYY-MM-DD&guests=N&room_type=type&min_price=X&max_price=Y
 */

header('Content-Type: application/json');
include_once('../config.php');

try {
    // Get filter parameters
    $checkin = isset($_GET['checkin']) ? $_GET['checkin'] : '';
    $checkout = isset($_GET['checkout']) ? $_GET['checkout'] : '';
    $guests = isset($_GET['guests']) ? intval($_GET['guests']) : 0;
    $room_type = isset($_GET['room_type']) ? $_GET['room_type'] : '';
    $min_price = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
    $max_price = isset($_GET['max_price']) ? intval($_GET['max_price']) : 999999;
    
    // Build base query
    $query = "SELECT * FROM rooms WHERE available = 1";
    $params = [];
    $types = "";
    
    // Apply filters
    if ($room_type) {
        $query .= " AND room_type LIKE ?";
        $params[] = "%$room_type%";
        $types .= "s";
    }
    
    if ($min_price > 0) {
        $query .= " AND price >= ?";
        $params[] = $min_price;
        $types .= "i";
    }
    
    if ($max_price < 999999) {
        $query .= " AND price <= ?";
        $params[] = $max_price;
        $types .= "i";
    }
    
    // Check availability if dates provided
    if ($checkin && $checkout) {
        // Validate dates
        $checkin_date = new DateTime($checkin);
        $checkout_date = new DateTime($checkout);
        
        if ($checkout_date <= $checkin_date) {
            throw new Exception('Check-out date must be after check-in date');
        }
        
        // Exclude rooms that are already booked for the requested dates
        // A room is unavailable if there's a booking where:
        // - booking check-in < requested check-out AND
        // - booking check-out > requested check-in
        $query .= " AND id NOT IN (
            SELECT DISTINCT room_id FROM bookings 
            WHERE status != 'cancelled' 
            AND booking_date < ? 
            AND DATE_ADD(booking_date, INTERVAL days DAY) > ?
        )";
        $params[] = $checkout;
        $params[] = $checkin;
        $types .= "ss";
    }
    
    $query .= " ORDER BY price ASC";
    
    // Execute query
    $stmt = $conn->prepare($query);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch rooms
    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        // Add calculated fields
        $row['capacity'] = 2; // Default capacity, can be enhanced
        $row['facilities'] = ['WiFi', 'AC', 'TV', 'Bed']; // Default facilities
        
        // Determine capacity based on room type
        if (stripos($row['room_type'], 'suite') !== false) {
            $row['capacity'] = 4;
            $row['facilities'] = ['WiFi', 'AC', 'Smart TV', 'Living Area', 'Mini Kitchen'];
        } elseif (stripos($row['room_type'], 'family') !== false) {
            $row['capacity'] = 5;
            $row['facilities'] = ['WiFi', 'AC', 'TV', 'Multiple Beds'];
        } elseif (stripos($row['room_type'], 'single') !== false) {
            $row['capacity'] = 1;
            $row['facilities'] = ['WiFi', 'AC', 'TV', 'Single Bed'];
        }
        
        // Filter by guest capacity if specified
        if ($guests > 0 && $row['capacity'] < $guests) {
            continue;
        }
        
        $rooms[] = $row;
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'count' => count($rooms),
        'rooms' => $rooms,
        'filters' => [
            'checkin' => $checkin,
            'checkout' => $checkout,
            'guests' => $guests,
            'room_type' => $room_type,
            'min_price' => $min_price,
            'max_price' => $max_price
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
