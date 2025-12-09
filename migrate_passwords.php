<?php
/**
 * PASSWORD MIGRATION SCRIPT
 *
 * Run this ONCE to hash all plaintext passwords in the database.
 * This script uses bcrypt (via password_hash) which is the industry standard.
 *
 * Access: https://your-url.railway.app/migrate_passwords.php
 *
 * IMPORTANT: Delete this file after running!
 */

include 'db.php';

echo "<h1>Password Migration</h1>";
echo "<pre>";

// Get all users with plaintext passwords
$result = mysqli_query($conn, "SELECT id, username, password FROM users");

if (!$result) {
    die("Error fetching users: " . mysqli_error($conn));
}

$updated = 0;
$skipped = 0;

while ($user = mysqli_fetch_assoc($result)) {
    // Check if password is already hashed (bcrypt hashes start with $2y$)
    if (substr($user['password'], 0, 4) === '$2y$') {
        echo "✓ {$user['username']}: Already hashed, skipping\n";
        $skipped++;
        continue;
    }

    // Hash the plaintext password
    $hashed = password_hash($user['password'], PASSWORD_BCRYPT);

    // Update the database
    $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $hashed, $user['id']);

    if (mysqli_stmt_execute($stmt)) {
        echo "✓ {$user['username']}: Password hashed successfully\n";
        $updated++;
    } else {
        echo "✗ {$user['username']}: Failed to update - " . mysqli_error($conn) . "\n";
    }

    mysqli_stmt_close($stmt);
}

echo "\n";
echo "========================================\n";
echo "Migration Complete!\n";
echo "Updated: $updated users\n";
echo "Skipped: $skipped users (already hashed)\n";
echo "========================================\n";
echo "\n⚠️  IMPORTANT: Delete this file (migrate_passwords.php) for security!\n";
echo "</pre>";

mysqli_close($conn);
?>
