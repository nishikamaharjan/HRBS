session_start();
// Redirect to dynamic rooms page
header("Location: rooms.php");
exit;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRBS - Room Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
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

        .room-details-section {
            padding: 8rem 5% 4rem;
            background-color: #E7F6F2;
        }

        .room-type-tabs {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-top: 2rem;
        }

        .tab-btn {
            padding: 1rem 2rem;
            border: none;
            background-color: #ffffff;
            color: #2C3333;
            font-size: 1.1rem;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 2px solid #A5C9CA;
        }

        .tab-btn:hover {
            background-color: #A5C9CA;
            color: #ffffff;
        }

        .tab-btn.active {
            background-color: #2C3333;
            color: #ffffff;
            border-color: #2C3333;
        }

        .room-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .room-images {
            padding: 2rem;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .image-gallery {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        .thumbnail {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        .thumbnail:hover {
            opacity: 0.8;
        }

        .room-info {
            padding: 2rem;
        }

        .room-info h1 {
            color: #2C3333;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .room-info p {
            color: #395B64;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .price {
            font-size: 2rem;
            color: #2C3333;
            margin: 2rem 0;
        }

        .price span {
            font-size: 1rem;
            color: #395B64;
        }

        .price-dynamic {
            font-size: 2rem;
            color: #2C3333;
            margin: 2rem 0;
        }

        .price-dynamic .base-price {
            font-size: 1.2rem;
            color: #395B64;
            text-decoration: line-through;
            margin-right: 0.5rem;
        }

        .price-dynamic .final-price {
            font-size: 2rem;
            color: #2C3333;
            font-weight: bold;
        }

        .price-info {
            font-size: 0.9rem;
            color: #395B64;
            margin-top: 0.5rem;
        }

        .price-breakdown {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #F5F5F5;
            border-radius: 8px;
            font-size: 0.9rem;
            display: none;
        }

        .price-breakdown.show {
            display: block;
        }

        .price-breakdown h4 {
            color: #2C3333;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .breakdown-item {
            display: flex;
            justify-content: space-between;
            padding: 0.3rem 0;
            border-bottom: 1px solid #ddd;
        }

        .breakdown-item:last-child {
            border-bottom: none;
            font-weight: bold;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 2px solid #2C3333;
        }

        .price-loading {
            color: #395B64;
            font-size: 0.9rem;
            font-style: italic;
        }

        .toggle-breakdown {
            color: #395B64;
            cursor: pointer;
            text-decoration: underline;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: inline-block;
        }

        .toggle-breakdown:hover {
            color: #2C3333;
        }

        .amenities {
            margin: 2rem 0;
        }

        .amenities h3 {
            color: #2C3333;
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
            gap: 0.5rem;
            color: #395B64;
        }

        .booking-form {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(165, 201, 202, 0.3);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: #2C3333;
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #A5C9CA;
            border-radius: 4px;
            font-size: 1rem;
        }

        .book-button {
            background-color: #2C3333;
            color: #E7F6F2;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .book-button:hover {
            background-color: #395B64;
        }

        .room-tab {
            display: none;
        }

        .room-tab.active {
            display: grid;
        }

        .footer-section {
            background-color: #2C3333;
            color: #E7F6F2;
            padding: 5rem 5% 2rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
        }

        .footer-column h4 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 0.8rem;
        }

        .footer-column ul li a {
            color: #A5C9CA;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-column ul li a:hover {
            color: #E7F6F2;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="nav-links">
                <a href="index.html">Home</a>
                <a href="room.php">Rooms</a>
                <a href="about_us.php">About Us</a>
                <a href="contact.php">Contact</a>
            </div>
            <div class="user-icon">
                <a href="profile.php"><i class="fas fa-user"></i></a>
            </div>
        </nav>
    </header>

    <section class="room-details-section">
        <div class="room-type-tabs">
            <button class="tab-btn active" data-room="normal">Normal Room</button>
            <button class="tab-btn" data-room="deluxe">Deluxe Room</button>
            <button class="tab-btn" data-room="suite">Suite Room</button>
            <button class="tab-btn" data-room="luxury">Luxury Room</button>
            <button class="tab-btn" data-room="premium">Premium Room</button>
            <button class="tab-btn" data-room="executive">Executive Room</button>
            <button class="tab-btn" data-room="family">Family Room</button>
            <button class="tab-btn" data-room="single">Single Room</button>
        </div>

        <!-- Normal Room -->
        <div class="room-container room-tab active" id="normal-room">
            <div class="room-images">
                <img src="img/hotel-normal.jpg" alt="Normal Room Main Image" class="main-image">
                <div class="image-gallery">
                    <img src="img\normal room1.jpg" alt="Normal Room View 1" class="thumbnail">
                    <img src="img\normal room2.jpg" alt="Normal Room View 2" class="thumbnail">
                    <img src="img\normal room3.avif" alt="Normal Room View 3" class="thumbnail">
                    <img src="img\normal room 4.png" alt="Normal Room View 4" class="thumbnail">
                </div>
            </div>
            <div class="room-info">
                <h1>Normal Room</h1>
                <p>Comfortable and cozy room perfect for solo travelers or couples. Features essential amenities for a pleasant stay.</p>
                <div class="price-dynamic" id="price-normal">
                    <div>
                        <span class="base-price" id="base-price-normal" style="display: none;"></span>
                        <span class="final-price" id="final-price-normal">Rs.2500</span>
                        <span>/night</span>
                    </div>
                    <div class="price-info" id="price-info-normal">
                        <span class="price-loading">Select dates to see dynamic pricing</span>
                    </div>
                    <div class="toggle-breakdown" id="toggle-normal" onclick="toggleBreakdown('normal')" style="display: none;">
                        <i class="fas fa-chevron-down"></i> View Price Breakdown
                    </div>
                    <div class="price-breakdown" id="breakdown-normal"></div>
                </div>
                <div class="amenities">
                    <h3>Room Amenities</h3>
                    <div class="amenities-grid">
                        <div class="amenity-item">
                            <i class="fas fa-wifi"></i>
                            <span>Free WiFi</span>
                        </div>
                        <div class="amenity-item">
                            <i class="fas fa-tv"></i>
                            <span>TV</span>
                        </div>
                        <div class="amenity-item">
                            <i class="fas fa-snowflake"></i>
                            <span>Air Conditioning</span>
                        </div>
                        <div class="amenity-item">
                            <i class="fas fa-bed"></i>
                            <span>Queen Size Bed</span>
                        </div>
                    </div>
                </div>
                <form class="booking-form" action="process_booking.php" method="POST" onsubmit="return validateBookingForm(this)">
                    <input type="hidden" name="room_type" value="normal">
                    <div class="form-group">
                        <label for="check-in-normal">Check-in Date</label>
                        <input type="date" id="check-in-normal" name="check_in" required>
                        <span class="error-message" id="checkin-error-normal"></span>
                    </div>
                    <div class="form-group">
                        <label for="check-out-normal">Check-out Date</label>
                        <input type="date" id="check-out-normal" name="check_out" required>
                        <span class="error-message" id="checkout-error-normal"></span>
                    </div>
                    <div class="form-group">
                        <label for="guests-normal">Number of Guests</label>
                        <input type="number" id="guests-normal" name="guests" min="1" max="5" required>
                    </div>
                    <button type="submit" class="book-button">Book Now</button>
                </form>
            </div>
        </div>

        <!-- Deluxe Room -->
        <div class="room-container room-tab" id="deluxe-room">
            <div class="room-images">
                <img src="img/hotel-deluxe.jpg" alt="Deluxe Room Main Image" class="main-image">
                <div class="image-gallery">
                    <img src="img\deluxe room1.jpg" alt="Deluxe Room View 1" class="thumbnail">
                    <img src="img\deluxe room2.jpeg" alt="Deluxe Room View 2" class="thumbnail">
                    <img src="img\deluxe-room3.jpeg" alt="Deluxe Room View 3" class="thumbnail">
                    <img src="img\deluxe room4.jpg" alt="Deluxe Room View 4" class="thumbnail">
                </div>
            </div>
            <div class="room-info">
                <h1>Deluxe Room</h1>
                <p>Experience luxury and comfort in our spacious Deluxe Room. Perfect for both business and leisure travelers, featuring modern amenities and stunning views.</p>
                <div class="price-dynamic" id="price-deluxe">
                    <div>
                        <span class="base-price" id="base-price-deluxe" style="display: none;"></span>
                        <span class="final-price" id="final-price-deluxe">Rs.3000</span>
                        <span>/night</span>
                    </div>
                    <div class="price-info" id="price-info-deluxe">
                        <span class="price-loading">Select dates to see dynamic pricing</span>
                    </div>
                    <div class="toggle-breakdown" id="toggle-deluxe" onclick="toggleBreakdown('deluxe')" style="display: none;">
                        <i class="fas fa-chevron-down"></i> View Price Breakdown
                    </div>
                    <div class="price-breakdown" id="breakdown-deluxe"></div>
                </div>
                <div class="amenities">
                    <h3>Room Amenities</h3>
                    <div class="amenities-grid">
                        <div class="amenity-item">
                            <i class="fas fa-wifi"></i>
                            <span>High-Speed WiFi</span>
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
                            <i class="fas fa-coffee"></i>
                            <span>Coffee Maker</span>
                        </div>
                        <div class="amenity-item">
                            <i class="fas fa-utensils"></i>
                            <span>Mini Bar</span>
                        </div>
                        <div class="amenity-item">
                            <i class="fas fa-concierge-bell"></i>
                            <span>Room Service</span>
                        </div>
                    </div>
                </div>
                <form class="booking-form" action="process_booking.php" method="POST" onsubmit="return validateBookingForm(this)">
                    <input type="hidden" name="room_type" value="deluxe">
                    <div class="form-group">
                        <label for="check-in-deluxe">Check-in Date</label>
                        <input type="date" id="check-in-deluxe" name="check_in" required>
                        <span class="error-message" id="checkin-error-deluxe"></span>
                    </div>
                    <div class="form-group">
                        <label for="check-out-deluxe">Check-out Date</label>
                        <input type="date" id="check-out-deluxe" name="check_out" required>
                        <span class="error-message" id="checkout-error-deluxe"></span>
                    </div>
                    <div class="form-group">
                        <label for="guests-deluxe">Number of Guests</label>
                        <input type="number" id="guests-deluxe" name="guests" min="1" max="5" required>
                    </div>
                    <button type="submit" class="book-button">Book Now</button>
                </form>

            </div>
        </div>

        <!-- Suite Room -->
        <div class="room-container room-tab" id="suite-room">
            <div class="room-images">
                <img src="img/hotel-suite.jpg" alt="Suite Room Main Image" class="main-image">
                <div class="image-gallery">
                    <img src="img\suite room1.jpg" alt="Suite Room View 1" class="thumbnail">
                    <img src="img\Hotel-suite-living-room.jpg" alt="Suite Room View 2" class="thumbnail">
                    <img src="img\suiteroom2.jpg" alt="Suite Room View 3" class="thumbnail">
                    <img src="img\suite room3.jpg" alt="Suite Room View 4" class="thumbnail">
                </div>
            </div>
            <div class="room-info">
                <h1>Suite Room</h1>
                <p>Our most luxurious accommodation featuring a separate living area, premium amenities, and breathtaking views. Perfect for families or those seeking the ultimate comfort.</p>
                <div class="price-dynamic" id="price-suite">
                    <div>
                        <span class="base-price" id="base-price-suite" style="display: none;"></span>
                        <span class="final-price" id="final-price-suite">Rs.5000</span>
                        <span>/night</span>
                    </div>
                    <div class="price-info" id="price-info-suite">
                        <span class="price-loading">Select dates to see dynamic pricing</span>
                    </div>
                    <div class="toggle-breakdown" id="toggle-suite" onclick="toggleBreakdown('suite')" style="display: none;">
                        <i class="fas fa-chevron-down"></i> View Price Breakdown
                    </div>
                    <div class="price-breakdown" id="breakdown-suite"></div>
                </div>
                <div class="amenities">
                    <h3>Room Amenities</h3>
                    <div class="amenities-grid">
                        <div class="amenity-item">
                            <i class="fas fa-wifi"></i>
                            <span>Premium WiFi</span>
                        </div>
                        <div class="amenity-item">
                            <i class="fas fa-tv"></i>
                            <span>65" Smart TV</span>
                        </div>
                        <div class="amenity-item">
                            <i class="fas fa-snowflake"></i>
                            <span>Climate Control</span>
                        </div>
                        <div class="amenity-item">
                            <i class="fas fa-coffee"></i>
                            <span>Premium Coffee Maker</span>
                        </div>
                        <div class="amenity-item">
                            <i class="fas fa-utensils"></i>
                            <span>Mini Kitchen</span>
                        </div>
                        <div class="amenity-item">
                            <i class="fas fa-concierge-bell"></i>
                            <span>24/7 Room Service</span>
                        </div>
                        <div class="amenity-item">
                            <i class="fas fa-couch"></i>
                            <span>Living Area</span>
                        </div>
                        <div class="amenity-item">
                            <i class="fas fa-bath"></i>
                            <span>Luxury Bathroom</span>
                        </div>
                    </div>
                </div>
                <form class="booking-form" action="process_booking.php" method="POST" onsubmit="return validateBookingForm(this)">
                    <input type="hidden" name="room_type" value="suite">
                    <div class="form-group">
                        <label for="check-in-suite">Check-in Date</label>
                        <input type="date" id="check-in-suite" name="check_in" required>
                        <span class="error-message" id="checkin-error-suite"></span>
                    </div>
                    <div class="form-group">
                        <label for="check-out-suite">Check-out Date</label>
                        <input type="date" id="check-out-suite" name="check_out" required>
                        <span class="error-message" id="checkout-error-suite"></span>
                    </div>
                    <div class="form-group">
                        <label for="guests-suite">Number of Guests</label>
                        <input type="number" id="guests-suite" name="guests" min="1" max="4" required>
                    </div>
                    <button type="submit" class="book-button">Book Now</button>
                </form>
            </div>
        </div>

        <!-- Luxury Room -->
        <div class="room-container room-tab" id="luxury-room">
            <div class="room-images">
                <img src="img/hotel-deluxe.jpg" alt="Luxury Room Main Image" class="main-image">
                <div class="image-gallery">
                    <img src="img\deluxe room1.jpg" alt="Luxury Room View 1" class="thumbnail">
                    <img src="img\deluxe room2.jpeg" alt="Luxury Room View 2" class="thumbnail">
                    <img src="img\deluxe-room3.jpeg" alt="Luxury Room View 3" class="thumbnail">
                    <img src="img\deluxe room4.jpg" alt="Luxury Room View 4" class="thumbnail">
                </div>
            </div>
            <div class="room-info">
                <h1>Luxury Room</h1>
                <p>Indulge in ultimate luxury with our premium Luxury Room. Featuring elegant furnishings, premium amenities, and exceptional comfort for a memorable stay.</p>
                <div class="price-dynamic" id="price-luxury">
                    <div>
                        <span class="base-price" id="base-price-luxury" style="display: none;"></span>
                        <span class="final-price" id="final-price-luxury">Rs.4000</span>
                        <span>/night</span>
                    </div>
                    <div class="price-info" id="price-info-luxury">
                        <span class="price-loading">Select dates to see dynamic pricing</span>
                    </div>
                    <div class="toggle-breakdown" id="toggle-luxury" onclick="toggleBreakdown('luxury')" style="display: none;">
                        <i class="fas fa-chevron-down"></i> View Price Breakdown
                    </div>
                    <div class="price-breakdown" id="breakdown-luxury"></div>
                </div>
                <div class="amenities">
                    <div class="amenity-item">
                        <i class="fas fa-wifi"></i>
                        <span>Free WiFi</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-tv"></i>
                        <span>Smart TV</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-bed"></i>
                        <span>King Size Bed</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-shower"></i>
                        <span>Premium Bathroom</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-snowflake"></i>
                        <span>Air Conditioning</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-utensils"></i>
                        <span>Mini Bar</span>
                    </div>
                </div>
                <form class="booking-form" action="process_booking.php" method="POST" onsubmit="return validateBookingForm(this)">
                    <input type="hidden" name="room_type" value="luxury">
                    <div class="form-group">
                        <label for="check-in-luxury">Check-in Date</label>
                        <input type="date" id="check-in-luxury" name="check_in" required>
                        <span class="error-message" id="checkin-error-luxury"></span>
                    </div>
                    <div class="form-group">
                        <label for="check-out-luxury">Check-out Date</label>
                        <input type="date" id="check-out-luxury" name="check_out" required>
                        <span class="error-message" id="checkout-error-luxury"></span>
                    </div>
                    <div class="form-group">
                        <label for="guests-luxury">Number of Guests</label>
                        <input type="number" id="guests-luxury" name="guests" min="1" max="3" required>
                    </div>
                    <button type="submit" class="book-button">Book Now</button>
                </form>
            </div>
        </div>

        <!-- Premium Room -->
        <div class="room-container room-tab" id="premium-room">
            <div class="room-images">
                <img src="img/hotel-suite.jpg" alt="Premium Room Main Image" class="main-image">
                <div class="image-gallery">
                    <img src="img\premium1.jpg" alt="Premium Room View 1" class="thumbnail">
                    <img src="img\premium room.jpg" alt="Premium Room View 2" class="thumbnail">
                    <img src="img\premium3.jpg" alt="Premium Room View 3" class="thumbnail">
                    <img src="img\Premium4.jpg" alt="Premium Room View 4" class="thumbnail">
                </div>
            </div>
            <div class="room-info">
                <h1>Premium Room</h1>
                <p>Experience the perfect blend of comfort and sophistication in our Premium Room. Designed for discerning travelers who appreciate quality and attention to detail.</p>
                <div class="price-dynamic" id="price-premium">
                    <div>
                        <span class="base-price" id="base-price-premium" style="display: none;"></span>
                        <span class="final-price" id="final-price-premium">Rs.4500</span>
                        <span>/night</span>
                    </div>
                    <div class="price-info" id="price-info-premium">
                        <span class="price-loading">Select dates to see dynamic pricing</span>
                    </div>
                    <div class="toggle-breakdown" id="toggle-premium" onclick="toggleBreakdown('premium')" style="display: none;">
                        <i class="fas fa-chevron-down"></i> View Price Breakdown
                    </div>
                    <div class="price-breakdown" id="breakdown-premium"></div>
                </div>
                <div class="amenities">
                    <div class="amenity-item">
                        <i class="fas fa-wifi"></i>
                        <span>High-Speed WiFi</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-tv"></i>
                        <span>55" Smart TV</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-bed"></i>
                        <span>Queen Size Bed</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-shower"></i>
                        <span>Luxury Bathroom</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-snowflake"></i>
                        <span>Climate Control</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-coffee"></i>
                        <span>Coffee Maker</span>
                    </div>
                </div>
                <form class="booking-form" action="process_booking.php" method="POST" onsubmit="return validateBookingForm(this)">
                    <input type="hidden" name="room_type" value="premium">
                    <div class="form-group">
                        <label for="check-in-premium">Check-in Date</label>
                        <input type="date" id="check-in-premium" name="check_in" required>
                        <span class="error-message" id="checkin-error-premium"></span>
                    </div>
                    <div class="form-group">
                        <label for="check-out-premium">Check-out Date</label>
                        <input type="date" id="check-out-premium" name="check_out" required>
                        <span class="error-message" id="checkout-error-premium"></span>
                    </div>
                    <div class="form-group">
                        <label for="guests-premium">Number of Guests</label>
                        <input type="number" id="guests-premium" name="guests" min="1" max="2" required>
                    </div>
                    <button type="submit" class="book-button">Book Now</button>
                </form>
            </div>
        </div>

        <!-- Executive Room -->
        <div class="room-container room-tab" id="executive-room">
            <div class="room-images">
                <img src="img/hotel-deluxe.jpg" alt="Executive Room Main Image" class="main-image">
                <div class="image-gallery">
                    <img src="img\Executive1.jpg" alt="Executive Room View 1" class="thumbnail">
                    <img src="img\Executive2.jpg" alt="Executive Room View 2" class="thumbnail">
                    <img src="img\Executive3.webp" alt="Executive Room View 3" class="thumbnail">
                    <img src="img\Executive4.webp" alt="Executive Room View 4" class="thumbnail">
                </div>
            </div>
            <div class="room-info">
                <h1>Executive Room</h1>
                <p>Designed for business travelers, our Executive Room offers a dedicated workspace, premium amenities, and a quiet environment to ensure productivity and comfort.</p>
                <div class="price-dynamic" id="price-executive">
                    <div>
                        <span class="base-price" id="base-price-executive" style="display: none;"></span>
                        <span class="final-price" id="final-price-executive">Rs.3800</span>
                        <span>/night</span>
                    </div>
                    <div class="price-info" id="price-info-executive">
                        <span class="price-loading">Select dates to see dynamic pricing</span>
                    </div>
                    <div class="toggle-breakdown" id="toggle-executive" onclick="toggleBreakdown('executive')" style="display: none;">
                        <i class="fas fa-chevron-down"></i> View Price Breakdown
                    </div>
                    <div class="price-breakdown" id="breakdown-executive"></div>
                </div>
                <div class="amenities">
                    <div class="amenity-item">
                        <i class="fas fa-wifi"></i>
                        <span>High-Speed WiFi</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-laptop"></i>
                        <span>Work Desk</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-bed"></i>
                        <span>Queen Size Bed</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-shower"></i>
                        <span>Premium Bathroom</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-snowflake"></i>
                        <span>Air Conditioning</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-coffee"></i>
                        <span>Coffee Maker</span>
                    </div>
                </div>
                <form class="booking-form" action="process_booking.php" method="POST" onsubmit="return validateBookingForm(this)">
                    <input type="hidden" name="room_type" value="executive">
                    <div class="form-group">
                        <label for="check-in-executive">Check-in Date</label>
                        <input type="date" id="check-in-executive" name="check_in" required>
                        <span class="error-message" id="checkin-error-executive"></span>
                    </div>
                    <div class="form-group">
                        <label for="check-out-executive">Check-out Date</label>
                        <input type="date" id="check-out-executive" name="check_out" required>
                        <span class="error-message" id="checkout-error-executive"></span>
                    </div>
                    <div class="form-group">
                        <label for="guests-executive">Number of Guests</label>
                        <input type="number" id="guests-executive" name="guests" min="1" max="2" required>
                    </div>
                    <button type="submit" class="book-button">Book Now</button>
                </form>
            </div>
        </div>

        <!-- Family Room -->
        <div class="room-container room-tab" id="family-room">
            <div class="room-images">
                <img src="img/hotel-normal.jpg" alt="Family Room Main Image" class="main-image">
                <div class="image-gallery">
                    <img src="img\Family1.jpg" alt="Family Room View 1" class="thumbnail">
                    <img src="img\Family2.jpg" alt="Family Room View 2" class="thumbnail">
                    <img src="img\Family3.jpg" alt="Family Room View 3" class="thumbnail">
                    <img src="img\Family4.jpg" alt="Family Room View 4" class="thumbnail">
                </div>
            </div>
            <div class="room-info">
                <h1>Family Room</h1>
                <p>Spacious and comfortable room perfect for families. Features multiple beds, extra space, and family-friendly amenities for a delightful stay with your loved ones.</p>
                <div class="price-dynamic" id="price-family">
                    <div>
                        <span class="base-price" id="base-price-family" style="display: none;"></span>
                        <span class="final-price" id="final-price-family">Rs.3500</span>
                        <span>/night</span>
                    </div>
                    <div class="price-info" id="price-info-family">
                        <span class="price-loading">Select dates to see dynamic pricing</span>
                    </div>
                    <div class="toggle-breakdown" id="toggle-family" onclick="toggleBreakdown('family')" style="display: none;">
                        <i class="fas fa-chevron-down"></i> View Price Breakdown
                    </div>
                    <div class="price-breakdown" id="breakdown-family"></div>
                </div>
                <div class="amenities">
                    <div class="amenity-item">
                        <i class="fas fa-wifi"></i>
                        <span>Free WiFi</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-tv"></i>
                        <span>Smart TV</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-bed"></i>
                        <span>Multiple Beds</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-shower"></i>
                        <span>Family Bathroom</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-snowflake"></i>
                        <span>Air Conditioning</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-child"></i>
                        <span>Child-Friendly</span>
                    </div>
                </div>
                <form class="booking-form" action="process_booking.php" method="POST" onsubmit="return validateBookingForm(this)">
                    <input type="hidden" name="room_type" value="family">
                    <div class="form-group">
                        <label for="check-in-family">Check-in Date</label>
                        <input type="date" id="check-in-family" name="check_in" required>
                        <span class="error-message" id="checkin-error-family"></span>
                    </div>
                    <div class="form-group">
                        <label for="check-out-family">Check-out Date</label>
                        <input type="date" id="check-out-family" name="check_out" required>
                        <span class="error-message" id="checkout-error-family"></span>
                    </div>
                    <div class="form-group">
                        <label for="guests-family">Number of Guests</label>
                        <input type="number" id="guests-family" name="guests" min="1" max="10" required>
                    </div>
                    <button type="submit" class="book-button">Book Now</button>
                </form>
            </div>
        </div>

        <!-- Single Room -->
        <div class="room-container room-tab" id="single-room">
            <div class="room-images">
                <img src="img/hotel-normal.jpg" alt="Single Room Main Image" class="main-image">
                <div class="image-gallery">
                    <img src="img\Single1.jpg" alt="Single Room View 1" class="thumbnail">
                    <img src="img\Single2.jpg" alt="Single Room View 2" class="thumbnail">
                    <img src="img\Single3.jpg" alt="Single Room View 3" class="thumbnail">
                    <img src="img\Single4.jpg" alt="Single Room View 4" class="thumbnail">
                </div>
            </div>
            <div class="room-info">
                <h1>Single Room</h1>
                <p>Perfect for solo travelers, our Single Room offers all essential amenities in a compact, comfortable space. Ideal for business trips or short stays.</p>
                <div class="price-dynamic" id="price-single">
                    <div>
                        <span class="base-price" id="base-price-single" style="display: none;"></span>
                        <span class="final-price" id="final-price-single">Rs.1200</span>
                        <span>/night</span>
                    </div>
                    <div class="price-info" id="price-info-single">
                        <span class="price-loading">Select dates to see dynamic pricing</span>
                    </div>
                    <div class="toggle-breakdown" id="toggle-single" onclick="toggleBreakdown('single')" style="display: none;">
                        <i class="fas fa-chevron-down"></i> View Price Breakdown
                    </div>
                    <div class="price-breakdown" id="breakdown-single"></div>
                </div>
                <div class="amenities">
                    <div class="amenity-item">
                        <i class="fas fa-wifi"></i>
                        <span>Free WiFi</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-tv"></i>
                        <span>TV</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-bed"></i>
                        <span>Single Bed</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-shower"></i>
                        <span>Private Bathroom</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-snowflake"></i>
                        <span>Air Conditioning</span>
                    </div>
                    <div class="amenity-item">
                        <i class="fas fa-lock"></i>
                        <span>Safe</span>
                    </div>
                </div>
                <form class="booking-form" action="process_booking.php" method="POST" onsubmit="return validateBookingForm(this)">
                    <input type="hidden" name="room_type" value="single">
                    <div class="form-group">
                        <label for="check-in-single">Check-in Date</label>
                        <input type="date" id="check-in-single" name="check_in" required>
                        <span class="error-message" id="checkin-error-single"></span>
                    </div>
                    <div class="form-group">
                        <label for="check-out-single">Check-out Date</label>
                        <input type="date" id="check-out-single" name="check_out" required>
                        <span class="error-message" id="checkout-error-single"></span>
                    </div>
                    <div class="form-group">
                        <label for="guests-single">Number of Guests</label>
                        <input type="number" id="guests-single" name="guests" min="1" max="1" required>
                    </div>
                    <button type="submit" class="book-button">Book Now</button>
                </form>
            </div>
        </div>
    </section>

    <footer class="footer-section">
        <div class="footer-container">
            <div class="footer-column">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Rooms</a></li>
                    <li><a href="#">Services</a></li>
                    <li><a href="#">About Us</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Contact Us</h4>
                <ul>
                    <li><a href="#">Email</a></li>
                    <li><a href="#">Phone</a></li>
                    <li><a href="#">Address</a></li>
                    <li><a href="#">Support</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Follow Us</h4>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">LinkedIn</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image gallery functionality
            const mainImages = document.querySelectorAll('.main-image');
            const thumbnails = document.querySelectorAll('.thumbnail');

            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    const mainImage = this.closest('.room-images').querySelector('.main-image');
                    mainImage.src = this.src;
                });
            });

            // Room type tabs functionality
            const tabButtons = document.querySelectorAll('.tab-btn');
            const roomTabs = document.querySelectorAll('.room-tab');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const roomType = button.getAttribute('data-room');
                    
                    // Update active button
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');

                    // Update active room tab
                    roomTabs.forEach(tab => tab.classList.remove('active'));
                    document.getElementById(`${roomType}-room`).classList.add('active');
                });
            });

            // Add event listeners to date inputs to set minimum dates
            const today = new Date().toISOString().split('T')[0];
            
            // Set min date for all date inputs
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                input.min = today;
            });
            
            // Add change event listeners to check-in dates
            const checkInInputs = document.querySelectorAll('input[id^="check-in-"]');
            checkInInputs.forEach(checkIn => {
                checkIn.addEventListener('change', function() {
                    const roomType = this.id.split('-')[2];
                    const checkOut = document.querySelector(`#check-out-${roomType}`);
                    checkOut.min = this.value;
                    // Update price when dates change
                    updateDynamicPrice(roomType);
                });
            });
            
            // Add change event listeners to check-out dates
            const checkOutInputs = document.querySelectorAll('input[id^="check-out-"]');
            checkOutInputs.forEach(checkOut => {
                checkOut.addEventListener('change', function() {
                    const roomType = this.id.split('-')[2];
                    // Update price when dates change
                    updateDynamicPrice(roomType);
                });
            });
        });
        
        // Function to update dynamic price
        function updateDynamicPrice(roomType) {
            const checkIn = document.querySelector(`#check-in-${roomType}`);
            const checkOut = document.querySelector(`#check-out-${roomType}`);
            const priceInfo = document.querySelector(`#price-info-${roomType}`);
            const finalPriceEl = document.querySelector(`#final-price-${roomType}`);
            const toggleBtn = document.querySelector(`#toggle-${roomType}`);
            const breakdownEl = document.querySelector(`#breakdown-${roomType}`);
            
            // Validate dates are selected
            if (!checkIn.value || !checkOut.value) {
                priceInfo.innerHTML = '<span class="price-loading">Select dates to see dynamic pricing</span>';
                toggleBtn.style.display = 'none';
                breakdownEl.classList.remove('show');
                return;
            }
            
            // Validate date range
            const checkInDate = new Date(checkIn.value);
            const checkOutDate = new Date(checkOut.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (checkInDate < today) {
                priceInfo.innerHTML = '<span style="color: red;">Check-in date cannot be in the past</span>';
                return;
            }
            
            if (checkOutDate <= checkInDate) {
                priceInfo.innerHTML = '<span style="color: red;">Check-out must be after check-in</span>';
                return;
            }
            
            // Show loading
            priceInfo.innerHTML = '<span class="price-loading"><i class="fas fa-spinner fa-spin"></i> Calculating price...</span>';
            
            // Fetch dynamic price
            fetch(`get_price.php?room_type=${roomType}&check_in=${checkIn.value}&check_out=${checkOut.value}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const priceData = data.data;
                        const basePriceEl = document.querySelector(`#base-price-${roomType}`);
                        const baseTotal = priceData.base_price * priceData.nights;
                        const savings = baseTotal - priceData.total_price;
                        const avgPricePerNight = priceData.average_price_per_night;
                        
                        // Show base price with strikethrough if different from average
                        if (Math.abs(avgPricePerNight - priceData.base_price) > 0.01) {
                            basePriceEl.textContent = `Rs.${priceData.base_price.toFixed(2)}`;
                            basePriceEl.style.display = 'inline';
                        } else {
                            basePriceEl.style.display = 'none';
                        }
                        
                        // Update final price
                        finalPriceEl.textContent = `Rs.${avgPricePerNight.toFixed(2)}`;
                        
                        // Update price info
                        let infoHtml = `<strong>${priceData.nights} night(s)</strong> - Total: Rs.${priceData.total_price.toFixed(2)}`;
                        
                        if (savings > 0.01) {
                            infoHtml += ` <span style="color: green;">(Save Rs.${savings.toFixed(2)})</span>`;
                        } else if (savings < -0.01) {
                            infoHtml += ` <span style="color: orange;">(Premium pricing applied)</span>`;
                        }
                        
                        priceInfo.innerHTML = infoHtml;
                        
                        // Build breakdown
                        let breakdownHtml = '<h4>Price Breakdown</h4>';
                        breakdownHtml += `<div class="breakdown-item"><span>Base Price (${priceData.nights} nights)</span><span>Rs.${baseTotal.toFixed(2)}</span></div>`;
                        
                        // Show daily breakdown
                        if (priceData.daily_prices && priceData.daily_prices.length > 0) {
                            breakdownHtml += '<div style="margin-top: 0.5rem; font-weight: bold;">Daily Pricing:</div>';
                            priceData.daily_prices.forEach(day => {
                                if (day.factors.length > 0 || day.final_price !== day.base_price) {
                                    breakdownHtml += `<div class="breakdown-item" style="font-size: 0.85rem;">`;
                                    breakdownHtml += `<span>${day.day_name} (${day.date})</span>`;
                                    breakdownHtml += `<span>Rs.${day.final_price.toFixed(2)}`;
                                    if (day.base_price !== day.final_price) {
                                        breakdownHtml += ` <span style="font-size: 0.75rem; color: #666;">(Base: Rs.${day.base_price.toFixed(2)})</span>`;
                                    }
                                    breakdownHtml += `</span></div>`;
                                    
                                    // Show factors if any
                                    if (day.factors.length > 0) {
                                        day.factors.forEach(factor => {
                                            const factorType = factor.type === 'discount' ? 'discount' : 'surcharge';
                                            const factorColor = factor.type === 'discount' ? 'green' : 'orange';
                                            const multiplierText = factor.type === 'discount' 
                                                ? `-${((1 - factor.multiplier) * 100).toFixed(0)}%`
                                                : `+${((factor.multiplier - 1) * 100).toFixed(0)}%`;
                                            breakdownHtml += `<div style="font-size: 0.75rem; color: ${factorColor}; padding-left: 1rem;">→ ${factor.name} ${multiplierText}</div>`;
                                        });
                                    }
                                }
                            });
                        }
                        
                        // Length discount
                        if (priceData.length_discount) {
                            breakdownHtml += `<div class="breakdown-item"><span>Length of Stay Discount (${priceData.length_discount.discount_percent}% off)</span><span style="color: green;">-Rs.${priceData.length_discount.discount_amount.toFixed(2)}</span></div>`;
                        }
                        
                        breakdownHtml += `<div class="breakdown-item"><span>Total Price</span><span>Rs.${priceData.total_price.toFixed(2)}</span></div>`;
                        
                        breakdownEl.innerHTML = breakdownHtml;
                        toggleBtn.style.display = 'inline-block';
                    } else {
                        priceInfo.innerHTML = `<span style="color: red;">Error: ${data.error}</span>`;
                        toggleBtn.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    priceInfo.innerHTML = '<span style="color: red;">Error fetching price. Please try again.</span>';
                    toggleBtn.style.display = 'none';
                });
        }
        
        // Toggle price breakdown visibility
        function toggleBreakdown(roomType) {
            const breakdownEl = document.querySelector(`#breakdown-${roomType}`);
            const toggleBtn = document.querySelector(`#toggle-${roomType}`);
            const icon = toggleBtn.querySelector('i');
            
            if (breakdownEl.classList.contains('show')) {
                breakdownEl.classList.remove('show');
                toggleBtn.innerHTML = '<i class="fas fa-chevron-down"></i> View Price Breakdown';
            } else {
                breakdownEl.classList.add('show');
                toggleBtn.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Price Breakdown';
            }
        }

        // Add this function for date validation
        function validateBookingForm(form) {
            const roomType = form.room_type.value;
            const checkIn = form.querySelector(`#check-in-${roomType}`);
            const checkOut = form.querySelector(`#check-out-${roomType}`);
            const checkInError = form.querySelector(`#checkin-error-${roomType}`);
            const checkOutError = form.querySelector(`#checkout-error-${roomType}`);
            
            // Reset error messages
            checkInError.style.display = 'none';
            checkOutError.style.display = 'none';
            
            // Get current date (without time)
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Convert input dates to Date objects
            const checkInDate = new Date(checkIn.value);
            const checkOutDate = new Date(checkOut.value);
            
            let isValid = true;
            
            // Check if dates are in the past
            if (checkInDate < today) {
                checkInError.textContent = 'Check-in date cannot be in the past';
                checkInError.style.display = 'block';
                isValid = false;
            }
            
            if (checkOutDate < today) {
                checkOutError.textContent = 'Check-out date cannot be in the past';
                checkOutError.style.display = 'block';
                isValid = false;
            }
            
            // Check if check-out is after check-in
            if (checkOutDate <= checkInDate) {
                checkOutError.textContent = 'Check-out date must be after check-in date';
                checkOutError.style.display = 'block';
                isValid = false;
            }
            
            return isValid;
        }
    </script>
</body>
</html>
