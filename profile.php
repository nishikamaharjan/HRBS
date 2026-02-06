<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit;
}

// Database connection
include 'config.php';

// Check if notification helper exists and table is available
$notifications_available = false;
$notifications = [];
$unread_count = 0;

if (file_exists('notification_helper.php')) {
    require_once 'notification_helper.php';
    // Check if notifications table exists
    try {
        $check_table = $conn->query("SHOW TABLES LIKE 'notifications'");
        if ($check_table && $check_table->num_rows > 0) {
            $notifications_available = true;
        }
    } catch (Exception $e) {
        // Table doesn't exist or error checking - continue without notifications
        $notifications_available = false;
    }
}

// Fetch user's booking details
$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC");
$query->bind_param("i", $user_id);
$query->execute();
$bookings = $query->get_result();

// Fetch user details
$user_query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user = $user_query->get_result()->fetch_assoc();

// Fetch notifications if available
if ($notifications_available && function_exists('getUserNotifications') && function_exists('getUnreadNotificationCount')) {
    try {
        $notifications = getUserNotifications($conn, $user_id, false, 10);
        $unread_count = getUnreadNotificationCount($conn, $user_id);
    } catch (Exception $e) {
        // If notifications fail, continue without them
        $notifications = [];
        $unread_count = 0;
    }
}

