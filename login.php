<?php
/**
 * LOGIN PAGE WITH SECURITY & GRACEFUL DEGRADATION
 *
 * Security Features:
 * 1. SQL Injection Protection - Uses prepared statements instead of raw SQL
 * 2. Input Validation - Checks if form data exists before processing
 * 3. Error Handling - Gracefully handles database query failures
 *
 * Graceful Degradation:
 * - If database query fails, shows user-friendly error instead of crashing
 * - If session can't be created, still shows appropriate error message
 */

session_start();
include 'db.php';

// SECURITY: Validate input exists before processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // SECURITY: Use prepared statements to prevent SQL injection
    // First, fetch user by username only
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");

    if ($stmt) {
        // Bind username parameter
        mysqli_stmt_bind_param($stmt, "s", $user);

        // Execute query
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            // User found - verify password with bcrypt
            $row = mysqli_fetch_assoc($result);

            // SECURITY: Use password_verify for bcrypt hash comparison
            if (password_verify($pass, $row['password'])) {
                // Valid credentials - create session
                $_SESSION['username'] = $user;
                $_SESSION['role'] = $row['role'];

                // Redirect based on role
                if ($row['role'] == 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: grade.php');
                }
                exit();
            } else {
                // Invalid password
                $error = "Invalid credentials!";
            }
        } else {
            // User not found
            $error = "Invalid credentials!";
        }

        mysqli_stmt_close($stmt);
    } else {
        // GRACEFUL DEGRADATION: Query preparation failed
        $error = "Login service temporarily unavailable. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Grading System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            border: 1px solid #e5e7eb;
        }
        /* Rutgers Logo */
        .logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .logo img {
            max-width: 180px;
            height: auto;
            margin-bottom: 20px;
        }
        h2 {
            color: #000000;
            text-align: center;
            margin-bottom: 8px;
            font-size: 22px;
            font-weight: 600;
        }
        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            color: #374151;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
            background: #ffffff;
            color: #111827;
        }
        input:focus {
            outline: none;
            border-color: #B31414;
        }
        button {
            width: 100%;
            padding: 13px;
            background: #B31414;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
        }
        button:hover {
            background: #8B0F0F;
        }
        .error {
            color: #B31414;
            background: #fee2e2;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #fecaca;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="rutgers-logo.png" alt="Rutgers University">
            <h2>Project Grading System</h2>
            <p class="subtitle">Sign in to access the grading platform</p>
        </div>

        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>
