<?php
// Database connection - works on both XAMPP and Railway
// Railway uses MYSQL_* variables, XAMPP uses defaults
$host = getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'railway';
$username = getenv('MYSQL_USER') ?: getenv('DB_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: getenv('DB_PASSWORD') ?: ''; // Default XAMPP password is empty
$port = getenv('MYSQL_PORT') ?: 3306;

// Try mysqli connection
if (function_exists('mysqli_connect')) {
    $conn = @mysqli_connect($host, $username, $password, $dbname, $port);
    if (!$conn) {
        die("Connection failed to $host:$port - " . mysqli_connect_error() . " (Database: $dbname)");
    }
} else {
    die("mysqli extension not available. Please install php-mysqli.");
}
?>
