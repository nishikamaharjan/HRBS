<?php
session_start();
include 'config.php';

// Fetch all rooms from database
$sql = "SELECT * FROM rooms WHERE available = 1 ORDER BY price ASC";
$result = $conn->query($sql);
$rooms = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRBS - dynamic Rooms</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css"> 
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333333;
            line-height: 1.6;
        }

        /* Header matching index.php */
        header {
            background-color: #2C3333;
            padding: 1.5rem 10%;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .nav-links {
            display: flex;
            gap: 4rem;
        }

        .nav-links a {
            color: #E7F6F2;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        .nav-links a:hover {
            color: #2C3333;
            background-color: #A5C9CA;
        }

        .user-icon a {
            color: #A5C9CA;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .user-icon a:hover {
            color: #E7F6F2;
        }

        /* Hero Section for Rooms */
        .page-hero {
            background: linear-gradient(rgba(44, 51, 51, 0.8), rgba(44, 51, 51, 0.8)), url('img/landingpagebg.jpg') center/cover;
            height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #E7F6F2;
            margin-bottom: 0;
            padding-top: 80px; /* Offset fixed header */
        }

        .page-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .page-hero p {
            font-size: 1.5rem;
            color: #A5C9CA;
        }

        .room-details-section {
            padding: 4rem 5%;
            background-color: #E7F6F2;
            min-height: 60vh;
        }

        /* Improved Tabs */
        .room-type-tabs {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap; 
        }

        .tab-btn {
            padding: 0.8rem 2rem;
            border: none;
            background-color: #ffffff;
            color: #2C3333;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            border-radius: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .tab-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            color: #395B64;
        }

        .tab-btn.active {
            background-color: #2C3333;
            color: #E7F6F2;
            border-color: #A5C9CA;
            box-shadow: 0 4px 12px rgba(44, 51, 51, 0.3);
        }

        /* Room Card Container */
        .room-container {
            max-width: 1200px;
            margin: 0 auto;
            display: none;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease;
        }

        .room-container.active {
            display: grid;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .room-images {
            background-color: #f0f0f0;
            height: 100%;
        }

        .main-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .room-info {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .room-info h1 {
            color: #2C3333;
            font-size: 2.2rem;
            margin-bottom: 1rem;
            position: relative;
            padding-bottom: 1rem;
        }

        .room-info h1::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 3px;
            background-color: #A5C9CA;
        }

        .room-info p {
            color: #395B64;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        /* Dynamic Pricing Styles */
        .price-dynamic {
            background: #F8F9FA;
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid #2C3333;
            margin-bottom: 2rem;
        }

        .price-dynamic .final-price {
            font-size: 2.5rem;
            color: #2C3333;
            font-weight: 700;
        }
        
        .price-dynamic span {
             color: #666;
        }

        .breakdown-item {
            padding: 0.5rem 0;
            border-bottom: 1px dashed #ccc;
            display: flex;
            justify-content: space-between;
        }
        
        .breakdown-item:last-child {
            border-bottom: none;
        }
        
        .price-breakdown {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: #fff;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .price-breakdown.show {
            max-height: 500px;
            padding: 1rem;
            border: 1px solid #e0e0e0;
        }
        
        .price-breakdown h4 {
            color: #2C3333;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        
        .toggle-breakdown {
            cursor: pointer;
            color: #395B64;
            font-weight: 600;
            margin-top: 0.5rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s;
        }
        
        .toggle-breakdown:hover {
            color: #2C3333;
        }
        
        .amount-pos {
            color: #dc3545;
            font-weight: 600;
        }
        
        .amount-neg {
            color: #28a745;
            font-weight: 600;
        }
        
        .breakdown-detail {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
        }

        /* Form Styling */
        .booking-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group label {
            font-weight: 600;
            color: #2C3333;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #E7F6F2;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #395B64;
        }

        .book-button {
            grid-column: 1 / -1;
            background-color: #2C3333;
            color: white;
            padding: 1.2rem;
            font-size: 1.2rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .book-button:hover {
            background-color: #395B64;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .amenities-grid {
             display: grid;
             grid-template-columns: repeat(2, 1fr);
             gap: 1rem;
             margin-bottom: 2rem;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: #395B64;
        }

        .amenity-item i {
            color: #2C3333;
            background: #E7F6F2;
            padding: 0.5rem;
            border-radius: 50%;
        }

        @media (max-width: 768px) {
            .room-container.active { display: flex; flex-direction: column; }
            .room-images { height: 250px; }
            .room-info { padding: 1.5rem; }
            .booking-form { grid-template-columns: 1fr; }
            .page-hero h1 { font-size: 2.5rem; }
            .nav-links { display: none; } /* Add mobile menu if needed, but keeping it simple for now */
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="rooms.php">Rooms</a>
                <a href="about_us.php">About Us</a>
                <a href="contact.php">Contact</a>
            </div>
            <div class="user-icon">
                <a href="profile.php"><i class="fas fa-user"></i></a>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="page-hero">
        <div class="hero-content">
            <h1>Our Luxurious Rooms</h1>
            <p>Experience comfort like never before</p>
        </div>
    </section>

    <section class="room-details-section">
        <!-- Dynamic Tabs -->
        <div class="room-type-tabs">
            <?php foreach($rooms as $index => $room): ?>
                <?php 
                    // Normalize room_type to use as ID (lowercase, replace spaces with hyphens)
                    $roomTypeKey = strtolower(str_replace(' ', '-', $room['room_type'])); 
                    // Remove 'room' from key if it exists to match existing convention if standard types 
                    // But for dynamic, keeping it consistent is key. Let's use the DB value but sanitized.
                    // Actually, the JS expects clean IDs. Let's stick to simple "room_type" value if it's single word, or handle it.
                    // The old code had data-room="normal", "deluxe".
                    // If DB has "Deluxe Room", we want "deluxe".
                    $rawType = str_replace(' Room', '', $room['room_type']); // Strip ' Room' suffix if present
                    $safeType = strtolower(trim($rawType));
                ?>
                <button class="tab-btn <?php echo $index === 0 ? 'active' : ''; ?>" 
                        onclick="openRoom(event, '<?php echo $safeType; ?>')">
                    <?php echo htmlspecialchars($room['room_type']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Dynamic Room Containers -->
        <?php foreach($rooms as $index => $room): ?>
            <?php 
                $rawType = str_replace(' Room', '', $room['room_type']);
                $safeType = strtolower(trim($rawType));
            ?>
            <div class="room-container <?php echo $index === 0 ? 'active' : ''; ?>" id="<?php echo $safeType; ?>-room">
                <input type="hidden" id="room-id-<?php echo $safeType; ?>" value="<?php echo $room['id']; ?>">
                <input type="hidden" id="real-type-<?php echo $safeType; ?>" value="<?php echo htmlspecialchars($room['room_type']); ?>">
                
                <div class="room-images">
                    <?php if(!empty($room['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="<?php echo htmlspecialchars($room['room_type']); ?>" class="main-image">
                    <?php else: ?>
                        <img src="img/hotel-normal.jpg" alt="Default Image" class="main-image">
                    <?php endif; ?>
                    
                    <!-- We can add a gallery if we had a separate table for images. For now, showing main image. -->
                </div>
                
                <div class="room-info">
                    <h1><?php echo htmlspecialchars($room['room_type']); ?></h1>
                    <p><?php echo htmlspecialchars($room['description'] ?? 'Experience comfort and luxury in our ' . $room['room_type']); ?></p>
                    
                    <div class="price-dynamic" id="price-<?php echo $safeType; ?>">
                        <div>
                            <span class="base-price" id="base-price-<?php echo $safeType; ?>" style="display: none;"></span>
                            <span class="final-price" id="final-price-<?php echo $safeType; ?>">Rs.<?php echo number_format($room['price'], 2); ?></span>
                            <span>/night</span>
                        </div>
                        <div class="price-info" id="price-info-<?php echo $safeType; ?>">
                            <span class="price-loading">Select dates to see dynamic pricing</span>
                        </div>
                        <div class="toggle-breakdown" id="toggle-<?php echo $safeType; ?>" onclick="toggleBreakdown('<?php echo $safeType; ?>')" style="display: none;">
                            <i class="fas fa-chevron-down"></i> View Price Breakdown
                        </div>
                        <div class="price-breakdown" id="breakdown-<?php echo $safeType; ?>"></div>
                    </div>

                    <div class="amenities">
                        <h3>Room Amenities</h3>
                        <div class="amenities-grid">
                            <!-- Static amenities for now as they are not in DB individually -->
                            <div class="amenity-item"><i class="fas fa-wifi"></i><span>Free WiFi</span></div>
                            <div class="amenity-item"><i class="fas fa-tv"></i><span>TV</span></div>
                            <div class="amenity-item"><i class="fas fa-snowflake"></i><span>AC</span></div>
                            <div class="amenity-item"><i class="fas fa-bed"></i><span>Comfortable Bed</span></div>
                        </div>
                    </div>

                    <form class="booking-form" action="process_booking.php" method="POST" onsubmit="return validateBookingForm(this)">
                        <!-- Pass both ID and Type -->
                        <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                        <input type="hidden" name="room_type" value="<?php echo htmlspecialchars($room['room_type']); ?>"> 
                        
                        <div class="form-group">
                            <label for="check-in-<?php echo $safeType; ?>">Check-in Date</label>
                            <input type="date" id="check-in-<?php echo $safeType; ?>" name="check_in" required onchange="updateDynamicPrice('<?php echo $safeType; ?>')">
                            <span class="error-message" id="checkin-error-<?php echo $safeType; ?>"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="check-out-<?php echo $safeType; ?>">Check-out Date</label>
                            <input type="date" id="check-out-<?php echo $safeType; ?>" name="check_out" required onchange="updateDynamicPrice('<?php echo $safeType; ?>')">
                            <span class="error-message" id="checkout-error-<?php echo $safeType; ?>"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="guests-<?php echo $safeType; ?>">Number of Guests</label>
                            <input type="number" id="guests-<?php echo $safeType; ?>" name="guests" min="1" max="5" required>
                        </div>
                        
                        <button type="submit" class="book-button">Book Now</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <footer class="footer-section">
        <div class="footer-container">
            <div class="footer-column">
                <h4>HRBS</h4>
                <p>Your comfort is our priority.</p>
            </div>
        </div>
    </footer>

    <script>
        // Tab functionality
        function openRoom(evt, roomType) {
            var i, roomContainer, tabBtn;
            
            // Hide all room containers
            roomContainer = document.getElementsByClassName("room-container");
            for (i = 0; i < roomContainer.length; i++) {
                roomContainer[i].classList.remove("active");
                roomContainer[i].style.display = "none";
            }
            
            // Remove active class from all buttons
            tabBtn = document.getElementsByClassName("tab-btn");
            for (i = 0; i < tabBtn.length; i++) {
                tabBtn[i].className = tabBtn[i].className.replace(" active", "");
            }
            
            // Show current room and add active class to button
            document.getElementById(roomType + "-room").style.display = "grid"; 
            document.getElementById(roomType + "-room").classList.add("active");
            evt.currentTarget.className += " active";
        }

        // Validate Booking Form
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

        // Toggle price breakdown visibility
        function toggleBreakdown(roomType) {
            const breakdownEl = document.querySelector(`#breakdown-${roomType}`);
            const toggleBtn = document.querySelector(`#toggle-${roomType}`);
            
            if (breakdownEl.classList.contains('show')) {
                breakdownEl.classList.remove('show');
                toggleBtn.innerHTML = '<i class="fas fa-chevron-down"></i> View Price Breakdown';
            } else {
                breakdownEl.classList.add('show');
                toggleBtn.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Price Breakdown';
            }
        }

        // Update Dynamic Price
        function updateDynamicPrice(roomType) {
            const checkIn = document.querySelector(`#check-in-${roomType}`);
            const checkOut = document.querySelector(`#check-out-${roomType}`);
            const priceInfo = document.querySelector(`#price-info-${roomType}`);
            const finalPriceEl = document.querySelector(`#final-price-${roomType}`);
            const basePriceEl = document.querySelector(`#base-price-${roomType}`);
            const toggleBtn = document.querySelector(`#toggle-${roomType}`);
            const breakdownEl = document.querySelector(`#breakdown-${roomType}`);
            
            if (!checkIn.value || !checkOut.value) return;
            
            // Basic date validation before fetch
            const checkInDate = new Date(checkIn.value);
            const checkOutDate = new Date(checkOut.value);
            const today = new Date();
            today.setHours(0,0,0,0);
            
            if(checkInDate < today || checkOutDate <= checkInDate) {
                return;
            }

            priceInfo.innerHTML = '<span class="price-loading"><i class="fas fa-spinner fa-spin"></i> Calculating...</span>';
            
            // Get real room type for server lookup
            const realRoomType = document.querySelector(`#real-type-${roomType}`).value;
            
            fetch(`get_price.php?room_type=${encodeURIComponent(realRoomType)}&check_in=${checkIn.value}&check_out=${checkOut.value}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const d = data.data;
                        finalPriceEl.textContent = `Rs.${d.average_price_per_night.toFixed(2)}`;
                        
                        let infoHtml = `<strong>${d.nights} nights</strong> - Total: Rs.${d.total_price.toFixed(2)}`;
                        priceInfo.innerHTML = infoHtml;
                        
                        // NEW AGGREGATED BREAKDOWN
                        let html = `<h4>Price Breakdown</h4>`;
                        
                        // Base Price
                        html += `<div class="breakdown-item">
                            <span>Base Price (Rs.${d.base_price.toFixed(2)} × ${d.nights} nights)</span>
                            <span>Rs.${d.base_total.toFixed(2)}</span>
                        </div>`;
                        
                        // Weekend Adjustment
                        const weekendMarkup = d.weekend_markup || 0;
                        html += `<div class="breakdown-item">
                            <div>
                                <div>Weekend Adjustment (+25%)</div>
                                <div class="breakdown-detail">${weekendMarkup > 0 ? '✔ Applied (Fri/Sat)' : '❌ Not applied'}</div>
                            </div>
                            <span class="${weekendMarkup > 0 ? 'amount-pos' : ''}">
                                ${weekendMarkup > 0 ? '+Rs.' + weekendMarkup.toFixed(2) : '-'}
                            </span>
                        </div>`;
                        
                        // Seasonal Adjustment
                        const seasonalMarkup = d.seasonal_markup || 0;
                        html += `<div class="breakdown-item">
                            <div>
                                <div>Seasonal Adjustment (+30%)</div>
                                <div class="breakdown-detail">${seasonalMarkup > 0 ? '✔ Applied (Peak Season)' : '❌ Not applied'}</div>
                            </div>
                            <span class="${seasonalMarkup > 0 ? 'amount-pos' : ''}">
                                ${seasonalMarkup > 0 ? '+Rs.' + seasonalMarkup.toFixed(2) : '-'}
                            </span>
                        </div>`;
                        
                        // Long Stay Discount
                        const longStayDiscount = d.long_stay_discount || 0;
                        html += `<div class="breakdown-item">
                            <div>
                                <div>Long Stay Discount</div>
                                <div class="breakdown-detail">${longStayDiscount < 0 ? '✔ Applied' : '❌ Not applied'}</div>
                            </div>
                            <span class="${longStayDiscount < 0 ? 'amount-neg' : ''}">
                                ${longStayDiscount < 0 ? '-Rs.' + Math.abs(longStayDiscount).toFixed(2) : '-'}
                            </span>
                        </div>`;
                        
                        // Safety Net
                        const safetyNet = d.safety_net_adjustment || 0;
                        html += `<div class="breakdown-item">
                            <div>
                                <div>Safety Net</div>
                                <div class="breakdown-detail">${safetyNet > 0 ? '✔ Applied (Min. 80% base)' : '❌ Not applied'}</div>
                            </div>
                            <span class="${safetyNet > 0 ? 'amount-pos' : ''}">
                                ${safetyNet > 0 ? '+Rs.' + safetyNet.toFixed(2) : '-'}
                            </span>
                        </div>`;
                        
                        // Final Total
                        html += `<div class="breakdown-item" style="border-top: 2px solid #2C3333; margin-top: 0.5rem; padding-top: 0.5rem; font-weight: bold;">
                            <span>Final Total</span>
                            <span>Rs.${d.final_total.toFixed(2)}</span>
                        </div>`;
                        
                        html += `<p style="margin-top: 10px; font-size: 0.8rem; color: #777; font-style: italic;">
                            Prices may vary based on dates, weekends, and seasonal demand.
                        </p>`;
                        
                        breakdownEl.innerHTML = html;
                        toggleBtn.style.display = 'block';
                    } else {
                        priceInfo.innerText = "Error calculating price";
                    }
                })
                .catch(err => {
                    console.error(err);
                    priceInfo.innerText = "Error";
                });
        }
    </script>
</body>
</html>
