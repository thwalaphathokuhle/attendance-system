<?php
// Database connection settings for XAMPP (default: no password on root)
$host = "localhost";
$db_name = "attendance_system";
$db_user = "root";
$db_pass = "";

try {
    $conn = new mysqli($host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Optional but recommended: force UTF-8
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>