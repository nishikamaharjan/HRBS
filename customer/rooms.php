<?php
// No session required for browsing rooms
include_once('../config.php');

// Get filter parameters
$checkin = isset($_GET['checkin']) ? $_GET['checkin'] : '';
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : '';
$guests = isset($_GET['guests']) ? intval($_GET['guests']) : 0;
$room_type = isset($_GET['room_type']) ? $_GET['room_type'] : '';
$min_price = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? intval($_GET['max_price']) : 10000;

// Build query to fetch rooms
$query = "SELECT * FROM rooms WHERE available = 1";
$params = [];
$types = "";

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

if ($max_price < 10000) {
    $query .= " AND price <= ?";
    $params[] = $max_price;
    $types .= "i";
}

$query .= " ORDER BY price ASC";

// Execute query
$stmt = $conn->prepare($query);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$rooms = $result->fetch_all(MYSQLI_ASSOC);

// Collect distinct room types for dynamic tabs
$roomTypes = [];
foreach ($rooms as $room) {
    if (!empty($room['room_type'])) {
        $roomTypes[$room['room_type']] = true;
    }
}
$roomTypes = array_keys($roomTypes);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse available rooms at HRS - Find your perfect accommodation">
    <title>Available Rooms - HRS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            padding-top: 70px;
        }
        
        .rooms-page {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            padding: 2rem 0;
        }
        
        /* Filters Sidebar */
        .filters-sidebar {
            background-color: var(--color-white);
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            height: fit-content;
            position: sticky;
            top: 90px;
        }
        
        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--color-light);
        }
        
        .filters-header h3 {
            font-size: var(--font-size-xl);
            color: var(--color-text-dark);
        }
        
        .clear-filters {
            color: var(--color-accent);
            font-size: var(--font-size-sm);
            cursor: pointer;
            transition: color var(--transition-base);
        }
        
        .clear-filters:hover {
            color: var(--color-secondary);
        }
        
        .filter-group {
            margin-bottom: 1.5rem;
        }
        
        .filter-label {
            display: block;
            font-weight: 600;
            color: var(--color-text-dark);
            margin-bottom: 0.5rem;
            font-size: var(--font-size-sm);
        }
        
        /* Price Range Slider */
        .price-range {
            margin-top: 0.5rem;
        }
        
        .price-values {
            display: flex;
            justify-content: space-between;
            font-size: var(--font-size-sm);
            color: var(--color-text-medium);
            margin-top: 0.5rem;
        }
        
        input[type="range"] {
            width: 100%;
            margin: 0.5rem 0;
        }
        
        /* Room Results */
        .rooms-results {
            min-height: 400px;
        }

        /* Room type tabs */
        .room-type-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .room-type-tab {
            padding: 0.5rem 1.2rem;
            border-radius: var(--radius-xl, 999px);
            border: 1px solid var(--color-accent);
            background-color: var(--color-white);
            color: var(--color-text-dark);
            font-size: var(--font-size-sm);
            cursor: pointer;
            transition: all var(--transition-base);
        }

        .room-type-tab.active {
            background-color: var(--color-accent);
            color: #fff;
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--color-light);
        }
        
        .results-count {
            font-size: var(--font-size-lg);
            color: var(--color-text-dark);
        }
        
        .sort-select {
            padding: 0.5rem 1rem;
            border: 2px solid var(--color-accent);
            border-radius: var(--radius-sm);
            font-size: var(--font-size-sm);
        }
        
        /* Room Card Enhanced */
        .room-card-enhanced {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 1.5rem;
            background-color: var(--color-white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-base);
            border: 1px solid rgba(165, 201, 202, 0.2);
            margin-bottom: 1.5rem;
        }
        
        .room-card-enhanced:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            border-color: var(--color-accent);
        }
        
        .room-card-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .room-card-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .room-card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        
        .room-card-title {
            font-size: var(--font-size-2xl);
            color: var(--color-text-dark);
            margin-bottom: 0.5rem;
        }
        
        .room-availability {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-sm);
            font-size: var(--font-size-xs);
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .available {
            background-color: var(--color-success);
            color: white;
        }
        
        .room-card-description {
            color: var(--color-text-medium);
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        
        .room-facilities {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        
        .facility {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            color: var(--color-text-medium);
            font-size: var(--font-size-sm);
        }
        
        .facility i {
            color: var(--color-accent);
        }
        
        .room-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--color-light);
        }
        
        .room-price {
            font-size: var(--font-size-2xl);
            font-weight: 700;
            color: var(--color-primary);
        }
        
        .room-price span {
            font-size: var(--font-size-sm);
            color: var(--color-text-medium);
            font-weight: 400;
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
        }
        
        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .rooms-page {
                grid-template-columns: 1fr;
            }
            
            .filters-sidebar {
                position: static;
            }
            
            .room-card-enhanced {
                grid-template-columns: 1fr;
            }
            
            .room-card-image {
                height: 200px;
            }
        }
        
        @media (max-width: 768px) {
            .results-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .room-card-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .btn {
                width: 100%;
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
    <div class="container">
        <div class="rooms-page">
            <!-- Filters Sidebar -->
            <aside class="filters-sidebar">
                <div class="filters-header">
                    <h3>Filters</h3>
                    <a href="rooms.php" class="clear-filters">Clear All</a>
                </div>
                
                <form method="GET" action="rooms.php" id="filters-form">
                    <!-- Date Filters -->
                    <div class="filter-group">
                        <label class="filter-label">Check-in Date</label>
                        <input type="date" name="checkin" class="form-control" value="<?php echo htmlspecialchars($checkin); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Check-out Date</label>
                        <input type="date" name="checkout" class="form-control" value="<?php echo htmlspecialchars($checkout); ?>">
                    </div>
                    
                    <!-- Guests -->
                    <div class="filter-group">
                        <label class="filter-label">Guests</label>
                        <select name="guests" class="form-control form-select">
                            <option value="">Any</option>
                            <option value="1" <?php echo $guests == 1 ? 'selected' : ''; ?>>1 Guest</option>
                            <option value="2" <?php echo $guests == 2 ? 'selected' : ''; ?>>2 Guests</option>
                            <option value="3" <?php echo $guests == 3 ? 'selected' : ''; ?>>3 Guests</option>
                            <option value="4" <?php echo $guests == 4 ? 'selected' : ''; ?>>4 Guests</option>
                            <option value="5" <?php echo $guests >= 5 ? 'selected' : ''; ?>>5+ Guests</option>
                        </select>
                    </div>
                    
                    <!-- Room Type -->
                    <div class="filter-group">
                        <label class="filter-label">Room Type</label>
                        <select name="room_type" class="form-control form-select">
                            <option value="">All Types</option>
                            <option value="normal" <?php echo $room_type == 'normal' ? 'selected' : ''; ?>>Normal Room</option>
                            <option value="deluxe" <?php echo $room_type == 'deluxe' ? 'selected' : ''; ?>>Deluxe Room</option>
                            <option value="suite" <?php echo $room_type == 'suite' ? 'selected' : ''; ?>>Suite Room</option>
                            <option value="luxury" <?php echo $room_type == 'luxury' ? 'selected' : ''; ?>>Luxury Room</option>
                            <option value="premium" <?php echo $room_type == 'premium' ? 'selected' : ''; ?>>Premium Room</option>
                            <option value="executive" <?php echo $room_type == 'executive' ? 'selected' : ''; ?>>Executive Room</option>
                            <option value="family" <?php echo $room_type == 'family' ? 'selected' : ''; ?>>Family Room</option>
                            <option value="single" <?php echo $room_type == 'single' ? 'selected' : ''; ?>>Single Room</option>
                        </select>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="filter-group">
                        <label class="filter-label">Price Range (per night)</label>
                        <div class="price-range">
                            <input type="range" name="min_price" min="0" max="10000" step="500" value="<?php echo $min_price; ?>" id="min-price">
                            <input type="range" name="max_price" min="0" max="10000" step="500" value="<?php echo $max_price; ?>" id="max-price">
                            <div class="price-values">
                                <span>Rs.<span id="min-price-value"><?php echo $min_price; ?></span></span>
                                <span>Rs.<span id="max-price-value"><?php echo $max_price; ?></span></span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
                </form>
            </aside>
            
            <!-- Room Results -->
            <main class="rooms-results">
                <div class="results-header">
                    <div class="results-count">
                        <strong><?php echo count($rooms); ?></strong> room<?php echo count($rooms) != 1 ? 's' : ''; ?> available
                    </div>
                </div>

                <?php if (count($rooms) > 0): ?>
                    <!-- Dynamic room type tabs -->
                    <div class="room-type-tabs" id="room-type-tabs">
                        <button class="room-type-tab active" data-room-type="all">All</button>
                        <?php foreach ($roomTypes as $type): 
                            // Create a simple slug for data attribute
                            $slug = strtolower(trim(preg_replace('/\s+room$/i', '', $type)));
                            $slug = preg_replace('/\s+/', '-', $slug);
                        ?>
                            <button class="room-type-tab" data-room-type="<?php echo htmlspecialchars($slug); ?>">
                                <?php echo htmlspecialchars($type); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div id="no-rooms-message" class="empty-state" style="display:none;">
                        <i class="fas fa-bed"></i>
                        <h3>No Rooms Available</h3>
                        <p>No rooms are available in this category. Please choose another type.</p>
                    </div>

                    <?php foreach ($rooms as $room): 
                        $typeSlug = strtolower(trim(preg_replace('/\s+room$/i', '', $room['room_type'])));
                        $typeSlug = preg_replace('/\s+/', '-', $typeSlug);
                    ?>
                        <div class="room-card-enhanced" data-room-type="<?php echo htmlspecialchars($typeSlug); ?>">
                            <img src="../<?php echo htmlspecialchars($room['image_url'] ?: 'img/hotel-normal.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($room['room_type']); ?>" 
                                 class="room-card-image">
                            
                            <div class="room-card-content">
                                <div>
                                    <div class="room-card-header">
                                        <div>
                                            <h3 class="room-card-title"><?php echo htmlspecialchars($room['room_type']); ?></h3>
                                            <span class="room-availability available">Available</span>
                                        </div>
                                    </div>
                                    
                                    <p class="room-card-description">
                                        <?php echo htmlspecialchars($room['description'] ?: 'Comfortable room with modern amenities for a pleasant stay.'); ?>
                                    </p>
                                    
                                    <div class="room-facilities">
                                        <span class="facility"><i class="fas fa-wifi"></i> WiFi</span>
                                        <span class="facility"><i class="fas fa-tv"></i> TV</span>
                                        <span class="facility"><i class="fas fa-snowflake"></i> AC</span>
                                        <span class="facility"><i class="fas fa-bed"></i> Comfortable Bed</span>
                                    </div>
                                </div>
                                
                                <div class="room-card-footer">
                                    <div class="room-price">
                                        Rs.<?php echo number_format($room['price'], 0); ?> <span>/night</span>
                                    </div>
                                    <a href="room-detail.php?id=<?php echo $room['id']; ?><?php echo $checkin ? '&checkin='.$checkin : ''; ?><?php echo $checkout ? '&checkout='.$checkout : ''; ?>" 
                                       class="btn btn-primary">
                                        View Details & Book
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-bed"></i>
                        <h3>No Rooms Available</h3>
                        <p>No rooms match your search criteria. Try adjusting your filters.</p>
                        <a href="rooms.php" class="btn btn-primary mt-lg">Clear Filters</a>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-xl">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h4>About HRS</h4>
                    <p style="color: var(--color-light); line-height: 1.8;">
                        Your trusted partner for comfortable stays. We offer premium rooms and exceptional service.
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
        // Update price range display
        const minPriceInput = document.getElementById('min-price');
        const maxPriceInput = document.getElementById('max-price');
        const minPriceValue = document.getElementById('min-price-value');
        const maxPriceValue = document.getElementById('max-price-value');
        
        if (minPriceInput) {
            minPriceInput.addEventListener('input', (e) => {
                minPriceValue.textContent = e.target.value;
            });
        }
        
        if (maxPriceInput) {
            maxPriceInput.addEventListener('input', (e) => {
                maxPriceValue.textContent = e.target.value;
            });
        }

        // Room type tab filtering
        const tabContainer = document.getElementById('room-type-tabs');
        const roomCards = document.querySelectorAll('.room-card-enhanced');
        const noRoomsMessage = document.getElementById('no-rooms-message');

        if (tabContainer) {
            tabContainer.addEventListener('click', (event) => {
                const tab = event.target.closest('.room-type-tab');
                if (!tab) return;

                const selectedType = tab.getAttribute('data-room-type');

                // Update active tab
                tabContainer.querySelectorAll('.room-type-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                // Filter cards
                let visibleCount = 0;
                roomCards.forEach(card => {
                    const cardType = card.getAttribute('data-room-type');
                    if (selectedType === 'all' || cardType === selectedType) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide empty-state message for current tab
                if (noRoomsMessage) {
                    noRoomsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            });
        }
    </script>
</body>
</html>
