<?php
session_start();
include_once('../config.php');

// Require login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php?redirect=customer/my-bookings.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's bookings
$stmt = $conn->prepare("
    SELECT b.*, r.room_type, r.price, r.image_url 
    FROM bookings b 
    LEFT JOIN rooms r ON b.room_id = r.id 
    WHERE b.user_id = ? 
    ORDER BY b.booking_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View and manage your hotel bookings">
    <title>My Bookings - HRS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            padding-top: 70px;
        }
        
        .bookings-page {
            padding: 2rem 0;
        }
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: var(--font-size-3xl);
            color: var(--color-text-dark);
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: var(--color-text-medium);
        }
        
        /* Booking Card */
        .booking-card {
            background-color: var(--color-white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(165, 201, 202, 0.2);
            transition: all var(--transition-base);
        }
        
        .booking-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--color-accent);
        }
        
        .booking-card-content {
            display: grid;
            grid-template-columns: 200px 1fr auto;
            gap: 1.5rem;
            padding: 1.5rem;
        }
        
        .booking-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: var(--radius-md);
        }
        
        .booking-details {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
        }
        
        .booking-title {
            font-size: var(--font-size-xl);
            color: var(--color-text-dark);
            margin-bottom: 0.25rem;
        }
        
        .booking-id {
            font-size: var(--font-size-sm);
            color: var(--color-text-medium);
        }
        
        .booking-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 0.75rem;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: var(--font-size-xs);
            color: var(--color-text-medium);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-size: var(--font-size-base);
            color: var(--color-text-dark);
            font-weight: 500;
        }
        
        .booking-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-end;
        }
        
        .booking-price {
            font-size: var(--font-size-2xl);
            font-weight: 700;
            color: var(--color-primary);
        }
        
        /* Status Badge */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            font-size: var(--font-size-sm);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background-color: var(--color-warning);
            color: white;
        }
        
        .status-confirmed {
            background-color: var(--color-success);
            color: white;
        }
        
        .status-cancelled {
            background-color: var(--color-error);
            color: white;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background-color: var(--color-white);
            border-radius: var(--radius-lg);
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--color-accent);
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            font-size: var(--font-size-2xl);
            color: var(--color-text-dark);
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: var(--color-text-medium);
            margin-bottom: 2rem;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .booking-card-content {
                grid-template-columns: 150px 1fr;
            }
            
            .booking-actions {
                grid-column: 1 / -1;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding-top: 1rem;
                border-top: 1px solid var(--color-light);
            }
        }
        
        @media (max-width: 768px) {
            .booking-card-content {
                grid-template-columns: 1fr;
            }
            
            .booking-image {
                height: 200px;
            }
            
            .booking-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header/Navigation -->
    <header class="header">
        <nav class="nav container">
            <a href="index.php" class="nav-brand">HRS</a>
            
            <button class="nav-toggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="rooms.php" class="nav-link">Rooms</a></li>
                <li><a href="../about_us.php" class="nav-link">About</a></li>
                <li><a href="../contact.php" class="nav-link">Contact</a></li>
                <li class="nav-user">
                    <a href="my-bookings.php" class="nav-link active">My Bookings</a>
                    <a href="../profile.php" class="nav-user-icon" title="Profile">
                        <i class="fas fa-user"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="container bookings-page">
        <div class="page-header">
            <h1 class="page-title">My Bookings</h1>
            <p class="page-subtitle">View and manage your hotel reservations</p>
        </div>
        
        <?php if (count($bookings) > 0): ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-card-content">
                        <img src="../<?php echo htmlspecialchars($booking['image_url'] ?: 'img/hotel-normal.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($booking['room_type']); ?>" 
                             class="booking-image">
                        
                        <div class="booking-details">
                            <div>
                                <div class="booking-header">
                                    <div>
                                        <h3 class="booking-title"><?php echo htmlspecialchars($booking['room_type']); ?></h3>
                                        <p class="booking-id">Booking ID: #<?php echo $booking['booking_id']; ?></p>
                                    </div>
                                </div>
                                
                                <div class="booking-info-grid">
                                    <div class="info-item">
                                        <span class="info-label">Booking Date</span>
                                        <span class="info-value">
                                            <i class="fas fa-calendar"></i> 
                                            <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Duration</span>
                                        <span class="info-value">
                                            <i class="fas fa-moon"></i> 
                                            <?php echo $booking['days']; ?> night<?php echo $booking['days'] > 1 ? 's' : ''; ?>
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Guests</span>
                                        <span class="info-value">
                                            <i class="fas fa-users"></i> 
                                            <?php echo $booking['persons']; ?> guest<?php echo $booking['persons'] > 1 ? 's' : ''; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="booking-actions">
                            <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                            <div class="booking-price">
                                Rs.<?php echo number_format($booking['total_price'], 0); ?>
                            </div>
                            <?php if ($booking['status'] == 'pending'): ?>
                                <button onclick="cancelBooking(<?php echo $booking['booking_id']; ?>)" 
                                        class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>No Bookings Yet</h3>
                <p>You haven't made any bookings. Start exploring our rooms!</p>
                <a href="rooms.php" class="btn btn-primary btn-lg">Browse Rooms</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer mt-xl">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h4>About HRS</h4>
                    <p style="color: var(--color-light); line-height: 1.8;">
                        Your trusted partner for comfortable stays.
                    </p>
                </div>
                <div class="footer-column">
                    <h4>Quick Links</h4>
                    <a href="../about_us.php" class="footer-link">About Us</a>
                    <a href="rooms.php" class="footer-link">Rooms</a>
                    <a href="../contact.php" class="footer-link">Contact Us</a>
                </div>
                <div class="footer-column">
                    <h4>Contact</h4>
                    <a href="mailto:info@hrbs.com" class="footer-link">
                        <i class="fas fa-envelope"></i> info@hrbs.com
                    </a>
                    <a href="tel:+9771234567890" class="footer-link">
                        <i class="fas fa-phone"></i> +977 1234567890
                    </a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Hotel Reservation System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
    <script>
        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                // Send cancel request
                fetch('../cancel_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `booking_id=${bookingId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        HRS.showToast('Booking cancelled successfully', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        HRS.showToast(data.message || 'Failed to cancel booking', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    HRS.showToast('An error occurred', 'error');
                });
            }
        }
    </script>
</body>
</html>
