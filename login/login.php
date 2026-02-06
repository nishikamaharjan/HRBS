<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    include '../config.php';

    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        header("Location: login.php?error=Invalid email");
        exit();
    }

    $user = $result->fetch_assoc();
    $stored_hash = $user['password'];

    if (password_verify($password, $stored_hash)) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'] === 'Admin' ? 'Admin' : 'User';

        if ($_SESSION['role'] === 'Admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../index.html");
        }
        exit();
    } else {
        header("Location: login.php?error=Invalid password");
        exit();
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1>Login</h1>
            <?php if (isset($_GET['error'])): ?>
                <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form> <br>
            <p>
                <a href="forgotpassword.php" class="forgot-password-link">Forgot Password?</a>
            </p>
            <p>Don't have an account? <a href="register.php">Sign up here</a>.</p>
        </div>
    </div>
</body>
</html>
