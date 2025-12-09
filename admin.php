<?php
/**
 * ADMIN DASHBOARD WITH SECURITY & GRACEFUL DEGRADATION
 *
 * Security Features:
 * 1. Session Validation - Only authenticated admins can access
 * 2. No SQL injection risk - Read-only queries with no user input
 *
 * Graceful Degradation:
 * - If no grades exist yet, shows "No data" instead of errors
 * - If query fails, shows admin dashboard with error message
 * - Dashboard remains functional even if stats calculation fails
 */

session_start();

// SECURITY: Session validation - only admin can access
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

include 'db.php';

// GRACEFUL DEGRADATION: Initialize variables with defaults
$result = null;
$stats = ['total_groups' => 0, 'total_grades' => 0, 'overall_avg' => 0];
$db_error = null;

// Get all grades grouped by group number
// Use ANY_VALUE() for non-aggregated columns (fixes strict SQL mode)
$sql = "SELECT group_number,
               ANY_VALUE(group_members) as group_members,
               ANY_VALUE(project_title) as project_title,
               AVG(total) as average_grade,
               COUNT(*) as num_judges
        FROM grades
        GROUP BY group_number
        ORDER BY group_number";
$result = mysqli_query($conn, $sql);

// Calculate overall stats
$stats_sql = "SELECT COUNT(DISTINCT group_number) as total_groups, COUNT(*) as total_grades, AVG(total) as overall_avg FROM grades";
$stats_result = mysqli_query($conn, $stats_sql);

