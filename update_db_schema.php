<?php
include 'config.php';

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");

    // Alter table column
    $sql = "ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table users updated successfully: password column resized to VARCHAR(255).";
    } else {
        echo "Error updating table: " . $conn->error;
    }
    
    $conn->close();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
