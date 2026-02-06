<?php
/**
 * Notification Helper Functions
 * Handles creating and managing notifications for users
 */

/**
 * Create a notification for a user
 * 
 * @param mysqli $conn Database connection
 * @param int $user_id User ID to send notification to
 * @param string $title Notification title
 * @param string $message Notification message
 * @param string $type Notification type (booking_accepted, booking_rejected, booking_cancelled, info)
 * @param int|null $booking_id Optional booking ID if notification is related to a booking
 * @return bool True on success, False on failure
 */
function createNotification($conn, $user_id, $title, $message, $type = 'info', $booking_id = null) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, booking_id, title, message, type) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iisss", $user_id, $booking_id, $title, $message, $type);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    } catch (Exception $e) {
        error_log("Error creating notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all notifications for a user
 * 
 * @param mysqli $conn Database connection
 * @param int $user_id User ID
 * @param bool $unread_only If true, only return unread notifications
 * @param int $limit Maximum number of notifications to return
 * @return array Array of notifications
 */
function getUserNotifications($conn, $user_id, $unread_only = false, $limit = 50) {
    try {
        $query = "
            SELECT * FROM notifications 
            WHERE user_id = ? 
        ";
        
        if ($unread_only) {
            $query .= " AND is_read = 0 ";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = [];
        
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        
        $stmt->close();
        return $notifications;
    } catch (Exception $e) {
        error_log("Error getting notifications: " . $e->getMessage());
        return [];
    }
}

/**
 * Get count of unread notifications for a user
 * 
 * @param mysqli $conn Database connection
 * @param int $user_id User ID
 * @return int Number of unread notifications
 */
function getUnreadNotificationCount($conn, $user_id) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'] ?? 0;
    } catch (Exception $e) {
        error_log("Error getting unread notification count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Mark a notification as read
 * 
 * @param mysqli $conn Database connection
 * @param int $notification_id Notification ID
 * @param int $user_id User ID (for security, ensure user owns the notification)
 * @return bool True on success, False on failure
 */
function markNotificationAsRead($conn, $notification_id, $user_id) {
    try {
        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $notification_id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    } catch (Exception $e) {
        error_log("Error marking notification as read: " . $e->getMessage());
        return false;
    }
}

/**
 * Mark all notifications as read for a user
 * 
 * @param mysqli $conn Database connection
 * @param int $user_id User ID
 * @return bool True on success, False on failure
 */
function markAllNotificationsAsRead($conn, $user_id) {
    try {
        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
        $stmt->bind_param("i", $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    } catch (Exception $e) {
        error_log("Error marking all notifications as read: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a notification
 * 
 * @param mysqli $conn Database connection
 * @param int $notification_id Notification ID
 * @param int $user_id User ID (for security)
 * @return bool True on success, False on failure
 */
function deleteNotification($conn, $notification_id, $user_id) {
    try {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE notification_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $notification_id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    } catch (Exception $e) {
        error_log("Error deleting notification: " . $e->getMessage());
        return false;
    }
}

?>
