<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit;
}

// Database connection
include 'config.php';
require_once 'includes/DynamicPricing.php';

    if (!isset($_POST['room_id'], $_POST['check_in'], $_POST['check_out'], $_POST['guests'])) {
        $_SESSION['error_message'] = 'Missing required booking details';
        header("Location: rooms.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $room_id = (int)$_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $persons = (int)$_POST['guests'];

    // Enforce single active booking per user
    try {
        $activeStmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM bookings WHERE user_id = ? AND status IN ('pending','confirmed')");
        $activeStmt->bind_param("i", $user_id);
        $activeStmt->execute();
        $activeResult = $activeStmt->get_result();
        $activeRow = $activeResult->fetch_assoc();
        $activeStmt->close();

        if ($activeRow && (int)$activeRow['cnt'] > 0) {
            $_SESSION['error_message'] = 'You already have an active booking which is Pending or Confirmed. Please check your profile.';
            header("Location: rooms.php");
            exit;
        }
    } catch (Exception $e) {
        // Log error but maybe proceed? No, safer to fail.
        error_log("Active booking check failed: " . $e->getMessage());
        $_SESSION['error_message'] = 'System error checking existing bookings.';
        header("Location: rooms.php");
        exit;
    }

    // Validate dates
    try {
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        
        if ($check_in_date < $today) {
            throw new Exception("Check-in date cannot be in the past");
        }
        
        if ($check_out_date <= $check_in_date) {
             throw new Exception("Check-out date must be after check-in date");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: rooms.php");
        exit;
    }

    // Validate Room and Fetch Details (Capacity check added)
    $room_type_name = '';
    $db_base_price = 0;
    
    try {
        $stmt = $conn->prepare("SELECT room_type, price, available, capacity FROM rooms WHERE id = ?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if (!$row['available']) {
                 throw new Exception("Room is currently unavailable.");
            }
            if ($persons > $row['capacity']) {
                throw new Exception("Number of guests ($persons) exceeds room capacity (" . $row['capacity'] . ")");
            }

            $room_type_name = $row['room_type'];
            $db_base_price = (float)$row['price'];
        } else {
             throw new Exception("Invalid Room Selected.");
        }
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: rooms.php");
        exit;
    }

    // Calculate dynamic pricing using DynamicPricing algorithm
    try {
        $pricing = new DynamicPricing();
        
        // Calculate price using the new engine
        $price_data = $pricing->calculateTotal((float)$db_base_price, $check_in, $check_out);
        
        // Debug logging as requested
        error_log("Dynamic pricing applied: " . json_encode($price_data));

        $days        = $price_data['nights'];
        $total_price = $price_data['final_total'];
        
        // Calculate average for display compatibility (avoid division by zero if days=0, though caught below)
        $average_price_per_night = $days > 0 ? round($total_price / $days, 2) : 0.0;

        if ($days <= 0) {
            $_SESSION['error_message'] = 'Invalid stay duration.';
            header("Location: rooms.php");
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Error calculating price: ' . $e->getMessage();
        header("Location: rooms.php");
        exit;
    }

    // INSERT logic audit: Including all verified columns
    try {
        // Insert booking
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, room_type, days, persons, booking_date, check_in, check_out, total_price, status) VALUES (?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, 'pending')");
        if (!$stmt) {
             die("SQL PREPARE ERROR: " . $conn->error);
        }
        
        // Types: i (int), i (int), s (string), i (int), i (int), s (string), s (string), d (double)
        $stmt->bind_param("iisiissd", $user_id, $room_id, $room_type_name, $days, $persons, $check_in, $check_out, $total_price);
        
        if (!$stmt->execute()) {
            die("SQL ERROR: " . $stmt->error);
        }

        $booking_id = $stmt->insert_id;
        
        if ($booking_id == 0) {
            die("INSERT FAILED: No ID returned. Check auto_increment setting on bookings table.");
        }
        
        error_log("Booking inserted into DB: hrs. Booking ID: " . $booking_id);

        // Set success data with dynamic pricing information
        $_SESSION['booking_data'] = [
            'booking_id'              => $booking_id,
            'room_type'               => $room_type_name,
            'check_in'                => $check_in,
            'check_out'               => $check_out,
            'days'                    => $days,
            'persons'                 => $persons,
            'total_price'             => $total_price,
            'average_price_per_night' => $average_price_per_night,
            'base_price'              => $db_base_price,
            'price_breakdown'         => $price_data // full algorithm details
        ];
        
        header("Location: khalti_payment.php");
        exit;

    } catch (Exception $e) {
        die("SYSTEM ERROR: " . $e->getMessage());
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Booking - HRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #E7F6F2;
            padding: 2rem;
        }

        .processing-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .icon {
            font-size: 3rem;
            color: #2C3333;
            margin-bottom: 1rem;
        }

        h1 {
            color: #2C3333;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        p {
            color: #395B64;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .spinner {
            border: 4px solid rgba(165, 201, 202, 0.3);
            border-radius: 50%;
            border-top: 4px solid #2C3333;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 1rem auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            color: #dc3545;
            margin-top: 1rem;
            padding: 1rem;
            background-color: #fff5f5;
            border-radius: 4px;
        }

        .back-link {
            color: #395B64;
            text-decoration: none;
            margin-top: 1rem;
            display: inline-block;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #2C3333;
        }
    </style>
</head>
<body>
    <div class="processing-container">
        <?php if (isset($_SESSION['error_message'])): ?>
            <i class="fas fa-exclamation-circle icon"></i>
            <h1>Booking Error</h1>
            <p class="error-message"><?php echo $_SESSION['error_message']; ?></p>
            <a href="rooms.php" class="back-link">← Back to Rooms</a>
            <?php unset($_SESSION['error_message']); ?>
        <?php else: ?>
            <div class="spinner"></div>
            <h1>Processing Your Booking</h1>
            <p>Please wait while we process your reservation...</p>
        <?php endif; ?>
    </div>
</body>
</html>
