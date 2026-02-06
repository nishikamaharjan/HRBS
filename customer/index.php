<?php
// No session required for landing page - users can browse without login
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hotel Reservation System - Book your perfect room for holidays and business trips">
    <title>HRS - Hotel Reservation System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Hero Section Specific Styles */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('../img/landingpagebg.jpg') center/cover no-repeat;
            color: var(--color-light);
            padding-top: 80px;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(44, 51, 51, 0.7), rgba(44, 51, 51, 0.5));
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 1200px;
            padding: 2rem;
        }
        
        .hero-title {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--color-light);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.8s ease;
        }
        
        .hero-subtitle {
            font-size: 2rem;
            margin-bottom: 3rem;
            color: var(--color-accent);
            animation: slideUp 0.8s ease 0.2s backwards;
        }
        
        /* Search Form */
        .search-form {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            max-width: 900px;
            margin: 0 auto;
            animation: slideUp 0.8s ease 0.4s backwards;
        }
        
        .search-form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .search-form .form-group {
            margin-bottom: 0;
        }
        
        .search-form .form-label {
            color: var(--color-text-dark);
            font-size: var(--font-size-sm);
            font-weight: 600;
        }
        
        /* Room Cards */
        .room-card-price {
            font-size: var(--font-size-2xl);
            font-weight: 700;
            color: var(--color-primary);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid var(--color-light);
        }
        
        .room-card-price span {
            font-size: var(--font-size-sm);
            color: var(--color-text-medium);
            font-weight: 400;
        }
        
        /* Section Headers */
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title {
            font-size: var(--font-size-3xl);
            color: var(--color-text-dark);
            margin-bottom: 0.5rem;
            position: relative;
            display: inline-block;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -0.5rem;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background-color: var(--color-secondary);
        }
        
        /* Services Grid */
        .service-card {
            text-align: center;
        }
        
        .service-card .card-img {
            height: 200px;
        }
        
        .service-card h3 {
            padding: 1.5rem;
            font-size: var(--font-size-lg);
        }
        
        /* Facilities Icons */
        .facilities-icons {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }
        
        .facility-icon {
            color: var(--color-accent);
            font-size: var(--font-size-sm);
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.5rem;
            }
            
            .search-form {
                padding: 1.5rem;
            }
            
            .search-form-grid {
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
                <li><a href="index.php" class="nav-link active">Home</a></li>
                <li><a href="rooms.php" class="nav-link">Rooms</a></li>
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

    <!-- Hero Section with Search -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Book A Room</h1>
            <p class="hero-subtitle">For Your <strong>Holiday & Business Trip</strong></p>
            
            <!-- Search Form -->
            <div class="search-form">
                <form action="rooms.php" method="GET">
                    <div class="search-form-grid">
                        <div class="form-group">
                            <label for="search-checkin" class="form-label">Check-in</label>
                            <input type="date" id="search-checkin" name="checkin" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="search-checkout" class="form-label">Check-out</label>
                            <input type="date" id="search-checkout" name="checkout" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="search-guests" class="form-label">Guests</label>
                            <select id="search-guests" name="guests" class="form-control form-select" required>
                                <option value="">Select guests</option>
                                <option value="1">1 Guest</option>
                                <option value="2" selected>2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                                <option value="5">5+ Guests</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-search"></i> Search Rooms
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Popular Rooms Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Popular Rooms</h2>
            </div>
            
            <div class="grid grid-auto-fit">
                <!-- Normal Room -->
                <div class="card">
                    <img src="../img/hotel-normal.jpg" alt="Normal Room" class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">Normal Room</h3>
                        <p class="card-text">Comfortable and cozy room perfect for solo travelers or couples.</p>
                        <div class="facilities-icons">
                            <span class="facility-icon" title="WiFi"><i class="fas fa-wifi"></i></span>
                            <span class="facility-icon" title="TV"><i class="fas fa-tv"></i></span>
                            <span class="facility-icon" title="AC"><i class="fas fa-snowflake"></i></span>
                            <span class="facility-icon" title="Queen Bed"><i class="fas fa-bed"></i></span>
                        </div>
                        <div class="room-card-price">
                            Rs.2,500 <span>/night</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="rooms.php?room_type=normal" class="btn btn-primary btn-block">View Details</a>
                    </div>
                </div>

                <!-- Deluxe Room -->
                <div class="card">
                    <img src="../img/hotel-deluxe.jpg" alt="Deluxe Room" class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">Deluxe Room</h3>
                        <p class="card-text">Spacious luxury with modern amenities and stunning views.</p>
                        <div class="facilities-icons">
                            <span class="facility-icon" title="WiFi"><i class="fas fa-wifi"></i></span>
                            <span class="facility-icon" title="Smart TV"><i class="fas fa-tv"></i></span>
                            <span class="facility-icon" title="AC"><i class="fas fa-snowflake"></i></span>
                            <span class="facility-icon" title="Mini Bar"><i class="fas fa-utensils"></i></span>
                        </div>
                        <div class="room-card-price">
                            Rs.3,000 <span>/night</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="rooms.php?room_type=deluxe" class="btn btn-primary btn-block">View Details</a>
                    </div>
                </div>

                <!-- Suite Room -->
                <div class="card">
                    <img src="../img/hotel-suite.jpg" alt="Suite Room" class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">Suite Room</h3>
                        <p class="card-text">Ultimate luxury with separate living area and premium amenities.</p>
                        <div class="facilities-icons">
                            <span class="facility-icon" title="Premium WiFi"><i class="fas fa-wifi"></i></span>
                            <span class="facility-icon" title="65'' TV"><i class="fas fa-tv"></i></span>
                            <span class="facility-icon" title="Living Area"><i class="fas fa-couch"></i></span>
                            <span class="facility-icon" title="Mini Kitchen"><i class="fas fa-utensils"></i></span>
                        </div>
                        <div class="room-card-price">
                            Rs.5,000 <span>/night</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="rooms.php?room_type=suite" class="btn btn-primary btn-block">View Details</a>
                    </div>
                </div>

                <!-- Luxury Room -->
                <div class="card">
                    <img src="../img/hotel-deluxe.jpg" alt="Luxury Room" class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">Luxury Room</h3>
                        <p class="card-text">Premium comfort with elegant furnishings and exceptional service.</p>
                        <div class="facilities-icons">
                            <span class="facility-icon" title="WiFi"><i class="fas fa-wifi"></i></span>
                            <span class="facility-icon" title="Smart TV"><i class="fas fa-tv"></i></span>
                            <span class="facility-icon" title="King Bed"><i class="fas fa-bed"></i></span>
                            <span class="facility-icon" title="Premium Bath"><i class="fas fa-shower"></i></span>
                        </div>
                        <div class="room-card-price">
                            Rs.4,000 <span>/night</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="rooms.php?room_type=luxury" class="btn btn-primary btn-block">View Details</a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-xl">
                <a href="rooms.php" class="btn btn-secondary btn-lg">View All Rooms</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="section" style="background-color: var(--color-white);">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Services</h2>
            </div>
            
            <div class="grid grid-auto-fit">
                <div class="card service-card">
                    <img src="../img/hotel-normal.jpg" alt="Rooms" class="card-img">
                    <h3>Rooms</h3>
                </div>
                <div class="card service-card">
                    <img src="../img/dining.jpg" alt="Dining" class="card-img">
                    <h3>Dining</h3>
                </div>
                <div class="card service-card">
                    <img src="../img/confrence.jpg" alt="Conferences & Meetings" class="card-img">
                    <h3>Conferences & Meetings</h3>
                </div>
                <div class="card service-card">
                    <img src="../img/service.jpg" alt="Service & Facilities" class="card-img">
                    <h3>Service & Facilities</h3>
                </div>
                <div class="card service-card">
                    <img src="../img/weeding package.jpg" alt="Wedding Package" class="card-img">
                    <h3>Wedding Package</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h4>About HRS</h4>
                    <p style="color: var(--color-light); line-height: 1.8;">
                        Your trusted partner for comfortable stays. We offer premium rooms and exceptional service for both leisure and business travelers.
                    </p>
                </div>
                <div class="footer-column">
                    <h4>Quick Links</h4>
                    <a href="../about_us.php" class="footer-link">About Us</a>
                    <a href="rooms.php" class="footer-link">Rooms</a>
                    <a href="../contact.php" class="footer-link">Contact Us</a>
                    <a href="#" class="footer-link">FAQs</a>
                </div>
                <div class="footer-column">
                    <h4>Services</h4>
                    <a href="#" class="footer-link">Dining</a>
                    <a href="#" class="footer-link">Conferences</a>
                    <a href="#" class="footer-link">Weddings</a>
                    <a href="#" class="footer-link">Facilities</a>
                </div>
                <div class="footer-column">
                    <h4>Contact</h4>
                    <a href="mailto:info@hrbs.com" class="footer-link">
                        <i class="fas fa-envelope"></i> info@hrbs.com
                    </a>
                    <a href="tel:+9771234567890" class="footer-link">
                        <i class="fas fa-phone"></i> +977 1234567890
                    </a>
                    <a href="#" class="footer-link">
                        <i class="fas fa-map-marker-alt"></i> Kathmandu, Nepal
                    </a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Hotel Reservation System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
