<?php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '1134206');
define('DB_NAME', 'work_orders');
define('DB_CHARSET', 'utf8mb4');

// Create a new PDO instance
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to database: ' . $e->getMessage());
}
?>