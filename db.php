<?php
// Database connection - works on both XAMPP and Railway
// Railway uses MYSQL_* variables, XAMPP uses defaults
$host = getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'grading_system';
$username = getenv('MYSQL_USER') ?: getenv('DB_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: getenv('DB_PASSWORD') ?: ''; // Default XAMPP password is empty

// Debug: Show connection details (remove this in production!)
if ($host === 'localhost' && isset($_SERVER['RAILWAY_ENVIRONMENT'])) {
    die("ERROR: MySQL database not found. Please add a MySQL database in Railway and link it to this service. Current host: $host");
}

// Try mysqli connection
if (function_exists('mysqli_connect')) {
    $conn = @mysqli_connect($host, $username, $password, $dbname);
    if (!$conn) {
        die("Connection failed to $host: " . mysqli_connect_error());
    }
} else {
    die("mysqli extension not available. Please install php-mysqli.");
}
?>
