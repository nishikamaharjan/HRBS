<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - HRBS</title>
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
            color: #2C3333;
            line-height: 1.6;
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

        .about-hero {
            background: linear-gradient(135deg, #2C3333, #395B64);
            color: #E7F6F2;
            padding: 12rem 5% 6rem;
            text-align: center;
        }

        .about-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .about-hero p {
            font-size: 1.3rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
        }

        .about-content {
            padding: 5rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section {
            margin-bottom: 5rem;
        }

        .section-title {
            font-size: 2.5rem;
            color: #2C3333;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            padding-bottom: 1rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #2C3333, #395B64);
            border-radius: 2px;
        }

        .story-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            margin-top: 3rem;
        }

        .story-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .story-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #395B64;
        }

        .story-text p {
            margin-bottom: 1.5rem;
        }

        .mission-vision {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-top: 3rem;
        }

        .mission-card, .vision-card {
            background: #ffffff;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .mission-card:hover, .vision-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .mission-card i, .vision-card i {
            font-size: 4rem;
            color: #395B64;
            margin-bottom: 1.5rem;
        }

        .mission-card h3, .vision-card h3 {
            font-size: 2rem;
            color: #2C3333;
            margin-bottom: 1rem;
        }

        .mission-card p, .vision-card p {
            font-size: 1.1rem;
            color: #395B64;
            line-height: 1.8;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .value-card {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .value-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .value-card i {
            font-size: 3rem;
            color: #395B64;
            margin-bottom: 1rem;
        }

        .value-card h4 {
            font-size: 1.5rem;
            color: #2C3333;
            margin-bottom: 1rem;
        }

        .value-card p {
            color: #395B64;
            line-height: 1.7;
        }

        .stats-section {
            background: linear-gradient(135deg, #2C3333, #395B64);
            color: #E7F6F2;
            padding: 5rem 5%;
            margin: 5rem 0;
        }

        .stats-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #A5C9CA;
        }

        .stat-item p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .team-section {
            margin-top: 5rem;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2.5rem;
            margin-top: 3rem;
        }

        .team-card {
            background: #ffffff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-5px);
        }

        .team-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2C3333, #395B64);
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #E7F6F2;
        }

        .team-card h4 {
            font-size: 1.5rem;
            color: #2C3333;
            margin-bottom: 0.5rem;
        }

        .team-card p {
            color: #395B64;
            font-size: 1rem;
        }

        .cta-section {
            background: linear-gradient(135deg, #395B64, #2C3333);
            color: #E7F6F2;
            padding: 5rem 5%;
            text-align: center;
            margin-top: 5rem;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .cta-section p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: #A5C9CA;
            color: #2C3333;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            background: #E7F6F2;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        footer {
            background-color: #2C3333;
            color: #E7F6F2;
            padding: 4rem 5% 2rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
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

        .footer-bottom {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #395B64;
            color: #A5C9CA;
        }

        @media (max-width: 768px) {
            header {
                padding: 1rem 5%;
            }

            .nav-links {
                gap: 1.5rem;
                font-size: 0.9rem;
            }

            .about-hero {
                padding: 10rem 5% 4rem;
            }

            .about-hero h1 {
                font-size: 2.5rem;
            }

            .about-hero p {
                font-size: 1.1rem;
            }

            .story-grid,
            .mission-vision {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 2rem;
            }

            .stat-item h3 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="rooms.php">Rooms</a></li>
                <li><a href="about_us.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="user-icon">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="profile.php"><i class="fas fa-user"></i></a>
                <?php else: ?>
                    <a href="login/login.php"><i class="fas fa-user"></i></a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <section class="about-hero">
        <h1>About Hotel Room Booking System</h1>
        <p>Your trusted partner for comfortable and memorable stays</p>
    </section>

    <div class="about-content">
        <!-- Our Story Section -->
        <section class="section">
            <h2 class="section-title">Our Story</h2>
            <div class="story-grid">
                <div>
                    <img src="img/hotel-normal.jpg" alt="Our Hotel" class="story-image">
                </div>
                <div class="story-text">
                    <p>
                        Founded with a vision to revolutionize the hospitality industry, Hotel Room Booking System (HRBS) 
                        has been serving guests with exceptional accommodation services since our inception. We started 
                        as a small establishment with a big dream - to provide world-class hospitality experiences that 
                        create lasting memories.
                    </p>
                    <p>
                        Over the years, we have grown into a trusted name in the hotel industry, known for our commitment 
                        to excellence, attention to detail, and personalized service. Our journey has been marked by 
                        continuous innovation, expansion of our facilities, and most importantly, the satisfaction of 
                        thousands of guests who have made us their preferred choice.
                    </p>
                    <p>
                        Today, we stand proud as a modern hotel room booking system that seamlessly blends traditional 
                        hospitality values with cutting-edge technology, ensuring every guest enjoys a comfortable and 
                        memorable stay.
                    </p>
                </div>
            </div>
        </section>

        <!-- Mission & Vision Section -->
        <section class="section">
            <h2 class="section-title">Mission & Vision</h2>
            <div class="mission-vision">
                <div class="mission-card">
                    <i class="fas fa-bullseye"></i>
                    <h3>Our Mission</h3>
                    <p>
                        To provide exceptional hospitality services that exceed guest expectations through personalized 
                        attention, modern amenities, and a commitment to creating unforgettable experiences. We strive 
                        to be the preferred choice for travelers seeking comfort, quality, and value.
                    </p>
                </div>
                <div class="vision-card">
                    <i class="fas fa-eye"></i>
                    <h3>Our Vision</h3>
                    <p>
                        To become a leading hotel reservation platform recognized for innovation, sustainability, and 
                        excellence in service. We envision a future where technology and hospitality seamlessly converge 
                        to create exceptional experiences for guests worldwide.
                    </p>
                </div>
            </div>
        </section>

        <!-- Our Values Section -->
        <section class="section">
            <h2 class="section-title">Our Core Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-heart"></i>
                    <h4>Guest First</h4>
                    <p>Every decision we make prioritizes the comfort and satisfaction of our guests above all else.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-shield-alt"></i>
                    <h4>Integrity</h4>
                    <p>We conduct our business with honesty, transparency, and ethical practices in all our dealings.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-star"></i>
                    <h4>Excellence</h4>
                    <p>We continuously strive for excellence in service quality, facilities, and guest experiences.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-lightbulb"></i>
                    <h4>Innovation</h4>
                    <p>We embrace new technologies and innovative solutions to enhance our services and operations.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-leaf"></i>
                    <h4>Sustainability</h4>
                    <p>We are committed to environmentally responsible practices and sustainable hospitality.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-users"></i>
                    <h4>Teamwork</h4>
                    <p>We believe in the power of collaboration and value every team member's contribution.</p>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="stats-section">
            <div class="stats-container">
                <div class="stats-grid">
                    <div class="stat-item">
                        <h3>5000+</h3>
                        <p>Happy Guests</p>
                    </div>
                    <div class="stat-item">
                        <h3>10+</h3>
                        <p>Years of Experience</p>
                    </div>
                    <div class="stat-item">
                        <h3>8</h3>
                        <p>Room Types</p>
                    </div>
                    <div class="stat-item">
                        <h3>98%</h3>
                        <p>Customer Satisfaction</p>
                    </div>
                </div>
            </div>
        </section>

       

    <!-- Call to Action Section -->
    <section class="cta-section">
        <h2>Ready to Experience Excellence?</h2>
        <p>Book your stay with us today and discover why we're the preferred choice for travelers</p>
        <a href="rooms.php" class="cta-button">Book Now</a>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-column">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="rooms.php">Rooms</a></li>
                    <li><a href="about_us.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Contact Us</h4>
                <ul>
                    <li><a href="#">Email: info@hrbs.com</a></li>
                    <li><a href="#">Phone: +977 1234567890</a></li>
                    <li><a href="#">Address: Kathmandu, Nepal</a></li>
                    <li><a href="#">Support</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Follow Us</h4>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">LinkedIn</a></li>
                </ul>
            </div>
        </div>
       
    </footer>
</body>
</html>
