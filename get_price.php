<?php
/**
 * API endpoint to get dynamic pricing for room bookings
 * Returns JSON response with full price breakdown from DynamicPricing.
 */

header('Content-Type: application/json');

require_once 'config.php';
require_once 'includes/DynamicPricing.php';

// Check if required parameters are provided
if (!isset($_GET['room_type']) || !isset($_GET['check_in']) || !isset($_GET['check_out'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => 'Missing required parameters: room_type, check_in, check_out'
    ]);
    exit;
}

$room_type = $_GET['room_type'];
$check_in  = $_GET['check_in'];
$check_out = $_GET['check_out'];

// Validate dates and calculate dynamic pricing
try {
    $check_in_date  = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    
    // Validate date range
    if ($check_in_date < $today) {
        throw new Exception('Check-in date cannot be in the past');
    }
    
    if ($check_out_date <= $check_in_date) {
        throw new Exception('Check-out date must be after check-in date');
    }

    // Fetch base price from database
    $stmt = $conn->prepare("SELECT price FROM rooms WHERE room_type = ?");
    $stmt->bind_param("s", $room_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $db_base_price = null;
    
    if ($row = $result->fetch_assoc()) {
        $db_base_price = (float)$row['price'];
    } else {
        throw new Exception('Room type not found');
    }
    $stmt->close();

    // Use NEW DynamicPricing algorithm with calculateTotal
    $pricing = new DynamicPricing();
    $price_data = $pricing->calculateTotal($db_base_price, $check_in, $check_out);
    
    // Add base_price to response for frontend display
    $price_data['base_price'] = $db_base_price;
    $price_data['average_price_per_night'] = round($price_data['final_total'] / $price_data['nights'], 2);
    $price_data['total_price'] = $price_data['final_total'];

    echo json_encode([
        'success' => true,
        'data'    => $price_data
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}

?>