// Count active bookings
$active_bookings = 0;
$temp_bookings = [];
while ($row = $bookings->fetch_assoc()) {
    $temp_bookings[] = $row;
    if ($row['status'] !== 'cancelled') {
        $active_bookings++;
    }
}
$bookings_data = $temp_bookings; // Store in a new variable for later use
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - HRBS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background-color: #E7F6F2;
            min-height: 100vh;
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

        .user-icon {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-icon a {
            font-size: 1.5rem;
            color: #E7F6F2;
            transition: color 0.3s ease;
            position: relative;
        }

        .user-icon a:hover {
            color: #A5C9CA;
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .notifications-container {
            background: #ffffff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .notifications-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .notifications-header h2 {
            color: #2C3333;
            font-size: 1.5rem;
        }

        .mark-all-read-btn {
            background-color: #2C3333;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .mark-all-read-btn:hover {
            background-color: #395B64;
        }

        .notification-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #A5C9CA;
            transition: transform 0.3s ease;
            position: relative;
        }

        .notification-item:hover {
            transform: translateX(5px);
        }

        .notification-item.unread {
            background: #E7F6F2;
            border-left-color: #2C3333;
        }

        .notification-item.booking_accepted {
            border-left-color: #28a745;
        }

        .notification-item.booking_rejected {
            border-left-color: #dc3545;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.5rem;
        }

        .notification-title {
            color: #2C3333;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .notification-time {
            color: #666;
            font-size: 0.85rem;
        }

        .notification-message {
            color: #395B64;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }

        .notification-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .notification-action-btn {
            background-color: transparent;
            border: 1px solid #A5C9CA;
            color: #2C3333;
            padding: 0.3rem 0.8rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .notification-action-btn:hover {
            background-color: #A5C9CA;
            color: white;
        }

        .no-notifications {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .no-notifications i {
            font-size: 3rem;
            color: #A5C9CA;
            margin-bottom: 1rem;
        }

        .profile-section {
            margin-top: 6rem;
            padding: 2rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .profile-header {
            background: #ffffff;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 2rem;
            align-items: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background-color: #A5C9CA;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-avatar i {
            font-size: 3rem;
            color: #ffffff;
        }

        .profile-info h1 {
            color: #2C3333;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        .profile-info p {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .stat-card h3 {
            color: #2C3333;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            color: #666;
            font-size: 1rem;
        }

        .bookings-container {
            background: #ffffff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .bookings-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .bookings-header h2 {
            color: #2C3333;
            font-size: 1.5rem;
        }

        .booking-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-5px);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .booking-type {
            color: #2C3333;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .booking-date {
            color: #666;
            font-size: 0.9rem;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            color: #2C3333;
            font-weight: 500;
        }

        .total-price {
            color: #2C3333;
            font-weight: 700;
            font-size: 1.2rem;
            margin-top: 1rem;
            text-align: right;
        }

        .no-bookings {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .no-bookings i {
            font-size: 3rem;
            color: #A5C9CA;
            margin-bottom: 1rem;
        }

        .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            background-color: #2C3333;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            margin-top: 2rem;
        }

        .logout-btn:hover {
            background-color: #395B64;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .cancel-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .cancel-btn:hover {
            background-color: #c82333;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .booking-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
        }

        @media (max-width: 768px) {
            .profile-header {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .profile-avatar {
                margin: 0 auto;
            }

            .booking-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="nav-links">
                <a href="index.html">Home</a>
                <a href="index.html#rooms">Rooms</a>
            </div>
            <div class="user-icon">
                <?php if ($notifications_available): ?>
                    <a href="profile.php#notifications" class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <?php if ($unread_count > 0): ?>
                            <span class="notification-badge"><?php echo $unread_count > 9 ? '9+' : $unread_count; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <a href="profile.php"><i class="fas fa-user"></i></a>
            </div>
        </nav>
    </header>

    <section class="profile-section">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['phone_number']); ?></p>
                <p><i class="fas fa-calendar"></i> Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>

        <div class="profile-stats">
            <div class="stat-card">
                <h3><?php echo $active_bookings; ?></h3>
                <p>Active Bookings</p>
            </div>
            <div class="stat-card">
                <h3><?php 
                    $total_spent = 0;
                    foreach ($bookings_data as $booking) {
                        if ($booking['status'] !== 'cancelled') {
                            $total_spent += $booking['total_price'];
                        }
                    }
                    echo 'Rs. ' . number_format($total_spent);
                ?></h3>
                <p>Total Spent</p>
            </div>
        </div>

        <?php if ($notifications_available): ?>
        <div class="notifications-container" id="notifications">
            <div class="notifications-header">
                <h2><i class="fas fa-bell"></i> Notifications</h2>
                <?php if ($unread_count > 0): ?>
                    <form action="mark_notifications_read.php" method="POST" style="display: inline;">
                        <button type="submit" class="mark-all-read-btn">
                            <i class="fas fa-check-double"></i> Mark All as Read
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo $notification['is_read'] == 0 ? 'unread' : ''; ?> <?php echo $notification['type']; ?>">
                        <div class="notification-header">
                            <div class="notification-title">
                                <?php echo htmlspecialchars($notification['title']); ?>
                            </div>
                            <div class="notification-time">
                                <?php 
                                    $time_ago = time() - strtotime($notification['created_at']);
                                    if ($time_ago < 60) {
                                        echo 'Just now';
                                    } elseif ($time_ago < 3600) {
                                        echo floor($time_ago / 60) . ' minutes ago';
                                    } elseif ($time_ago < 86400) {
                                        echo floor($time_ago / 3600) . ' hours ago';
                                    } else {
                                        echo date('M j, Y g:i A', strtotime($notification['created_at']));
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="notification-message">
                            <?php echo nl2br(htmlspecialchars($notification['message'])); ?>
                        </div>
                        <?php if ($notification['is_read'] == 0): ?>
                            <div class="notification-actions">
                                <form action="mark_notification_read.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                                    <button type="submit" class="notification-action-btn">
                                        <i class="fas fa-check"></i> Mark as Read
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <p>No notifications yet.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="bookings-container">
            <div class="bookings-header">
                <h2>Your Bookings</h2>
            </div>

            <?php if (!empty($bookings_data)): ?>
                <?php foreach ($bookings_data as $booking): ?>
                    <div class="booking-card">
                        <div class="booking-header">
                            <span class="booking-type"><?php echo htmlspecialchars($booking['room_type']); ?></span>
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </div>
                        <div class="booking-details">
                            <div class="detail-item">
                                <span class="detail-label">Booking ID</span>
                                <span class="detail-value">#<?php echo $booking['booking_id']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Booking Date</span>
                                <span class="detail-value"><?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Duration</span>
                                <span class="detail-value"><?php echo $booking['days']; ?> days</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Guests</span>
                                <span class="detail-value"><?php echo $booking['persons']; ?> persons</span>
                            </div>
                        </div>
                        <div class="booking-actions">
                            <div class="total-price">
                                Total: Rs. <?php echo number_format($booking['total_price'], 2); ?>
                            </div>
                            <?php if ($booking['status'] === 'pending'): ?>
                                <form action="cancel_booking.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                    <button type="submit" class="cancel-btn" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                        <i class="fas fa-times"></i> Cancel Booking
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-bookings">
                    <i class="fas fa-calendar-times"></i>
                    <p>You haven't made any bookings yet.</p>
                    <a href="rooms.php" class="logout-btn">Book a Room</a>
                </div>
            <?php endif; ?>
        </div>

        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </section>
</body>
</html>
