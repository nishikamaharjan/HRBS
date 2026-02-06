<?php
// Database connection - uses local variables to avoid conflicts
function getDbConnection() {
    static $conn = null;
    
    if ($conn === null) {
        $host = 'localhost';
        $dbname = 'hrs';
        $username = 'root';
        $db_password = '';
        
        $conn = new mysqli($host, $username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
    }
    
    return $conn;
}

// For backward compatibility
$conn = getDbConnection();
?>
