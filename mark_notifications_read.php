<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit;
}

include 'config.php';
require_once 'notification_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    markAllNotificationsAsRead($conn, $user_id);
}

header("Location: profile.php#notifications");
exit;
?>
