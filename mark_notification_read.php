<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit;
}

include 'config.php';
require_once 'notification_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $notification_id = intval($_POST['notification_id']);
    $user_id = $_SESSION['user_id'];
    
    markNotificationAsRead($conn, $notification_id, $user_id);
}

header("Location: profile.php#notifications");
exit;
?>
