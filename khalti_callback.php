<?php
// khalti_callback.php
session_start();
require_once 'config.php';
require_once 'khalti_config.php';

// Log incoming parameters for debugging
error_log("Khalti Callback: " . json_encode($_GET));

$pidx = $_GET['pidx'] ?? null;
$status = $_GET['status'] ?? null;
$transaction_id = $_GET['transaction_id'] ?? null;
$purchase_order_id = $_GET['purchase_order_id'] ?? null;

if (!$pidx) {
    error_log("Khalti Callback Error: Missing pidx");
    header("Location: payment_failed.html");
    exit;
}

// 1. Verify Payment with Khalti Lookup API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => KHALTI_BASE_URL . 'lookup/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode(['pidx' => $pidx]),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Key ' . KHALTI_SECRET_KEY,
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    error_log("Khalti Verification Curl Error: " . $err);
    header("Location: payment_failed.html");
    exit;
}

$verification_data = json_decode($response, true);
error_log("Khalti Verification Response: " . $response);

$verified_status = $verification_data['status'] ?? 'Unknown';

// Extract booking ID
$booking_id = 0;
// Expecting format "BOOKING-123"
if (preg_match('/BOOKING-(\d+)/', $purchase_order_id, $matches)) {
    $booking_id = intval($matches[1]);
}

if ($booking_id <= 0) {
    error_log("Khalti Callback Error: Invalid booking ID from purchase_order_id: $purchase_order_id");
    header("Location: payment_failed.html");
    exit;
}

// 2. Update Database
if ($verified_status === 'Completed') {
    // Payment Success
    $stmt = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        header("Location: payment_success.html");
        exit;
    } else {
        error_log("Database Update Error: " . $conn->error);
        header("Location: payment_failed.html");
        exit;
    }
} else {
    // Payment Failed / Cancelled / Expired
    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    
    header("Location: payment_failed.html");
    exit;
}
?>
