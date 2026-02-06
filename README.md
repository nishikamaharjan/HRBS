# Hotel Room Booking System (HRS)

A comprehensive web-based hotel room booking management system with dynamic pricing, user authentication, payment integration, and administrative features.

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation & Setup](#installation--setup)
- [Database Configuration](#database-configuration)
- [System Architecture](#system-architecture)
- [How the System Works](#how-the-system-works)
- [Dynamic Pricing Algorithm](#dynamic-pricing-algorithm)
- [User Roles & Permissions](#user-roles--permissions)
- [Project Structure](#project-structure)
- [API Endpoints](#api-endpoints)
- [Payment Integration](#payment-integration)
- [Admin Features](#admin-features)
- [Security Features](#security-features)
- [Future Enhancements](#future-enhancements)

## 🎯 Overview

The Hotel Room Booking System (HRS) is a full-featured web application that allows users to browse, book, and manage hotel room reservations. The system implements an advanced dynamic pricing algorithm that adjusts room rates based on multiple factors such as seasonality, demand, advance booking periods, and length of stay. Administrators can manage bookings, users, and monitor system statistics through a dedicated admin panel.

## ✨ Features

### User Features
- **User Registration & Authentication**: Secure user registration and login system with password hashing
- **Room Browsing**: View 8 different room types with detailed descriptions and amenities
- **Dynamic Pricing**: Real-time price calculation based on multiple factors
- **Booking Management**: View, book, and cancel reservations
- **User Profile**: Manage personal information and view booking history
- **Payment Integration**: Secure payment processing via eSewa payment gateway
- **Booking Confirmation**: Email-style confirmation page after successful booking

### Admin Features
- **Dashboard**: Overview of system statistics (users, bookings, revenue)
- **Booking Management**: Accept, reject, and view all bookings
- **User Management**: Add, edit, and manage user accounts
- **Statistics**: View total users, bookings, and revenue analytics
- **Recent Bookings**: Monitor the latest booking activities

### Advanced Features
- **Dynamic Pricing Algorithm**: Sophisticated pricing model with multiple calculation factors
- **Real-time Price Updates**: AJAX-based price calculation without page refresh
- **Price Breakdown**: Detailed pricing information showing all applied factors
- **Responsive Design**: Mobile-friendly interface
- **Session Management**: Secure session handling for user authentication
- **Input Validation**: Client-side and server-side validation

## 🛠 Technology Stack

- **Backend**: PHP 8.2+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Payment Gateway**: eSewa Integration
- **Icons**: Font Awesome 6.0
- **Server**: Apache (XAMPP/WAMP)
- **Architecture**: MVC-like structure with separation of concerns

## 📦 Installation & Setup

### Prerequisites
- XAMPP/WAMP/LAMP server
- PHP 8.2 or higher
- MySQL/MariaDB 10.4+
- Web browser (Chrome, Firefox, Edge)

### Step 1: Clone/Download Project
```bash
# Place the project in your web server directory
# For XAMPP: C:\xampp\htdocs\Projectroombooking
# For WAMP: C:\wamp64\www\Projectroombooking
```

### Step 2: Database Setup
1. Start Apache and MySQL services in XAMPP/WAMP
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Create a new database named `hrs`
4. Import the SQL file: `hrs (2).sql`
5. The database will be created with all required tables

### Step 3: Database Configuration
Update the database credentials in `config.php`:
```php
$host = 'localhost';
$dbname = 'hrs';
$username = 'root';  // Change if needed
$password = '';      // Change if needed
```

### Step 4: Access the Application
- **User Interface**: http://localhost/Projectroombooking/
- **Admin Panel**: http://localhost/Projectroombooking/admin/dashboard.php
- **Login Page**: http://localhost/Projectroombooking/login/login.php

### Default Admin Credentials
- **Email**: nishika@gmail.com
- **Password**: (Set during initial setup - check database)

## 🗄 Database Configuration

### Database Schema

#### Tables Structure

**users**
- `id` (Primary Key)
- `full_name`
- `email` (Unique)
- `phone_number`
- `dob` (Date of Birth)
- `gender`
- `password` (Hashed)
- `role` (Admin/User)
- `created_at`

**bookings**
- `booking_id` (Primary Key)
- `user_id` (Foreign Key)
- `room_id`
- `room_type`
- `days`
- `persons`
- `booking_date`
- `total_price`
- `status` (pending/confirmed/cancelled)

**rooms** (Optional - for future enhancement)
- `id`
- `room_type`
- `price`
- `image_url`
- `description`
- `available`

## 🏗 System Architecture

```
┌─────────────────┐
│   User Browser  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Frontend (PHP) │
│  - room.php     │
│  - index.html   │
│  - profile.php  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Business Logic │
│  - DynamicPricing│
│  - process_booking│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Database      │
│   (MySQL)       │
└─────────────────┘
```

## 🔄 How the System Works

### 1. User Registration Flow
```
User Registration → Validation → Password Hashing → Database Insert → Login Redirect
```

1. User fills registration form with personal details
2. Server validates input (email format, phone number, password strength)
3. Password is hashed using `password_hash()`
4. User data is stored in database
5. User is redirected to login page

### 2. User Login Flow
```
Login Form → Email/Password Verification → Session Creation → Role-based Redirect
```

1. User enters email and password
2. System verifies credentials against database
3. Password is verified using `password_verify()`
4. Session variables are set (user_id, full_name, role)
5. Redirect based on role:
   - Admin → Admin Dashboard
   - User → Home Page

### 3. Room Booking Flow
```
Room Selection → Date Selection → Dynamic Price Calculation → Booking Creation → Payment → Confirmation
```

**Step-by-Step Process:**

1. **Room Selection**
   - User browses available room types on `room.php`
   - 8 room types available: Normal, Deluxe, Suite, Luxury, Premium, Executive, Family, Single
   - Each room shows amenities and base price

2. **Date Selection & Dynamic Pricing**
   - User selects check-in and check-out dates
   - JavaScript triggers AJAX call to `get_price.php`
   - Dynamic pricing algorithm calculates price
   - Price updates in real-time without page refresh
   - User can view detailed price breakdown

3. **Booking Submission**
   - User fills booking form (dates, number of guests)
   - Form is submitted to `process_booking.php`
   - Server validates dates and calculates final price
   - Booking record is created in database with status "pending"
   - Booking data is stored in session

4. **Payment Processing**
   - User is redirected to `esewa_payment.php`
   - Booking details and total amount are displayed
   - Payment form is generated with eSewa integration
   - User completes payment via eSewa gateway

5. **Payment Callback**
   - eSewa redirects to `esewa_callback.php`
   - Payment status is verified
   - Booking status is updated:
     - Success → "confirmed"
     - Failure → "cancelled"
   - User is redirected to confirmation page

6. **Booking Confirmation**
   - Confirmation page displays booking details
   - User can view booking in their profile

### 4. Dynamic Price Calculation Flow

```
Date Selection → API Call → DynamicPricing Class → Multiple Factors Calculation → Price Return
```

**Factors Considered:**
1. Base price (room type specific)
2. Day of week (weekend multiplier)
3. Holiday detection (holiday multiplier)
4. Peak season (seasonal multiplier)
5. Advance booking discount (early bird)
6. Last-minute surcharge
7. Occupancy rate (demand-based)
8. Length of stay discount

### 5. Admin Management Flow

```
Admin Login → Dashboard → Select Management Option → Perform Action → Update Database
```

**Admin Actions:**
- View dashboard statistics
- Accept/Reject bookings
- Add/Edit/Delete users
- View booking details
- Monitor system revenue

## 💰 Dynamic Pricing Algorithm

The system implements a sophisticated dynamic pricing model that adjusts room rates based on multiple factors:

### Pricing Factors

#### 1. Base Prices
Each room type has a base price:
- Single Room: Rs. 1,200/night
- Normal Room: Rs. 1,500/night
- Family Room: Rs. 3,500/night
- Executive Room: Rs. 3,800/night
- Luxury Room: Rs. 4,000/night
- Premium Room: Rs. 4,500/night
- Deluxe Room: Rs. 3,000/night
- Suite Room: Rs. 5,000/night

#### 2. Weekend Multiplier (25% increase)
- Applies to Friday and Saturday bookings
- Multiplier: 1.25x base price

#### 3. Holiday Multiplier (50% increase)
Major holidays with premium pricing:
- New Year (Jan 1)
- Valentine's Day (Feb 14)
- Nepali New Year (Apr 14)
- Labor Day (May 1)
- Janai Purnima (Aug 30)
- Dashain (Oct 2)
- Tihar (Oct 20)
- Christmas (Dec 25)
- New Year's Eve (Dec 31)

#### 4. Peak Season Multiplier (30% increase)
Peak months: December, January, February, June, July, August

#### 5. Advance Booking Discounts
- **30+ days**: 15% discount
- **14-29 days**: 10% discount
- **7-13 days**: 5% discount

#### 6. Last-Minute Surcharge (20% increase)
- Applies to bookings made within 2 days of check-in

#### 7. Occupancy-Based Pricing
- **High Demand (80%+ booked)**: 20% increase
- **Medium Demand (60-80%)**: 10% increase
- **Low Demand (≤30%)**: 10% discount

#### 8. Length of Stay Discounts
- **7+ nights**: 15% discount
- **4-6 nights**: 10% discount
- **3 nights**: 5% discount

### Price Calculation Example

For a Deluxe Room (Base: Rs. 3,000):
- Check-in: Weekend (Saturday)
- Stay: 5 nights
- Booking: 20 days in advance
- Peak season: Yes

Calculation:
1. Base: Rs. 3,000/night
2. Weekend: Rs. 3,000 × 1.25 = Rs. 3,750
3. Peak Season: Rs. 3,750 × 1.30 = Rs. 4,875
4. Advance Discount (10%): Rs. 4,875 × 0.90 = Rs. 4,387.50
5. Length Discount (10% on total): Applied to final total

**Final Price**: Calculated dynamically per night, then length discount applied

## 👥 User Roles & Permissions

### Regular User
- Register and login
- Browse rooms
- Make bookings
- View own bookings
- Cancel own bookings
- Update profile

### Admin
- All user permissions
- Access admin dashboard
- View all bookings
- Accept/Reject bookings
- Manage users (add/edit/delete)
- View system statistics
- Monitor revenue

## 📁 Project Structure

```
Projectroombooking/
│
├── admin/                      # Admin panel files
│   ├── dashboard.php          # Admin dashboard
│   ├── manage_bookings.php    # Booking management
│   ├── user_management.php    # User management
│   ├── accept_booking.php     # Accept booking handler
│   ├── reject_booking.php     # Reject booking handler
│   ├── add_user.php           # Add user handler
│   └── edit_user.php          # Edit user handler
│
├── login/                      # Authentication files
│   ├── login.php              # Login page
│   ├── register.php           # Registration page
│   ├── forgotpassword.php     # Password recovery
│   ├── login.css              # Login styles
│   ├── register.css           # Registration styles
│   └── forgotpassword.css     # Password recovery styles
│
├── database/                   # Database utilities
│   ├── dbconnect.php          # Database connection (legacy)
│   └── logout.php             # Logout handler
│
├── img/                        # Image assets
│   └── [Room images]          # Room photos
│
├── DynamicPricing.php         # Dynamic pricing algorithm class
├── get_price.php              # Price API endpoint
├── config.php                 # Database configuration
├── process_booking.php        # Booking processing
├── esewa_payment.php          # Payment page
├── esewa_callback.php         # Payment callback handler
├── booking_confirmation.php   # Booking confirmation page
├── cancel_booking.php         # Booking cancellation
├── room.php                   # Room browsing page
├── profile.php                # User profile
├── index.html                 # Home page
├── about_us.php               # About page
├── contact.php                # Contact page
├── logout.php                 # Logout handler
├── notification_helper.php    # Notification utilities
├── style.css                  # Main stylesheet
├── script.js                  # JavaScript utilities
├── hrs (2).sql                # Database schema
└── README.md                  # This file
```

## 🔌 API Endpoints

### GET `/get_price.php`
Returns dynamic pricing for a room booking.

**Parameters:**
- `room_type` (string): Room type identifier
- `check_in` (date): Check-in date (Y-m-d)
- `check_out` (date): Check-out date (Y-m-d)

**Response:**
```json
{
  "success": true,
  "data": {
    "base_price": 3000,
    "nights": 3,
    "daily_prices": [...],
    "subtotal": 11250.00,
    "length_discount": {
      "days": 3,
      "discount_percent": 5,
      "discount_amount": 562.50
    },
    "total_price": 10687.50,
    "average_price_per_night": 3562.50
  }
}
```

## 💳 Payment Integration

### eSewa Integration

The system integrates with eSewa payment gateway for secure payment processing.

**Payment Flow:**
1. User submits booking
2. System generates payment form with booking details
3. User is redirected to eSewa payment page
4. User completes payment
5. eSewa redirects back with payment status
6. System verifies payment and updates booking status

**Configuration:**
- Payment gateway URL: https://rc-epay.esewa.com.np/api/epay/main/v2/form
- Test mode: Uses EPAYTEST product code
- Signature generation: HMAC-SHA256

**Security:**
- Transaction UUID: Unique identifier for each payment
- Signature verification: Ensures payment authenticity
- Secure redirect: HTTPS endpoints

## 🔐 Security Features

- **Password Hashing**: Bcrypt password hashing using `password_hash()`
- **SQL Injection Protection**: Prepared statements for all database queries
- **XSS Protection**: `htmlspecialchars()` for output escaping
- **Session Security**: Session-based authentication
- **Input Validation**: Server-side validation for all inputs
- **CSRF Protection**: Session tokens (can be enhanced)
- **Role-based Access**: Admin/user role separation

## 📊 Admin Features

### Dashboard
- Total users count
- Total bookings count
- Total revenue (confirmed bookings)
- Recent bookings list

### Booking Management
- View all bookings with user details
- Accept pending bookings
- Reject bookings
- Filter by status
- View booking details

### User Management
- View all users
- Add new users
- Edit user information
- View user booking history
- Manage user roles

## 🚀 Future Enhancements

- [ ] Room availability calendar
- [ ] Email notifications for bookings
- [ ] SMS notifications
- [ ] Booking modification feature
- [ ] Review and rating system
- [ ] Multiple payment gateways
- [ ] Multi-language support
- [ ] Advanced search and filtering
- [ ] Room images gallery
- [ ] Seasonal promotions
- [ ] Loyalty program
- [ ] Analytics dashboard
- [ ] Export reports (PDF/Excel)
- [ ] API for mobile apps
- [ ] Real-time availability check

## 📝 Notes

### Important Considerations

1. **Database Configuration**: Update `config.php` with your database credentials
2. **Payment Gateway**: Configure eSewa credentials for production use
3. **File Permissions**: Ensure proper file permissions for uploads (if added)
4. **Session Configuration**: Session settings can be customized in `php.ini`
5. **Error Logging**: Enable error logging for production debugging

### Development Notes

- The system uses PHP sessions for authentication
- All database operations use prepared statements
- Frontend uses vanilla JavaScript (no frameworks)
- CSS uses modern Flexbox and Grid layouts
- Responsive design for mobile compatibility

## 🤝 Contributing

This is a project for learning and demonstration purposes. Contributions and improvements are welcome!

## 📄 License

This project is provided as-is for educational purposes.

## 👨‍💻 Support

For issues or questions:
1. Check the documentation
2. Review the code comments
3. Check database configuration
4. Verify file permissions

---

**Developed with ❤️ for Hotel Room Management**

*Last Updated: January 2025*
