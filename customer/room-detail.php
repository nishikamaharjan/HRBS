<?php
session_start();
include_once('../config.php');

// Get room ID from URL
$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$checkin = isset($_GET['checkin']) ? $_GET['checkin'] : '';
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : '';

if ($room_id == 0) {
    header('Location: rooms.php');
    exit;
}

// Fetch room details
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

if (!$room) {
    header('Location: rooms.php');
    exit;
}

// Define room images based on type
$room_images = [
    'normal' => ['img/hotel-normal.jpg', 'img/normal room1.jpg', 'img/normal room2.jpg', 'img/normal room3.avif'],
    'deluxe' => ['img/hotel-deluxe.jpg', 'img/deluxe room1.jpg', 'img/deluxe room2.jpeg', 'img/deluxe-room3.jpeg'],
    'suite' => ['img/hotel-suite.jpg', 'img/suite room1.jpg', 'img/Hotel-suite-living-room.jpg', 'img/suiteroom2.jpg'],
    'luxury' => ['img/hotel-deluxe.jpg', 'img/deluxe room1.jpg', 'img/deluxe room2.jpeg', 'img/deluxe room4.jpg'],
    'premium' => ['img/hotel-suite.jpg', 'img/premium1.jpg', 'img/premium room.jpg', 'img/premium3.jpg'],
    'executive' => ['img/hotel-deluxe.jpg', 'img/Executive1.jpg', 'img/Executive2.jpg', 'img/Executive3.webp'],
    'family' => ['img/hotel-normal.jpg', 'img/Family1.jpg', 'img/Family2.jpg', 'img/Family3.jpg'],
    'single' => ['img/hotel-normal.jpg', 'img/Single1.jpg', 'img/Single2.jpg', 'img/Single3.jpg']
];

