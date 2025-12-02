<?php
// Database connection
$host = 'localhost';
$dbname = 'grading_system';
$username = 'root';
$password = ''; // Default XAMPP password is empty

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
