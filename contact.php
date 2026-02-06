<?php
session_start();
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Here you would typically send an email or save to database
        // For now, we'll just show a success message
        $success_message = "Thank you for contacting us! We'll get back to you soon.";
        
        // Optional: Save to database or send email
        // You can implement email sending or database storage here
        
        // Clear form data after successful submission
        $name = $email = $phone = $subject = $message = '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - HRBS</title>
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

        .contact-hero {
            background: linear-gradient(135deg, #2C3333, #395B64);
            color: #E7F6F2;
            padding: 12rem 5% 6rem;
            text-align: center;
        }

        .contact-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .contact-hero p {
            font-size: 1.3rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
        }

        .contact-content {
            padding: 5rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            margin-top: 3rem;
        }

        .contact-info {
            background: #ffffff;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .contact-info h2 {
            font-size: 2rem;
            color: #2C3333;
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 1rem;
        }

        .contact-info h2::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #2C3333, #395B64);
            border-radius: 2px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #F8F9FA;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .info-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .info-item i {
            font-size: 1.5rem;
            color: #395B64;
            margin-right: 1.5rem;
            min-width: 30px;
        }

        .info-item div h3 {
            font-size: 1.2rem;
            color: #2C3333;
            margin-bottom: 0.5rem;
        }

        .info-item div p {
            color: #395B64;
            font-size: 1rem;
        }

        .contact-form-container {
            background: #ffffff;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .contact-form-container h2 {
            font-size: 2rem;
            color: #2C3333;
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 1rem;
        }

        .contact-form-container h2::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #2C3333, #395B64);
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: #2C3333;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 1rem;
            border: 2px solid #A5C9CA;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #395B64;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 150px;
        }

        .submit-btn {
            background: linear-gradient(135deg, #2C3333, #395B64);
            color: #E7F6F2;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 51, 51, 0.3);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background-color: #D4EDDA;
            color: #155724;
            border: 1px solid #C3E6CB;
        }

        .alert-error {
            background-color: #F8D7DA;
            color: #721C24;
            border: 1px solid #F5C6CB;
        }

        .map-section {
            margin-top: 5rem;
            background: #ffffff;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .map-section h2 {
            font-size: 2rem;
            color: #2C3333;
            margin-bottom: 2rem;
            text-align: center;
        }

        .map-container {
            width: 100%;
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            background: #E7F6F2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #395B64;
            font-size: 1.1rem;
        }

        .social-links {
            display: flex;
            gap: 1.5rem;
            margin-top: 2rem;
            justify-content: center;
        }

        .social-links a {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #2C3333, #395B64);
            color: #E7F6F2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .social-links a:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(44, 51, 51, 0.3);
        }

        footer {
            background-color: #2C3333;
            color: #E7F6F2;
            padding: 4rem 5% 2rem;
            margin-top: 5rem;
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

            .contact-hero {
                padding: 10rem 5% 4rem;
            }

            .contact-hero h1 {
                font-size: 2.5rem;
            }

            .contact-container {
                grid-template-columns: 1fr;
            }

            .map-container {
                height: 300px;
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

    <section class="contact-hero">
        <h1>Get In Touch</h1>
        <p>We're here to help and answer any question you might have</p>
    </section>

    <div class="contact-content">
        <div class="contact-container">
            <!-- Contact Information -->
            <div class="contact-info">
                <h2>Contact Information</h2>
                
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Address</h3>
                        <p>Kathmandu, Nepal<br>123 Hotel Street, Tourism District</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h3>Phone</h3>
                        <p>+977 1234567890<br>+977 9876543210</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p>info@hrbs.com<br>support@hrbs.com</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h3>Business Hours</h3>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday - Sunday: 10:00 AM - 4:00 PM</p>
                    </div>
                </div>

                <div class="social-links">
                    <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-container">
                <h2>Send Us a Message</h2>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="contact.php">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <select id="subject" name="subject" required>
                            <option value="">Select a subject</option>
                            <option value="general" <?php echo (isset($subject) && $subject == 'general') ? 'selected' : ''; ?>>General Inquiry</option>
                            <option value="booking" <?php echo (isset($subject) && $subject == 'booking') ? 'selected' : ''; ?>>Booking Information</option>
                            <option value="support" <?php echo (isset($subject) && $subject == 'support') ? 'selected' : ''; ?>>Customer Support</option>
                            <option value="feedback" <?php echo (isset($subject) && $subject == 'feedback') ? 'selected' : ''; ?>>Feedback</option>
                            <option value="complaint" <?php echo (isset($subject) && $subject == 'complaint') ? 'selected' : ''; ?>>Complaint</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>


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
                    <li><a href="mailto:info@hrs.com">Email: info@hrbs.com</a></li>
                    <li><a href="tel:+9771234567890">Phone: +977 1234567890</a></li>
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