// Get images for this room type
$room_type_key = strtolower(explode(' ', $room['room_type'])[0]);
$images = isset($room_images[$room_type_key]) ? $room_images[$room_type_key] : $room_images['normal'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($room['room_type']); ?> - Book now at HRS">
    <title><?php echo htmlspecialchars($room['room_type']); ?> - HRS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            padding-top: 70px;
        }
        
        .room-detail-page {
            padding: 2rem 0;
        }
        
        .room-detail-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
        }
        
        /* Image Gallery */
        .image-gallery-section {
            background-color: var(--color-white);
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }
        
        .main-image-container {
            margin-bottom: 1rem;
        }
        
        .main-image {
            width: 100%;
            height: 450px;
            object-fit: cover;
            border-radius: var(--radius-md);
        }
        
        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
        }
        
        .thumbnail {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all var(--transition-base);
            border: 2px solid transparent;
        }
        
        .thumbnail:hover,
        .thumbnail.active {
            border-color: var(--color-accent);
            opacity: 0.8;
        }
        
        /* Room Info */
        .room-info-section {
            background-color: var(--color-white);
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            margin-top: 2rem;
        }
        
        .room-title {
            font-size: var(--font-size-3xl);
            color: var(--color-text-dark);
            margin-bottom: 1rem;
        }
        
        .room-description {
            color: var(--color-text-medium);
            line-height: 1.8;
            margin-bottom: 2rem;
        }
        
        .amenities-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--color-light);
        }
        
        .amenities-title {
            font-size: var(--font-size-xl);
            color: var(--color-text-dark);
            margin-bottom: 1rem;
        }
        
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .amenity-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--color-text-medium);
        }
        
        .amenity-item i {
            color: var(--color-accent);
            font-size: var(--font-size-lg);
        }
        
        /* Booking Sidebar */
        .booking-sidebar {
            background-color: var(--color-white);
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            height: fit-content;
            position: sticky;
            top: 90px;
        }
        
        .price-display {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
        }
        
        .price-amount {
            font-size: var(--font-size-3xl);
            font-weight: 700;
            color: var(--color-light);
        }
        
        .price-period {
            font-size: var(--font-size-sm);
            color: var(--color-accent);
        }
        
        .total-price-display {
            background-color: var(--color-light);
            padding: 1rem;
            border-radius: var(--radius-sm);
            margin-top: 1rem;
            text-align: center;
        }
        
        .total-label {
            font-size: var(--font-size-sm);
            color: var(--color-text-medium);
            margin-bottom: 0.25rem;
        }
        
        .total-amount {
            font-size: var(--font-size-2xl);
            font-weight: 700;
            color: var(--color-primary);
        }
        
        .booking-form-section {
            margin-top: 1.5rem;
        }
        
        /* Guest Counter */
        .guest-counter {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            border: 2px solid var(--color-accent);
            border-radius: var(--radius-sm);
        }
        
        .counter-btn {
            width: 35px;
            height: 35px;
            border-radius: var(--radius-full);
            background-color: var(--color-accent);
            color: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-size-lg);
            font-weight: 700;
            transition: all var(--transition-base);
        }
        
        .counter-btn:hover {
            background-color: var(--color-secondary);
            color: var(--color-light);
        }
        
        .counter-value {
            font-size: var(--font-size-xl);
            font-weight: 600;
            color: var(--color-text-dark);
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .room-detail-grid {
                grid-template-columns: 1fr;
            }
            
            .booking-sidebar {
                position: static;
            }
        }
        
        @media (max-width: 768px) {
            .main-image {
                height: 300px;
            }
            
            .thumbnail-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .amenities-grid {
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
                <li><a href="rooms.php" class="nav-link active">Rooms</a></li>
                <li><a href="../about_us.php" class="nav-link">About</a></li>
                <li><a href="../contact.php" class="nav-link">Contact</a></li>
                <li class="nav-user">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="my-bookings.php" class="nav-link">My Bookings</a>
                        <a href="../profile.php" class="nav-user-icon" title="Profile">
                            <i class="fas fa-user"></i>
                        </a>
                    <?php else: ?>
                        <a href="../login/login.php" class="btn btn-accent btn-sm">Login / Signup</a>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="container room-detail-page">
        <div class="room-detail-grid">
            <!-- Left Column: Images and Info -->
            <div>
                <!-- Image Gallery -->
                <div class="image-gallery-section">
                    <div class="main-image-container">
                        <img src="../<?php echo $images[0]; ?>" alt="<?php echo htmlspecialchars($room['room_type']); ?>" class="main-image" id="main-image">
                    </div>
                    <div class="thumbnail-grid">
                        <?php foreach ($images as $index => $image): ?>
                            <img src="../<?php echo $image; ?>" 
                                 alt="<?php echo htmlspecialchars($room['room_type']); ?> View <?php echo $index + 1; ?>" 
                                 class="thumbnail <?php echo $index == 0 ? 'active' : ''; ?>"
                                 onclick="changeMainImage(this)">
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Room Information -->
                <div class="room-info-section">
                    <h1 class="room-title"><?php echo htmlspecialchars($room['room_type']); ?></h1>
                    <p class="room-description">
                        <?php echo htmlspecialchars($room['description'] ?: 'Experience comfort and luxury in our well-appointed room. Perfect for both business and leisure travelers, featuring modern amenities and exceptional service.'); ?>
                    </p>
                    
                    <!-- Amenities -->
                    <div class="amenities-section">
                        <h3 class="amenities-title">Room Amenities</h3>
                        <div class="amenities-grid">
                            <div class="amenity-item">
                                <i class="fas fa-wifi"></i>
                                <span>Free High-Speed WiFi</span>
                            </div>
                            <div class="amenity-item">
                                <i class="fas fa-tv"></i>
                                <span>Smart TV</span>
                            </div>
                            <div class="amenity-item">
                                <i class="fas fa-snowflake"></i>
                                <span>Air Conditioning</span>
                            </div>
                            <div class="amenity-item">
                                <i class="fas fa-bed"></i>
                                <span>Comfortable Bed</span>
                            </div>
                            <div class="amenity-item">
                                <i class="fas fa-shower"></i>
                                <span>Private Bathroom</span>
                            </div>
                            <div class="amenity-item">
                                <i class="fas fa-concierge-bell"></i>
                                <span>Room Service</span>
                            </div>
                            <div class="amenity-item">
                                <i class="fas fa-coffee"></i>
                                <span>Coffee/Tea Maker</span>
                            </div>
                            <div class="amenity-item">
                                <i class="fas fa-lock"></i>
                                <span>Safe Deposit Box</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Booking Sidebar -->
            <div>
                <div class="booking-sidebar">
                    <div class="price-display">
                        <div class="price-amount">Rs.<?php echo number_format($room['price'], 0); ?></div>
                        <div class="price-period">per night</div>
                    </div>
                    
                    <form id="booking-form" action="../process_booking.php" method="POST">
                        <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                        <input type="hidden" name="room_type" value="<?php echo htmlspecialchars($room['room_type']); ?>">
                        <input type="hidden" name="price_per_night" value="<?php echo $room['price']; ?>">
                        
                        <div class="form-group">
                            <label for="checkin" class="form-label">Check-in Date</label>
                            <input type="date" id="checkin" name="check_in" class="form-control" value="<?php echo htmlspecialchars($checkin); ?>" required>
                            <span class="form-error" id="checkin-error"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="checkout" class="form-label">Check-out Date</label>
                            <input type="date" id="checkout" name="check_out" class="form-control" value="<?php echo htmlspecialchars($checkout); ?>" required>
                            <span class="form-error" id="checkout-error"></span>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Number of Guests</label>
                            <div class="guest-counter">
                                <button type="button" class="counter-btn" onclick="decrementGuests()">-</button>
                                <span class="counter-value" id="guest-count">2</span>
                                <button type="button" class="counter-btn" onclick="incrementGuests()">+</button>
                            </div>
                            <input type="hidden" name="guests" id="guests-input" value="2">
                        </div>
                        
                        <div class="total-price-display">
                            <div class="total-label">Total Price</div>
                            <div class="total-amount" id="total-price">Rs.<?php echo number_format($room['price'], 0); ?></div>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button type="button" class="btn btn-primary btn-lg btn-block mt-lg" onclick="window.location.href='../room.php';">
                                <i class="fas fa-check-circle"></i> Book Now
                            </button>
                        <?php else: ?>
                            <a href="../login/login.php?redirect=<?php echo urlencode('customer/room-detail.php?id='.$room_id); ?>" 
                               class="btn btn-primary btn-lg btn-block mt-lg">
                                <i class="fas fa-sign-in-alt"></i> Login to Book
                            </a>
                        <?php endif; ?>
                        
                        <p class="text-center mt-sm" style="font-size: var(--font-size-sm); color: var(--color-text-medium);">
                            <i class="fas fa-shield-alt"></i> Secure booking • Free cancellation
                        </p>
                    </form>
                </div>
            </div>
        </div>
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
        const pricePerNight = <?php echo $room['price']; ?>;
        let guestCount = 2;
        
        // Change main image
        function changeMainImage(thumbnail) {
            const mainImage = document.getElementById('main-image');
            mainImage.src = thumbnail.src;
            
            // Update active thumbnail
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
        }
        
        // Guest counter functions
        function incrementGuests() {
            if (guestCount < 10) {
                guestCount++;
                updateGuestDisplay();
            }
        }
        
        function decrementGuests() {
            if (guestCount > 1) {
                guestCount--;
                updateGuestDisplay();
            }
        }
        
        function updateGuestDisplay() {
            document.getElementById('guest-count').textContent = guestCount;
            document.getElementById('guests-input').value = guestCount;
        }
        
        // Calculate total price
        function calculateTotal() {
            const checkin = document.getElementById('checkin').value;
            const checkout = document.getElementById('checkout').value;
            
            if (checkin && checkout && checkin < checkout) {
                const nights = HRS.calculateNights(checkin, checkout);
                const total = pricePerNight * nights;
                document.getElementById('total-price').textContent = 
                    `${HRS.formatPrice(total)} (${nights} night${nights > 1 ? 's' : ''})`;
            } else {
                document.getElementById('total-price').textContent = HRS.formatPrice(pricePerNight);
            }
        }
        
        // Add event listeners
        document.getElementById('checkin').addEventListener('change', calculateTotal);
        document.getElementById('checkout').addEventListener('change', calculateTotal);
        
        // Form validation
        document.getElementById('booking-form').addEventListener('submit', function(e) {
            const checkin = document.getElementById('checkin').value;
            const checkout = document.getElementById('checkout').value;
            
            if (!HRS.validateDateRange('checkin', 'checkout')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Calculate initial total if dates are provided
        calculateTotal();
    </script>
</body>
</html>
