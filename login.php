<?php
session_start();
include 'db.php';

// Check if login form submitted
if ($_POST) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Check credentials
    $sql = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
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
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CS Grading System</title>
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
        /* Logo section */
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        h2 {
            color: #111827;
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
            border-color: #2563eb;
        }
        button {
            width: 100%;
            padding: 13px;
            background: #2563eb;
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
            background: #1d4ed8;
        }
        .error {
            color: #dc2626;
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
            <h2>CS Project Grading System</h2>
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
