<?php
/**
 * DATABASE SETUP SCRIPT
 *
 * Run this ONCE to create tables and insert initial users.
 * Access this by going to: https://your-railway-url.up.railway.app/setup.php
 *
 * IMPORTANT: Delete this file after running it for security!
 */

include 'db.php';

echo "<h1>Database Setup</h1>";
echo "<pre>";

// Create users table
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(20)
)";

if (mysqli_query($conn, $sql_users)) {
    echo "✓ Users table created successfully\n";
} else {
    echo "✗ Error creating users table: " . mysqli_error($conn) . "\n";
}

// Create grades table
$sql_grades = "CREATE TABLE IF NOT EXISTS grades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_members TEXT,
    project_title VARCHAR(255),
    group_number VARCHAR(50),
    articulate_req INT,
    choose_tools INT,
    clear_presentation INT,
    functioned_team INT,
    total INT,
    judge_name VARCHAR(100),
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql_grades)) {
    echo "✓ Grades table created successfully\n";
} else {
    echo "✗ Error creating grades table: " . mysqli_error($conn) . "\n";
}

// Check if users already exist
$check = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
$row = mysqli_fetch_assoc($check);

if ($row['count'] == 0) {
    // Insert default users using prepared statement
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");

    $users = [
        ['judge1', '123', 'judge'],
        ['judge2', '123', 'judge'],
        ['judge3', '123', 'judge'],
        ['judge4', '123', 'judge'],
        ['admin', '123', 'admin']
    ];

    foreach ($users as $user) {
        mysqli_stmt_bind_param($stmt, "sss", $user[0], $user[1], $user[2]);
        if (mysqli_stmt_execute($stmt)) {
            echo "✓ Created user: {$user[0]}\n";
        } else {
            echo "✗ Error creating user {$user[0]}: " . mysqli_error($conn) . "\n";
        }
    }

    mysqli_stmt_close($stmt);
} else {
    echo "ℹ Users already exist, skipping user creation\n";
}

echo "\n✅ Database setup complete!\n";
echo "\nYou can now login with:\n";
echo "- judge1 / 123\n";
echo "- judge2 / 123\n";
echo "- judge3 / 123\n";
echo "- judge4 / 123\n";
echo "- admin / 123\n";
echo "\n⚠️  IMPORTANT: Delete this setup.php file for security!\n";
echo "</pre>";

mysqli_close($conn);
?>
