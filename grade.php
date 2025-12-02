<?php
session_start();
// Make sure only logged in judges can access
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'judge') {
    header('Location: login.php');
    exit();
}

include 'db.php';

// Handle form submission
if ($_POST && isset($_POST['submit_grade'])) {
    $group_members = $_POST['group_members'];
    $project_title = $_POST['project_title'];
    $group_number = $_POST['group_number'];
    $articulate = $_POST['articulate_req'];
    $tools = $_POST['choose_tools'];
    $presentation = $_POST['clear_presentation'];
    $team = $_POST['functioned_team'];
    $total = $articulate + $tools + $presentation + $team; // Calculate total
    $judge_name = $_SESSION['username'];
    $comments = $_POST['comments'];

    // Save to database
    $sql = "INSERT INTO grades (group_members, project_title, group_number, articulate_req, choose_tools, clear_presentation, functioned_team, total, judge_name, comments)
            VALUES ('$group_members', '$project_title', '$group_number', $articulate, $tools, $presentation, $team, $total, '$judge_name', '$comments')";

    if (mysqli_query($conn, $sql)) {
        $success = "Grade submitted successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Grading Form</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            padding-bottom: 50px;
        }

        /* Header */
        .header {
            background: #ffffff;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border-bottom: 1px solid #e5e7eb;
        }

        .header-content {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }


        .header-title h1 {
            font-size: 18px;
            color: #111827;
            font-weight: 600;
        }

        .header-title p {
            font-size: 13px;
            color: #6b7280;
        }

        .logout-btn {
            padding: 8px 16px;
            background: #dc2626;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .logout-btn:hover {
            background: #b91c1c;
        }

        /* Main Container */
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Success/Error messages */
        .success-message {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 14px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .error-message {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 14px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .form-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .form-header {
            background: #2563eb;
            color: white;
            padding: 24px 30px;
        }

        .form-header h2 {
            font-size: 20px;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .form-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .form-body {
            padding: 30px;
        }

        .section-title {
            font-size: 16px;
            color: #111827;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #ffffff;
            color: #111827;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus {
            outline: none;
            border-color: #2563eb;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* Criteria boxes */
        .criteria-box {
            padding: 18px;
            border-radius: 6px;
            margin-bottom: 14px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .criteria-box label {
            color: #111827;
            font-size: 14px;
        }

        .score-input-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .score-input-group input {
            width: 90px;
            font-size: 15px;
            font-weight: 600;
        }

        .score-input-group span {
            color: #6b7280;
            font-size: 14px;
        }

        /* Total Score Display */
        .total-score {
            background: #2563eb;
            color: white;
            padding: 20px;
            border-radius: 6px;
            margin: 24px 0;
        }

        .total-score-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .total-score-label {
            font-size: 16px;
            font-weight: 600;
        }

        .total-score-value {
            font-size: 32px;
            font-weight: 700;
        }

        .progress-bar {
            background: rgba(255, 255, 255, 0.2);
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            background: white;
            height: 100%;
            transition: width 0.3s ease;
        }

        /* Submit Button */
        .submit-btn {
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
        }

        .submit-btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="header-title">
                    <h1>CS Project Grading</h1>
                    <p>Logged in as: <strong><?php echo $_SESSION['username']; ?></strong></p>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <?php if (isset($success)) echo "<div class='success-message'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='error-message'>$error</div>"; ?>

        <div class="form-card">
            <div class="form-header">
                <h2>Project Grading Form</h2>
                <p>Please evaluate the student project presentation</p>
            </div>

            <div class="form-body">
                <form method="POST">
                    <!-- Project Information Section -->
                    <h3 class="section-title">Project Information</h3>

                    <div class="form-group">
                        <label for="group_number">Group Number *</label>
                        <input type="text" id="group_number" name="group_number" required>
                    </div>

                    <div class="form-group">
                        <label for="project_title">Project Title *</label>
                        <input type="text" id="project_title" name="project_title" required>
                    </div>

                    <div class="form-group">
                        <label for="group_members">Group Members *</label>
                        <textarea id="group_members" name="group_members" placeholder="Enter group member names" required></textarea>
                    </div>

                    <!-- Grading Criteria Section -->
                    <h3 class="section-title">Grading Criteria (0-15 points each)</h3>

                    <div class="criteria-box">
                        <label for="articulate_req">1. Articulate Requirements</label>
                        <div class="score-input-group">
                            <input type="number" id="articulate_req" name="articulate_req" min="0" max="15" value="0" oninput="calculateTotal()" required>
                            <span>/ 15 points</span>
                        </div>
                    </div>

                    <div class="criteria-box">
                        <label for="choose_tools">2. Choose Appropriate Tools/Methods</label>
                        <div class="score-input-group">
                            <input type="number" id="choose_tools" name="choose_tools" min="0" max="15" value="0" oninput="calculateTotal()" required>
                            <span>/ 15 points</span>
                        </div>
                    </div>

                    <div class="criteria-box">
                        <label for="clear_presentation">3. Clear Oral Presentation</label>
                        <div class="score-input-group">
                            <input type="number" id="clear_presentation" name="clear_presentation" min="0" max="15" value="0" oninput="calculateTotal()" required>
                            <span>/ 15 points</span>
                        </div>
                    </div>

                    <div class="criteria-box">
                        <label for="functioned_team">4. Functioned Well as a Team</label>
                        <div class="score-input-group">
                            <input type="number" id="functioned_team" name="functioned_team" min="0" max="15" value="0" oninput="calculateTotal()" required>
                            <span>/ 15 points</span>
                        </div>
                    </div>

                    <!-- Total Score Display -->
                    <div class="total-score">
                        <div class="total-score-content">
                            <span class="total-score-label">Total Score:</span>
                            <span class="total-score-value"><span id="totalScore">0</span> / 60</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="form-group">
                        <label for="comments">Additional Comments</label>
                        <textarea id="comments" name="comments" placeholder="Enter any additional comments or feedback..."></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" name="submit_grade" class="submit-btn">Submit Grade</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Calculate total score and update progress bar
        function calculateTotal() {
            var art = parseInt(document.getElementById('articulate_req').value) || 0;
            var tools = parseInt(document.getElementById('choose_tools').value) || 0;
            var pres = parseInt(document.getElementById('clear_presentation').value) || 0;
            var team = parseInt(document.getElementById('functioned_team').value) || 0;

            var total = art + tools + pres + team;

            // Update total score display
            document.getElementById('totalScore').textContent = total;

            // Update progress bar
            var percentage = (total / 60) * 100;
            document.getElementById('progressFill').style.width = percentage + '%';
        }
    </script>
</body>
</html>
