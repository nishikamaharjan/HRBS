<?php
// signup.php
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $dob = $_POST['dob'];
    $gen = $_POST['gender']; // Renamed to avoid confusion with valid variable if needed, but original code used $gender
    $gender = $_POST['gender'];
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Validate name (only letters and spaces)
    if (!preg_match('/^[a-zA-Z\s]+$/', $fullName)) {
        $error_message = "Full name should only contain letters and spaces.";
    }
    // Validate date of birth (not today or future)
    elseif (!empty($dob)) {
        $current_date = new DateTime('now');
        $dob_date = new DateTime($dob);
        if ($dob_date >= $current_date) {
            $error_message = "Date of birth cannot be today or in the future.";
        }
    }
    
    // Validate phone number (only digits, exactly 10 characters)
    if (empty($error_message) && !preg_match('/^\d{10}$/', $phone)) {
        $error_message = "Invalid phone number. It should contain exactly 10 digits.";
    }
    
    // Validate password (minimum 6 characters)
    if (empty($error_message) && strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    }
    
    // Password must match confirmation
    if (empty($error_message) && $password !== $confirmPassword) {
        $error_message = "Passwords do not match.";
    }
    
    // If no validation errors, proceed with database operations
    if (empty($error_message)) {
        // Database connection
        include '../config.php';

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Email is already registered.";
        } else {
            if ($stmt->num_rows > 0) {
                $error_message = "Email is already registered.";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert user into database
                $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, dob, gender, password) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $fullName, $email, $phone, $dob, $gender, $hashedPassword);

                if ($stmt->execute()) {
                    $success_message = "Registration successful please login";
                } else {
                    $error_message = "Error: " . $stmt->error;
                }
            }

            // Close connection
            $stmt->close();
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .signup-form {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .signup-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555555;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #007bff;
        }
        .submit-button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: #007bff;
            color: #ffffff;
            font-size: 16px;
            cursor: pointer;
        }
        .submit-button:hover {
            background: #0056b3;
        }
        .link {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
        }
        .link a {
            color: #007bff;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            font-size: 14px;
        }
        /* Modal Popup Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.show {
            display: flex;
        }
        .modal-popup {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            text-align: center;
        }
        .modal-popup h3 {
            color: #28a745;
            margin-bottom: 15px;
            font-size: 24px;
        }
        .modal-popup p {
            color: #333;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .modal-popup .btn-login {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background 0.3s;
            cursor: pointer;
        }
        .modal-popup .btn-login:hover {
            background: #0056b3;
        }
        .error-message {
            color: red;
            font-size: 14px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
    <script>
        function validateForm() {
            let fullName = document.getElementById("full_name").value;
            let namePattern = /^[a-zA-Z\s]+$/;
            let phone = document.getElementById("phone_number").value;
            let phonePattern = /^\d{10}$/;
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;
            let dob = document.getElementById("dob").value;
            let nameError = document.getElementById("name_error");
            let passwordError = document.getElementById("password_error");
            let phoneError = document.getElementById("phone_error");
            let dobError = document.getElementById("dob_error");

            // Validate full name
            if (!namePattern.test(fullName)) {
                nameError.textContent = "Name should only contain letters and spaces.";
                return false;
            } else {
                nameError.textContent = "";
            }

            // Validate date of birth
            let dobDate = new Date(dob);
            let today = new Date();
            today.setHours(0, 0, 0, 0); // Set time to midnight for date comparison
            if (dobDate >= today) {
                dobError.textContent = "Date of birth cannot be today or in the future.";
                return false;
            } else {
                dobError.textContent = "";
            }

            // Validate phone number
            if (!phonePattern.test(phone)) {
                phoneError.textContent = "Phone number must be exactly 10 digits.";
                return false;
            } else {
                phoneError.textContent = "";
            }

            // Validate password length
            if (password.length < 6) {
                passwordError.textContent = "Password must be at least 6 characters long.";
                return false;
            } else {
                passwordError.textContent = "";
            }

            // Validate password match
            if (password !== confirmPassword) {
                passwordError.textContent = "Passwords do not match.";
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <!-- Success Modal Popup -->
    <?php if (!empty($success_message)): ?>
    <div class="modal-overlay show" id="successModal">
        <div class="modal-popup">
            <h3>✓ Success!</h3>
            <p><?php echo htmlspecialchars($success_message); ?></p>
            <a href="login.php" class="btn-login">Go to Login</a>
        </div>
    </div>
    <?php endif; ?>
    
    <form action="register.php" method="POST" class="signup-form" onsubmit="return validateForm()">
        <h2>Signup</h2>
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required>
            <span class="error" id="name_error"></span>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" required>
            <span class="error" id="phone_error"></span>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" required>
            <span class="error" id="dob_error"></span>
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <span class="error" id="password_error"></span>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="submit-button">Sign Up</button>
        <div class="link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </form>
</body>
</html>
