<?php
session_start();

// Check if user is logged in and booking data exists
if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_data'])) {
    header("Location: login/login.php");
    exit;
}

$booking_data = $_SESSION['booking_data'];

// Check booking status in DB
include 'config.php';
$booking_id = $booking_data['booking_id'];
$stmt = $conn->prepare("SELECT status FROM bookings WHERE booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$stmt->bind_result($status);
$stmt->fetch();
$stmt->close();

if ($status !== 'confirmed') {
    // Not confirmed, redirect or show error
    header('Location: rooms.php');
    exit;
}

unset($_SESSION['booking_data']); // Clear booking data after reading
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - HRS</title>
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
            background-color: #E7F6F2;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #2C3333;
            padding: 1.5rem 10%;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-links {
            display: flex;
            gap: 3rem;
            list-style: none;
        }

        .nav-links a {
            color: #E7F6F2;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #A5C9CA;
        }

        .user-icon a {
            font-size: 1.5rem;
            color: #E7F6F2;
            transition: color 0.3s ease;
        }

        .user-icon a:hover {
            color: #A5C9CA;
        }

        .confirmation-section {
            margin-top: 6rem;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1;
        }

        .confirmation-card {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        .success-icon {
            color: #4CAF50;
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .confirmation-title {
            color: #2C3333;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .booking-details {
            text-align: left;
            margin: 2rem 0;
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            color: #6c757d;
            font-weight: 500;
        }

        .detail-value {
            color: #2C3333;
            font-weight: 600;
        }

        .total-price {
            font-size: 1.25rem;
            color: #2C3333;
            font-weight: 700;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #dee2e6;
        }

        .buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #2C3333;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #395B64;
        }

        .btn-secondary {
            background-color: #A5C9CA;
            color: #2C3333;
        }

        .btn-secondary:hover {
            background-color: #395B64;
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .confirmation-card {
                margin: 1rem;
                padding: 1.5rem;
            }

            .confirmation-title {
                font-size: 1.5rem;
            }

            .buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="nav-links">
                <a href="landingpage.html">Home</a>
                <a href="index.html#rooms">Rooms</a>
                <a href="index.html#services">Services</a>
                <a href="logout.php">Logout</a>
            </div>
            <div class="user-icon">
                <a href="profile.php"><i class="fas fa-user"></i></a>
            </div>
        </nav>
    </header>

    <section class="confirmation-section">
        <div class="confirmation-card">
            <i class="fas fa-check-circle success-icon"></i>
            <h1 class="confirmation-title">Booking Confirmed!</h1>
            <p>Thank you for choosing our hotel. Your booking has been successfully confirmed.</p>
            
            <div class="booking-details">
                <div class="detail-row">
                    <span class="detail-label">Booking ID:</span>
                    <span class="detail-value">#<?php echo $booking_data['booking_id']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Room Type:</span>
                    <span class="detail-value"><?php echo $booking_data['room_type']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Check-in:</span>
                    <span class="detail-value"><?php echo date('F j, Y', strtotime($booking_data['check_in'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Check-out:</span>
                    <span class="detail-value"><?php echo date('F j, Y', strtotime($booking_data['check_out'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duration:</span>
                    <span class="detail-value"><?php echo $booking_data['days']; ?> days</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Guests:</span>
                    <span class="detail-value"><?php echo $booking_data['persons']; ?> persons</span>
                </div>
                <div class="total-price">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value">Rs. <?php echo number_format($booking_data['total_price'], 2); ?></span>
                </div>
            </div>

            <div class="buttons">
                <a href="profile.php" class="btn btn-primary">View My Bookings</a>
                <a href="index.html" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </section>
</body>
</html>
