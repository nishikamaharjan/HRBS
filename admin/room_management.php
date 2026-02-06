<?php
session_start();
include '../config.php';

// Check if user is logged in and is admin
// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login/login.php");
    exit;
}

// Fetch all rooms
$roomsQuery = $conn->query("SELECT * FROM rooms ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management - HRBS Admin</title>
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

        .navbar-actions {
            display: flex;
            gap: 1rem;
        }

        .add-btn {
            background-color: #2C3333;
            color: #E7F6F2;
            border: none;
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: #E7F6F2;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            width: 300px;
        }

        .search-box input {
            border: none;
            background: none;
            padding: 0.5rem;
            width: 100%;
            font-size: 1rem;
            color: #2C3333;
        }

        .search-box input:focus {
            outline: none;
        }

        .search-box i {
            color: #395B64;
        }

        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            padding: 1rem 0;
        }

        .room-card {
            background: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .room-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #2C3333, #395B64);
        }

        .room-details {
            padding: 1.5rem;
        }

        .room-type {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2C3333;
            margin-bottom: 0.5rem;
        }

        .room-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #395B64;
            margin-bottom: 1rem;
        }

        .room-price small {
            font-size: 0.9rem;
            font-weight: 400;
            color: #666;
        }

        .room-description {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1rem;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .room-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding: 0.5rem 0;
            border-top: 1px solid #E7F6F2;
            border-bottom: 1px solid #E7F6F2;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .status-indicator.available {
            background-color: #28a745;
        }

        .status-indicator.unavailable {
            background-color: #dc3545;
        }

        .status-text {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .room-actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            flex: 1;
            padding: 0.8rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background-color: #2C3333;
            color: #E7F6F2;
        }

        .delete-btn {
            background-color: #dc3545;
            color: #ffffff;
        }

        .toggle-btn {
            background-color: #395B64;
            color: #E7F6F2;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: #fefefe;
            margin: 3% auto;
            width: 90%;
            max-width: 700px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes slideIn {
            from { transform: translateY(-100px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            background: linear-gradient(135deg, #2C3333, #395B64);
            color: #E7F6F2;
            padding: 1.5rem;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2C3333;
            font-weight: 500;
        }

        .form-group label.required::after {
            content: '*';
            color: #dc3545;
            margin-left: 4px;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #E7F6F2;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #395B64;
            box-shadow: 0 0 0 3px rgba(57, 91, 100, 0.1);
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #E7F6F2;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: #2C3333;
            color: #E7F6F2;
        }

        .btn-secondary {
            background-color: #E7F6F2;
            color: #2C3333;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .close-btn {
            background: none;
            border: none;
            color: #E7F6F2;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: rotate(90deg);
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

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 4rem;
            color: #A5C9CA;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: #2C3333;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 2rem;
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            .modal-content {
                margin: 10% auto;
                width: 95%;
            }

            .room-grid {
                grid-template-columns: 1fr;
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
            
            <a href="manage_bookings.php">
                <i class="fas fa-calendar-check"></i>
                <span>Manage Bookings</span>
            </a>
            
            <a href="room_management.php" class="active">
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
            <h1><i class="fas fa-door-open"></i> Room Management</h1>
            <div class="navbar-actions">
                <button class="add-btn" onclick="openAddModal()">
                    <i class="fas fa-plus-circle"></i> Add New Room
                </button>
                <div class="search-box">
                    <input type="text" id="searchRooms" placeholder="Search rooms..." onkeyup="searchRooms()">
                    <i class="fas fa-search"></i>
                </div>
            </div>
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

        <div class="room-grid" id="roomGrid">
            <?php if($roomsQuery && $roomsQuery->num_rows > 0): ?>
                <?php while($room = $roomsQuery->fetch_assoc()): ?>
                <div class="room-card" data-room-type="<?php echo strtolower($room['room_type']); ?>">
                    <img src="../<?php echo htmlspecialchars($room['image_url'] ?: 'img/hotel-normal.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($room['room_type']); ?>" 
                         class="room-image"
                         onerror="this.src='../img/hotel-normal.jpg'">
                    <div class="room-details">
                        <div class="room-type"><?php echo htmlspecialchars($room['room_type']); ?></div>
                        <?php if(!empty($room['room_number']) || !empty($room['capacity'])): ?>
                        <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.5rem;">
                            <?php if(!empty($room['room_number'])): ?>
                                <i class="fas fa-door-closed"></i> Room: <?php echo htmlspecialchars($room['room_number']); ?>
                            <?php endif; ?>
                            <?php if(!empty($room['capacity'])): ?>
                                <?php if(!empty($room['room_number'])) echo ' | '; ?>
                                <i class="fas fa-users"></i> Capacity: <?php echo $room['capacity']; ?> persons
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div class="room-price">
                            Rs. <?php echo number_format($room['price'], 0); ?>
                            <small>/ night</small>
                        </div>
                        <div class="room-description">
                            <?php echo htmlspecialchars($room['description'] ?: 'No description available.'); ?>
                        </div>
                        <div class="room-status">
                            <span class="status-indicator <?php echo $room['available'] ? 'available' : 'unavailable'; ?>"></span>
                            <span class="status-text"><?php echo $room['available'] ? 'Available' : 'Unavailable'; ?></span>
                        </div>
                        <div class="room-actions">
                            <button class="action-btn edit-btn" onclick="editRoom(<?php echo $room['id']; ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="action-btn toggle-btn" onclick="toggleAvailability(<?php echo $room['id']; ?>, <?php echo $room['available']; ?>)">
                                <i class="fas fa-toggle-<?php echo $room['available'] ? 'on' : 'off'; ?>"></i>
                            </button>
                            <button class="action-btn delete-btn" onclick="deleteRoom(<?php echo $room['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <i class="fas fa-door-open"></i>
                    <h3>No Rooms Yet</h3>
                    <p>Start by adding your first room to the system.</p>
                    <button class="add-btn" onclick="openAddModal()">
                        <i class="fas fa-plus-circle"></i> Add New Room
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Room Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle"></i> Add New Room</h2>
                <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
            </div>
            <form id="addRoomForm" action="add_room.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="room_number">Room Number</label>
                            <input type="text" id="room_number" name="room_number" class="form-control" 
                                   placeholder="e.g., 101, A-205">
                        </div>

                        <div class="form-group">
                            <label for="room_type" class="required">Room Type</label>
                            <select id="room_type" name="room_type" class="form-control" required>
                                <option value="">Select room type</option>
                                <option value="Normal Room">Normal Room</option>
                                <option value="Deluxe Room">Deluxe Room</option>
                                <option value="Suite Room">Suite Room</option>
                                <option value="Luxury Room">Luxury Room</option>
                                <option value="Premium Room">Premium Room</option>
                                <option value="Executive Room">Executive Room</option>
                                <option value="Family Room">Family Room</option>
                                <option value="Single Room">Single Room</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price" class="required">Price per Night (Rs.)</label>
                            <input type="number" id="price" name="price" class="form-control" 
                                   placeholder="Enter price" min="0" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="capacity">Capacity (Persons)</label>
                            <input type="number" id="capacity" name="capacity" class="form-control" 
                                   placeholder="Max persons" min="1" step="1">
                        </div>

                        <div class="form-group full-width">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" 
                                    placeholder="Describe the room features and amenities..." rows="3"></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="room_image" class="required">Room Image</label>
                            <input type="file" id="room_image" name="room_image" class="form-control" 
                                   accept="image/jpeg,image/jpg,image/png" onchange="previewImage(event, 'imagePreview')" required>
                            <small style="color: #666;">Upload JPG or PNG image (Max 5MB)</small>
                        </div>

                        <div class="form-group full-width" id="imagePreviewContainer" style="display: none;">
                            <label>Image Preview</label>
                            <div style="border: 2px solid #E7F6F2; border-radius: 8px; padding: 1rem; text-align: center;">
                                <img id="imagePreview" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 8px;">
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label>
                                <input type="checkbox" name="available" value="1" checked> 
                                Room is available for booking
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Add Room
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Room Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Room</h2>
                <button class="close-btn" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form id="editRoomForm" action="edit_room.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-grid">
                        <input type="hidden" id="edit_id" name="id">
                        
                        <div class="form-group">
                            <label for="edit_room_number">Room Number</label>
                            <input type="text" id="edit_room_number" name="room_number" class="form-control" 
                                   placeholder="e.g., 101, A-205">
                        </div>

                        <div class="form-group">
                            <label for="edit_room_type" class="required">Room Type</label>
                            <select id="edit_room_type" name="room_type" class="form-control" required>
                                <option value="Normal Room">Normal Room</option>
                                <option value="Deluxe Room">Deluxe Room</option>
                                <option value="Suite Room">Suite Room</option>
                                <option value="Luxury Room">Luxury Room</option>
                                <option value="Premium Room">Premium Room</option>
                                <option value="Executive Room">Executive Room</option>
                                <option value="Family Room">Family Room</option>
                                <option value="Single Room">Single Room</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_price" class="required">Price per Night (Rs.)</label>
                            <input type="number" id="edit_price" name="price" class="form-control" 
                                   min="0" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_capacity">Capacity (Persons)</label>
                            <input type="number" id="edit_capacity" name="capacity" class="form-control" 
                                   placeholder="Max persons" min="1" step="1">
                        </div>

                        <div class="form-group full-width">
                            <label for="edit_description">Description</label>
                            <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="edit_room_image">Update Room Image (Optional)</label>
                            <input type="file" id="edit_room_image" name="room_image" class="form-control" 
                                   accept="image/jpeg,image/jpg,image/png" onchange="previewImage(event, 'editImagePreview')">
                            <small style="color: #666;">Leave empty to keep current image. Upload JPG or PNG (Max 5MB)</small>
                        </div>

                        <div class="form-group full-width" id="editImagePreviewContainer" style="display: none;">
                            <label>New Image Preview</label>
                            <div style="border: 2px solid #E7F6F2; border-radius: 8px; padding: 1rem; text-align: center;">
                                <img id="editImagePreview" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 8px;">
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label>
                                <input type="checkbox" id="edit_available" name="available" value="1"> 
                                Room is available for booking
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Room
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functions
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function openEditModal() {
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Edit room
        function editRoom(roomId) {
            fetch('edit_room.php?id=' + roomId)
                .then(response => response.json())
                .then(room => {
                    document.getElementById('edit_id').value = room.id;
                    document.getElementById('edit_room_type').value = room.room_type;
                    document.getElementById('edit_price').value = room.price;
                    document.getElementById('edit_description').value = room.description || '';
                    document.getElementById('edit_image_url').value = room.image_url;
                    document.getElementById('edit_available').checked = room.available == 1;
                    openEditModal();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading room data');
                });
        }

        // Delete room
        function deleteRoom(roomId) {
            if(confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
                fetch('delete_room.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + roomId
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Error deleting room');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting room');
                });
            }
        }

        // Toggle availability
        function toggleAvailability(roomId, currentStatus) {
            const newStatus = currentStatus ? 0 : 1;
            fetch('toggle_room_availability.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${roomId}&available=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error toggling availability');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error toggling availability');
            });
        }

        // Search rooms
        function searchRooms() {
            const searchTerm = document.getElementById('searchRooms').value.toLowerCase();
            const roomCards = document.querySelectorAll('.room-card');
            
            roomCards.forEach(card => {
                const roomType = card.getAttribute('data-room-type');
                if(roomType.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Preview image before upload
        function previewImage(event, previewId) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById(previewId);
            
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Only JPG and PNG images are allowed!');
                    event.target.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }
                
                // Validate file size (5MB max)
                const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                if (file.size > maxSize) {
                    alert('Image size must be less than 5MB!');
                    event.target.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
