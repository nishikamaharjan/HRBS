<?php
session_start();
include '../config.php';

// Check if user is logged in and is admin
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
//     header("Location: ../login/login.php");
//     exit;
// }

// Fetch all bookings with user and room details
$bookingsQuery = $conn->query("
    SELECT b.*, u.full_name, u.email, u.phone_number
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.id
    ORDER BY b.booking_date DESC, b.booking_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - HRBS Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            display: flex;
            background-color: #E7F6F2;
            min-height: 100vh;
            color: #2C3333;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #2C3333, #395B64);
            color: #E7F6F2;
            padding: 0;
            position: fixed;
            height: 100vh;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 2px solid #A5C9CA;
            text-align: center;
        }

        .sidebar-header h2 {
            color: #E7F6F2;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .sidebar-menu {
            flex: 1;
            padding: 1rem 0;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: #E7F6F2;
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 0.3rem 1rem;
            border-radius: 10px;
            font-weight: 500;
        }

        .sidebar a i {
            width: 24px;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .sidebar a:hover {
            background-color: #A5C9CA;
            transform: translateX(5px);
        }

        .sidebar a.active {
            background-color: #A5C9CA;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .content-wrapper {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }

        .navbar {
            background-color: #ffffff;
            padding: 1.2rem 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .navbar h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2C3333;
        }

        .table-container {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, #2C3333, #395B64);
            color: #E7F6F2;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #E7F6F2;
        }

        tbody tr:hover {
            background-color: #F8F9FA;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .status {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-pending {
            background-color: #FFF3CD;
            color: #856404;
        }

        .status-confirmed {
            background-color: #D4EDDA;
            color: #155724;
        }

        .status-cancelled {
            background-color: #F8D7DA;
            color: #721C24;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-accept {
            background-color: #28a745;
            color: #ffffff;
        }

        .btn-accept:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-reject {
            background-color: #dc3545;
            color: #ffffff;
        }

        .btn-reject:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
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

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content-wrapper {
                margin-left: 0;
            }

            table {
                font-size: 0.85rem;
            }

            th, td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-hotel"></i> HRBS Admin</h2>
        </div>
        
        <div class="sidebar-menu">
            <a href="dashboard.php">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="manage_bookings.php" class="active">
                <i class="fas fa-calendar-check"></i>
                <span>Manage Bookings</span>
            </a>
            
            <a href="room_management.php">
                <i class="fas fa-door-open"></i>
                <span>Room Management</span>
            </a>
            
            <a href="user_management.php">
                <i class="fas fa-users"></i>
                <span>User Management</span>
            </a>

            <a href="../database/logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="navbar">
            <h1><i class="fas fa-calendar-check"></i> Manage Bookings</h1>
        </div>

        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'success'; ?>">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest Name</th>
                        <th>Contact</th>
                        <th>Room Type</th>
                        <th>Booking Date</th>
                        <th>Days</th>
                        <th>Persons</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($bookingsQuery && $bookingsQuery->num_rows > 0): ?>
                        <?php while($booking = $bookingsQuery->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $booking['booking_id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($booking['full_name'] ?? 'N/A'); ?>
                            </td>
                            <td>
                                <small><?php echo htmlspecialchars($booking['email'] ?? 'N/A'); ?></small><br>
                                <small><?php echo htmlspecialchars($booking['phone_number'] ?? 'N/A'); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                            <td><?php echo $booking['days']; ?></td>
                            <td><?php echo $booking['persons']; ?></td>
                            <td>Rs. <?php echo number_format($booking['total_price'], 2); ?></td>
                            <td>
                                <span class="status status-<?php echo strtolower($booking['status']); ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if($booking['status'] == 'pending'): ?>
                                        <a href="accept_booking.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-accept">
                                            <i class="fas fa-check"></i> Accept
                                        </a>
                                        <a href="reject_booking.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-reject">
                                            <i class="fas fa-times"></i> Reject
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #999;">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 2rem; color: #999;">
                                No bookings found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Add any JavaScript functionality if needed
    </script>
</body>
</html>
<?php $conn->close(); ?>
