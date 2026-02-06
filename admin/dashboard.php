<?php
session_start();
include '../config.php';

// Check if user is logged in and is admin
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ../login/login.php");
//     exit;
// }

// Fetch statistics
$totalUsersQuery = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $totalUsersQuery->fetch_assoc()['total_users'];

$totalBookingsQuery = $conn->query("SELECT COUNT(*) AS total_bookings FROM bookings");
$totalBookings = $totalBookingsQuery->fetch_assoc()['total_bookings'];

$totalRevenueQuery = $conn->query("SELECT SUM(total_price) AS total_revenue FROM bookings WHERE status = 'confirmed'");
$totalRevenue = $totalRevenueQuery->fetch_assoc()['total_revenue'] ?? 0;

// Fetch recent bookings
$recentBookingsQuery = $conn->query("
    SELECT b.*, u.full_name, u.email 
    FROM bookings b 
    LEFT JOIN users u ON b.user_id = u.id 
    ORDER BY b.booking_date DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HRBS</title>
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

        .dashboard-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }

        .navbar {
            background-color: #ffffff;
            padding: 1.2rem 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .navbar h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2C3333;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .card {
            background: #ffffff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #2C3333, #395B64);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .card .icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            display: inline-block;
            padding: 1rem;
            background-color: #E7F6F2;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            color: #2C3333;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .card p {
            color: #395B64;
            font-size: 2rem;
            font-weight: bold;
        }

        .table-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .table-container h2 {
            color: #2C3333;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th {
            background-color: #E7F6F2;
            color: #2C3333;
            font-weight: 600;
            padding: 1.2rem 1rem;
            text-align: left;
            border-bottom: 2px solid #395B64;
        }

        td {
            padding: 1.2rem 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        tr:hover td {
            background-color: #E7F6F2;
        }

        .status {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            gap: 0.5rem;
        }

        .status::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-pending::before {
            background-color: #856404;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-confirmed::before {
            background-color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-cancelled::before {
            background-color: #721c24;
        }

        .action-btn {
            padding: 0.5rem;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            margin: 0 0.2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .confirm-btn {
            background-color: #2C3333;
            color: #E7F6F2;
        }

        .cancel-btn {
            background-color: #dc3545;
            color: #ffffff;
        }

        .view-btn {
            background-color: #395B64;
            color: #E7F6F2;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 1024px) {
            .cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .dashboard-content {
                margin-left: 0;
                padding: 1rem;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .table-container {
                overflow-x: auto;
            }

            .navbar {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
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
            <a href="dashboard.php" class="active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="manage_bookings.php">
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

    <div class="dashboard-content">
        <div class="navbar">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h1>
        </div>

        <div class="cards">
            <div class="card">
                <div class="icon">👥</div>
                <h3>Total Users</h3>
                <p><?php echo $totalUsers; ?></p>
            </div>
            
            <div class="card">
                <div class="icon">📅</div>
                <h3>Total Bookings</h3>
                <p><?php echo $totalBookings; ?></p>
            </div>
            
            <div class="card">
                <span class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </span>
                <h3>Total Revenue</h3>
                <p>Rs. <?php echo number_format($totalRevenue, 2); ?></p>
            </div>
        </div>

        <div class="table-container">
            <h2>Recent Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest</th>
                        <th>Room Type</th>
                        <th>Check In</th>
                        <th>Duration</th>
                        <th>Status</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php while($booking = $recentBookingsQuery->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $booking['booking_id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($booking['full_name']); ?><br>
                            <small><?php echo htmlspecialchars($booking['email']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                        <td><?php echo $booking['days']; ?> days</td>
                        <td>
                            <span class="status status-<?php echo strtolower($booking['status']); ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </td>
                        <td>

                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Add mobile menu toggle functionality if needed
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