// GRACEFUL DEGRADATION: Handle query failures
if ($stats_result) {
    $stats = mysqli_fetch_assoc($stats_result);
    // Handle case where no grades exist yet
    if ($stats['total_groups'] === null) {
        $stats = ['total_groups' => 0, 'total_grades' => 0, 'overall_avg' => 0];
    }
} else {
    $db_error = "Unable to load statistics. Data may be temporarily unavailable.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            max-width: 1400px;
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

        .header-logo img {
            height: 40px;
            width: auto;
        }

        .header-title h1 {
            font-size: 18px;
            color: #B31414;
            font-weight: 600;
        }

        .header-title p {
            font-size: 13px;
            color: #6b7280;
        }

        .logout-btn {
            padding: 8px 16px;
            background: #B31414;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .logout-btn:hover {
            background: #8B0F0F;
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #ffffff;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .stat-label {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }

        .stat-description {
            color: #B31414;
            font-size: 13px;
        }

        /* Tabs */
        .tabs {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .tab-buttons {
            display: flex;
            border-bottom: 1px solid #e5e7eb;
        }

        .tab-button {
            flex: 1;
            padding: 14px;
            background: #ffffff;
            border: none;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.2s;
        }

        .tab-button.active {
            background: #B31414;
            color: white;
        }

        .tab-button:hover:not(.active) {
            background: #f9fafb;
        }

        .tab-content {
            display: none;
            padding: 30px;
        }

        .tab-content.active {
            display: block;
        }

        .tab-content h2 {
            color: #111827;
            margin-bottom: 8px;
            font-size: 20px;
            font-weight: 600;
        }

        .tab-content > p {
            color: #6b7280;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #B31414;
            color: white;
            padding: 14px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
            font-size: 14px;
        }

        tr {
            background: #ffffff;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        tr:hover {
            background: #f3f4f6;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-outline {
            border: 1px solid #d1d5db;
            color: #6b7280;
        }

        .score-green { color: #10b981; font-weight: 600; }
        .score-blue { color: #3b82f6; font-weight: 600; }

        .no-data {
            text-align: center;
            color: #6b7280;
            font-style: italic;
            padding: 30px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="header-logo">
                    <img src="rutgers-logo.png" alt="Rutgers University">
                </div>
                <div class="header-title">
                    <h1>Admin Dashboard</h1>
                    <p>CS Project Grading System</p>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <!-- Container -->
    <div class="container">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Groups</div>
                <div class="stat-value"><?php echo $stats['total_groups'] ?: 0; ?></div>
                <div class="stat-description">Projects evaluated</div>
            </div>

            <div class="stat-card purple">
                <div class="stat-label">Total Grades</div>
                <div class="stat-value"><?php echo $stats['total_grades'] ?: 0; ?></div>
                <div class="stat-description">Submissions received</div>
            </div>

            <div class="stat-card green">
                <div class="stat-label">Average Score</div>
                <div class="stat-value"><?php echo number_format($stats['overall_avg'] ?: 0, 1); ?></div>
                <div class="stat-description">Out of 60 points</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab-buttons">
                <button class="tab-button active" onclick="switchTab('averages')">Group Averages</button>
                <button class="tab-button" onclick="switchTab('individual')">Individual Grades</button>
            </div>

            <!-- Group Averages Tab -->
            <div class="tab-content active" id="averagesTab">
                <h2>Group Average Scores</h2>
                <p>Average scores calculated from all judges' evaluations</p>

                <table>
                    <thead>
                        <tr>
                            <th>Group #</th>
                            <th>Group Members</th>
                            <th>Project Title</th>
                            <th>Judges</th>
                            <th>Avg Score</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            mysqli_data_seek($result, 0); // Reset pointer
                            while ($row = mysqli_fetch_assoc($result)) {
                                $percentage = ($row['average_grade'] / 60) * 100;
                                $scoreClass = $percentage >= 85 ? 'score-green' : 'score-blue';
                                echo "<tr>";
                                echo "<td>" . $row['group_number'] . "</td>";
                                echo "<td>" . $row['group_members'] . "</td>";
                                echo "<td>" . $row['project_title'] . "</td>";
                                echo "<td><span class='badge badge-outline'>" . $row['num_judges'] . " / 4</span></td>";
                                echo "<td class='$scoreClass'>" . number_format($row['average_grade'], 2) . " / 60</td>";
                                echo "<td class='$scoreClass'>" . number_format($percentage, 1) . "%</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='no-data'>No grades submitted yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Individual Grades Tab -->
            <div class="tab-content" id="individualTab">
                <h2>Individual Judge Grades</h2>
                <p>Detailed breakdown of all grades by judge</p>

                <table>
                    <thead>
                        <tr>
                            <th>Group #</th>
                            <th>Judge</th>
                            <th>Requirements</th>
                            <th>Tools/Methods</th>
                            <th>Presentation</th>
                            <th>Teamwork</th>
                            <th>Total</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Show all individual grades
                        $sql2 = "SELECT * FROM grades ORDER BY group_number, judge_name";
                        $result2 = mysqli_query($conn, $sql2);

                        if (mysqli_num_rows($result2) > 0) {
                            while ($row = mysqli_fetch_assoc($result2)) {
                                $percentage = ($row['total'] / 60) * 100;
                                $scoreClass = $percentage >= 85 ? 'score-green' : 'score-blue';
                                echo "<tr>";
                                echo "<td><span class='badge badge-outline'>Group " . $row['group_number'] . "</span></td>";
                                echo "<td>" . $row['judge_name'] . "</td>";
                                echo "<td>" . $row['articulate_req'] . "/15</td>";
                                echo "<td>" . $row['choose_tools'] . "/15</td>";
                                echo "<td>" . $row['clear_presentation'] . "/15</td>";
                                echo "<td>" . $row['functioned_team'] . "/15</td>";
                                echo "<td class='$scoreClass'>" . $row['total'] . "/60</td>";
                                echo "<td>" . ($row['comments'] ?: '-') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='no-data'>No grades submitted yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Switch between tabs
        function switchTab(tabName) {
            // Get all tab buttons and contents
            var tabButtons = document.querySelectorAll('.tab-button');
            var tabContents = document.querySelectorAll('.tab-content');

            // Remove active class from all
            for (var i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
                tabContents[i].classList.remove('active');
            }

            // Add active class to selected tab
            if (tabName === 'averages') {
                tabButtons[0].classList.add('active');
                document.getElementById('averagesTab').classList.add('active');
            } else {
                tabButtons[1].classList.add('active');
                document.getElementById('individualTab').classList.add('active');
            }
        }
    </script>
</body>
</html>
