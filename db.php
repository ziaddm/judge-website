<?php
// Database connection - works on both XAMPP and Railway
// Railway uses MYSQL_* variables, XAMPP uses defaults
$host = getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'grading_system';
$username = getenv('MYSQL_USER') ?: getenv('DB_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: getenv('DB_PASSWORD') ?: ''; // Default XAMPP password is empty

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
