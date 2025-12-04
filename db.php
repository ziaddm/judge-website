<?php
/**
 * DATABASE CONNECTION WITH GRACEFUL DEGRADATION
 *
 * This file demonstrates "graceful degradation" - if the database fails,
 * we show a user-friendly error page instead of crashing the application.
 *
 * Key Features:
 * 1. Environment variable fallbacks (works on XAMPP and Railway)
 * 2. Connection error handling without exposing sensitive details
 * 3. User-friendly error page if database is unavailable
 */

// Get database credentials from environment variables with fallbacks
$host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'railway';
$username = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: getenv('DB_USER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: getenv('DB_PASSWORD') ?: '';
$port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: 3306;

// GRACEFUL DEGRADATION: Instead of crashing with die(), we handle errors gracefully
$conn = null;
$db_error = null;

try {
    if (!function_exists('mysqli_connect')) {
        throw new Exception("Database extension not available");
    }

    // Attempt connection
    $conn = @mysqli_connect($host, $username, $password, $dbname, $port);

    if (!$conn) {
        throw new Exception("Could not connect to database");
    }
} catch (Exception $e) {
    // Store error but don't expose sensitive details to users
    $db_error = $e->getMessage();

    // GRACEFUL DEGRADATION: Show user-friendly error page
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Service Unavailable</title>
        <style>
            body {
                font-family: 'Segoe UI', Arial, sans-serif;
                background: #f5f7fa;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
                padding: 20px;
            }
            .error-container {
                background: white;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                max-width: 500px;
                text-align: center;
            }
            h1 { color: #dc2626; margin-bottom: 20px; }
            p { color: #6b7280; margin-bottom: 30px; line-height: 1.6; }
            .btn {
                display: inline-block;
                padding: 12px 24px;
                background: #2563eb;
                color: white;
                text-decoration: none;
                border-radius: 6px;
                font-weight: 600;
            }
            .btn:hover { background: #1d4ed8; }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>Service Temporarily Unavailable</h1>
            <p>We're experiencing technical difficulties with our database connection. The system is degraded but will be back shortly.</p>
            <p><strong>What this means:</strong> The database server may be restarting or undergoing maintenance. Please try again in a few moments.</p>
            <a href="javascript:location.reload()" class="btn">Try Again</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Connection successful - mysqli_real_escape_string and prepared statements now available
?>
