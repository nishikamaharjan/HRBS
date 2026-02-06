<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333333;
            line-height: 1.6;
            overflow-x: hidden;
        }

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
            list-style: none;
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
            padding: 0.5rem;
            border-radius: 50%;
            background-color: rgba(231, 246, 242, 0.1);
        }

        .user-icon a:hover {
            color: #2C3333;
            background-color: #A5C9CA;
        }

        .hero {
            text-align: center;
            padding: 15rem 1.5rem 10rem;
            background: url('./img/landingpagebg.jpg') center/cover;
            color: #E7F6F2;
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(44, 51, 51, 0.7), rgba(44, 51, 51, 0.4));
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero-content h1 {
            font-size: 4.5rem;
            margin-bottom: 1.5rem;
            color: #E7F6F2;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-content p {
            font-size: 2rem;
            margin-bottom: 2rem;
            color: #A5C9CA;
        }

        .hero-content strong {
            color: #E7F6F2;
            font-size: 2.5rem;
        }

        .services-section {
            padding: 8rem 5%;
            background-color: #E7F6F2;
        }

        .services-section h2 {
            text-align: center;
            font-size: 2.5rem;
            color: #2C3333;
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }

        .services-section h2::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -0.5rem;
            width: 60px;
            height: 3px;
            background-color: #395B64;
            transform: translateX(-50%);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .service-item {
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(165, 201, 202, 0.2);
        }

        .service-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            border-color: #A5C9CA;
        }

        .service-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .service-item h3 {
            padding: 1.5rem;
            text-align: center;
            color: #2C3333;
            font-size: 1.2rem;
        }

        .rooms-section {
            padding: 8rem 5%;
            background-color: #E7F6F2;
        }

        .rooms-section h2 {
            text-align: center;
            font-size: 2.5rem;
            color: #2C3333;
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }

        .rooms-section h2::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -0.5rem;
            width: 60px;
            height: 3px;
            background-color: #395B64;
            transform: translateX(-50%);
        }

        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .room-card {
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(165, 201, 202, 0.2);
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            border-color: #A5C9CA;
        }

        .room-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .room-details {
            padding: 2rem;
        }

        .room-details h3 {
            color: #2C3333;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .room-details h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background-color: #A5C9CA;
        }

        .room-details p {
            color: #395B64;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .price {
            color: #2C3333;
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(165, 201, 202, 0.2);
        }

        .price span {
            color: #395B64;
            font-size: 1rem;
            font-weight: normal;
        }

        .footer-section {
            background-color: #2C3333;
            color: #E7F6F2;
            padding: 6rem 5% 3rem;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 4rem;
        }

        .footer-column h4 {
            color: #A5C9CA;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-column h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 30px;
            height: 2px;
            background-color: #A5C9CA;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 0.8rem;
        }

        .footer-column ul li a {
            color: #E7F6F2;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-column ul li a:hover {
            color: #A5C9CA;
            padding-left: 0.5rem;
        }

        @media (max-width: 768px) {
            header {
                padding: 1rem 5%;
            }

            .nav-links {
                gap: 1.5rem;
            }

            .nav-links a {
                font-size: 1rem;
            }

            .hero-content h1 {
                font-size: 3rem;
            }

            .hero-content p {
                font-size: 1.5rem;
            }

            .hero-content strong {
                font-size: 2rem;
            }

            .services-grid, .rooms-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul class="nav-links">
                <li><a href="#">Home</a></li>
                <li><a href="rooms.php">Rooms</a></li>
                <li><a href="about_us.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="user-icon">
                <a href="profile.php"><i class="fas fa-user"></i></a>
            </div>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Book A Room</h1>
            <p>For Your <strong><br><br>Holiday & Business Trip</strong></p>
        </div>
    </section>

    <section class="rooms-section">
        <h2>Rooms Available</h2>
        <div class="rooms-grid">
            <div class="room-card">
                <img src="img/hotel-normal.jpg" alt="Normal Room">
                <div class="room-details">
                    <h3>Normal Room</h3>
                    <p>Indulge in a Memorable One-Time Romantic Dinner for Two</p>
                    <p class="price">Rs.1500 <span>/night</span></p>
                </div>
            </div>
            <div class="room-card">
                <img src="img/hotel-deluxe.jpg" alt="Deluxe Room">
                <div class="room-details">
                    <h3>Deluxe Room</h3>
                    <p>Experience an Exclusively Private Environment to Boost Your Productivity</p>
                    <p class="price">Rs.3000 <span>/night</span></p>
                </div>
            </div>
            <div class="room-card">
                <img src="img/hotel-suite.jpg" alt="Suite Room">
                <div class="room-details">
                    <h3>Suite Room</h3>
                    <p>Indulge in a Memorable One-Time Romantic Dinner for Two</p>
                    <p class="price">Rs.5000 <span>/night</span></p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="services-section">
        <h2>Services</h2>
        <div class="services-grid">
            <div class="service-item">
                <img src="img/hotel-normal.jpg" alt="Rooms">
                <h3>Rooms</h3>
            </div>
            <div class="service-item">
                <img src="img/dining.jpg" alt="Dining">
                <h3>Dining</h3>
            </div>
            <div class="service-item">
                <img src="img/confrence.jpg" alt="Conferences & Meetings">
                <h3>Conferences & Meetings</h3>
            </div>
            <div class="service-item">
                <img src="img/service.jpg" alt="Service & Facilities">
                <h3>Service & Facilities</h3>
            </div>
            <div class="service-item">
                <img src="img/weeding package.jpg" alt="Wedding Package">
                <h3>Wedding Package</h3>
            </div>
        </div>
    </section>


    
    <footer class="footer-section">
        <div class="footer-container">
            <div class="footer-column">
                <h4>Product</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Where to start</h4>
                <ul>
                    <li><a href="#">Rooms</a></li>
                    <li><a href="#">Dining</a></li>
                    <li><a href="#">Meetings</a></li>
                    <li><a href="#">Weddings</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Contact</h4>
                <ul>
                    <li><a href="#">Email: info@hrbs.com</a></li>
                    <li><a href="#">Phone: +977 1234567890</a></li>
                    <li><a href="#">Address: Kathmandu, Nepal</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>