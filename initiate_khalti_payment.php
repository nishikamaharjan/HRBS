<?php
// initiate_khalti_payment.php
session_start();
header('Content-Type: application/json');

// Include configurations
require_once 'config.php';
require_once 'khalti_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['booking_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit;
}

$booking_id = intval($input['booking_id']);
$user_id = $_SESSION['user_id'];

// 1. Validate Booking and Get Amount from Database (Critical Security Step)
// We DO NOT trust any amount sent from frontend
$stmt = $conn->prepare("SELECT total_price, status FROM bookings WHERE booking_id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Booking not found or invalid access']);
    exit;
}

$booking = $result->fetch_assoc();
$stmt->close();

// Check if already paid
if ($booking['status'] === 'confirmed') {
    echo json_encode(['success' => false, 'message' => 'Booking is already confirmed']);
    exit;
}

// Prepare Data for Khalti
$amount_rs = $booking['total_price'];
$amount_paisa = $amount_rs * 100; // Convert to Paisa (Khalti v2 expects Paisa)

// User Info (Fetching from DB for completeness)
$user_stmt = $conn->prepare("SELECT full_name, email, phone_number FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_res = $user_stmt->get_result();
$user = $user_res->fetch_assoc();
$user_stmt->close();

$purchase_order_id = "BOOKING-" . $booking_id;
$purchase_order_name = "Room Booking #" . $booking_id;
$return_url = "http://localhost/Projectroombooking/khalti_callback.php";
$website_url = "http://localhost/Projectroombooking/";

$payload = [
    "return_url" => $return_url,
    "website_url" => $website_url,
    "amount" => $amount_paisa,
    "purchase_order_id" => $purchase_order_id,
    "purchase_order_name" => $purchase_order_name,
    "customer_info" => [
        "name" => $user['full_name'] ?? 'Guest',
        "email" => $user['email'] ?? 'test@example.com',
        "phone" => $user['phone_number'] ?? '9800000000'
    ]
];

// 2. Call Khalti API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => KHALTI_BASE_URL . 'initiate/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Key ' . KHALTI_SECRET_KEY,
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($err) {
    error_log("Khalti Curl Error: " . $err);
    echo json_encode(['success' => false, 'message' => 'Payment initiation failed (Network Error)']);
    exit;
}

$response_data = json_decode($response, true);

// 3. Handle Response
if ($http_code == 200 && isset($response_data['payment_url'])) {
    echo json_encode([
        'success' => true,
        'payment_url' => $response_data['payment_url'],
        'pidx' => $response_data['pidx']
    ]);
} else {
    error_log("Khalti API Error: " . $response);
    echo json_encode([
        'success' => false, 
        'message' => $response_data['detail'] ?? 'Payment initiation failed from Gateway'
    ]);
}
?>
