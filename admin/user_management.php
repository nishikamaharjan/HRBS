<?php
session_start();
include '../config.php';

// Check if user is logged in and is admin
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ../login/login.php");
//     exit;
// }

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login/login.php");
//     exit;
// }

// Fetch all users
$usersQuery = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - HRBS Admin</title>
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

        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem 0;
        }

        .user-card {
            background: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .user-header {
            background: linear-gradient(135deg, #2C3333, #395B64);
            color: #E7F6F2;
            padding: 2rem;
            text-align: center;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #E7F6F2;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: #2C3333;
        }

        .user-details {
            padding: 1.5rem;
        }

        .user-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2C3333;
            margin-bottom: 0.5rem;
        }

        .user-email {
            color: #666;
            margin-bottom: 1rem;
        }

        .user-meta {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 0.9rem;
        }

        .user-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
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

        .action-btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .role-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .role-admin {
            background-color: #2C3333;
            color: #E7F6F2;
        }

        .role-user {
            background-color: #E7F6F2;
            color: #2C3333;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
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
            margin: 5% auto;
            width: 90%;
            max-width: 800px;
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
            margin-bottom: 1.5rem;
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

        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 0.5rem;
        }

        .error {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .modal-content {
                margin: 10% auto;
                width: 95%;
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
            
            <a href="room_management.php">
                <i class="fas fa-door-open"></i>
                <span>Room Management</span>
            </a>
            
            <a href="user_management.php" class="active">
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
            <h1><i class="fas fa-users"></i> User Management</h1>
            <div class="navbar-actions">
                <button class="add-btn" onclick="openAddModal()">
                    <i class="fas fa-user-plus"></i> Add New User
                </button>
                <div class="search-box">
                    <input type="text" id="searchUsers" placeholder="Search users...">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </div>

        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <div class="user-grid">
            <?php while($user = $usersQuery->fetch_assoc()): ?>
            <div class="user-card">
                <div class="user-header">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <span class="role-badge role-<?php echo strtolower($user['role']); ?>">
                        <?php echo ucfirst($user['role']); ?>
                    </span>
                </div>
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                    <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                    <div class="user-meta">
                        <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['phone_number']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($user['dob'])); ?></span>
                    </div>
                    <div class="user-actions">
                        <button class="action-btn edit-btn" onclick="editUser(<?php echo $user['id']; ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteUser(<?php echo $user['id']; ?>)">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Add New User</h2>
                <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
            </div>
            <form id="addUserForm" action="add_user.php" method="POST">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="full_name" class="required">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" 
                                   placeholder="Enter full name" required>
                            <span class="error" id="name_error"></span>
                        </div>

                        <div class="form-group">
                            <label for="email" class="required">Email</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   placeholder="Enter email address" required>
                        </div>

                        <div class="form-group">
                            <label for="phone_number" class="required">Phone Number</label>
                            <input type="tel" id="phone_number" name="phone_number" class="form-control" 
                                   placeholder="Enter phone number" required>
                            <span class="error" id="phone_error"></span>
                        </div>

                        <div class="form-group">
                            <label for="dob" class="required">Date of Birth</label>
                            <input type="date" id="dob" name="dob" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="gender" class="required">Gender</label>
                            <select id="gender" name="gender" class="form-control" required>
                                <option value="">Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="role" class="required">Role</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">Select role</option>
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="password" class="required">Password</label>
                            <div class="password-group">
                                <input type="password" id="password" name="password" class="form-control" 
                                       placeholder="Enter password" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span class="error" id="password_error"></span>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="required">Confirm Password</label>
                            <div class="password-group">
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                                       placeholder="Confirm password" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span class="error" id="confirm_password_error"></span>
                        </div>

                        <div class="form-group full-width">
                            <label for="address" class="required">Address</label>
                            <textarea id="address" name="address" class="form-control" 
                                    placeholder="Enter full address" required rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit User</h2>
                <button class="close-btn" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form id="editUserForm" action="edit_user.php" method="POST">
                <div class="modal-body">
                    <div class="form-grid">
                        <input type="hidden" id="edit_id" name="id">
                        
                        <div class="form-group">
                            <label for="edit_full_name" class="required">Full Name</label>
                            <input type="text" id="edit_full_name" name="full_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_email" class="required">Email</label>
                            <input type="email" id="edit_email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_phone_number" class="required">Phone Number</label>
                            <input type="tel" id="edit_phone_number" name="phone_number" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_dob" class="required">Date of Birth</label>
                            <input type="date" id="edit_dob" name="dob" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_gender" class="required">Gender</label>
                            <select id="edit_gender" name="gender" class="form-control" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_role" class="required">Role</label>
                            <select id="edit_role" name="role" class="form-control" required>
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_password">New Password (Leave blank to keep current)</label>
                            <div class="password-group">
                                <input type="password" id="edit_password" name="password" class="form-control">
                                <button type="button" class="password-toggle" onclick="togglePassword('edit_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="edit_address" class="required">Address</label>
                            <textarea id="edit_address" name="address" class="form-control" required rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

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

        // Edit user
        function editUser(userId) {
            fetch('edit_user.php?id=' + userId)
                .then(response => response.json())
                .then(user => {
                    document.getElementById('edit_id').value = user.id;
                    document.getElementById('edit_full_name').value = user.full_name;
                    document.getElementById('edit_email').value = user.email;
                    document.getElementById('edit_phone_number').value = user.phone_number;
                    document.getElementById('edit_dob').value = user.dob;
                    document.getElementById('edit_gender').value = user.gender;
                    document.getElementById('edit_role').value = user.role;
                    document.getElementById('edit_address').value = user.address;
                    openEditModal();
                });
        }

        // Delete user function
        function deleteUser(userId) {
            if(confirm('Are you sure you want to delete this user?')) {
                fetch('delete_user.php', {
                    method: 'POST',
                    body: 'id=' + userId,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting user');
                    }
                });
            }
        }

        // Form validation
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateForm()) {
                this.submit();
            }
        });

        function validateForm() {
            let isValid = true;
            
            // Get form elements
            const fullName = document.getElementById('full_name');
            const phone = document.getElementById('phone_number');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            
            // Get error elements
            const nameError = document.getElementById('name_error');
            const phoneError = document.getElementById('phone_error');
            const passwordError = document.getElementById('password_error');
            const confirmPasswordError = document.getElementById('confirm_password_error');
            
            // Reset all error messages
            nameError.textContent = '';
            phoneError.textContent = '';
            passwordError.textContent = '';
            confirmPasswordError.textContent = '';
            
            // Validate full name (only letters and spaces)
            if (!/^[a-zA-Z\s]+$/.test(fullName.value)) {
                nameError.textContent = 'Name should only contain letters and spaces';
                isValid = false;
            }
            
            // Validate phone number (exactly 10 digits)
            if (!/^\d{10}$/.test(phone.value)) {
                phoneError.textContent = 'Phone number must be exactly 10 digits';
                isValid = false;
            }
            
            // Validate password (minimum 6 characters)
            if (password.value.length < 6) {
                passwordError.textContent = 'Password must be at least 6 characters long';
                isValid = false;
            }
            
            // Validate password confirmation
            if (password.value !== confirmPassword.value) {
                confirmPasswordError.textContent = 'Passwords do not match';
                isValid = false;
            }
            
            return isValid;
        }

        // Search functionality
        document.getElementById('searchUsers').addEventListener('keyup', function() {
            let searchValue = this.value.toLowerCase();
            let userCards = document.querySelectorAll('.user-card');
            
            userCards.forEach(card => {
                let text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
