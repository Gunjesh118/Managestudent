<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - MCQ Exam System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <h3>Admin Menu</h3>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_categories.php">Categories</a></li>
                <li><a href="manage_exams.php">Exams</a></li>
                <li><a href="manage_questions.php">Questions</a></li>
                <li><a href="manage_users.php">Users</a></li>
                <li><a href="view_results.php">Results</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="content">
            <h2>Admin Dashboard</h2>
            <div class="stats-grid">
                <div class="stat-box">
                    <h3>Total Users</h3>
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0");
                    echo "<p>" . $stmt->fetchColumn() . "</p>";
                    ?>
                </div>
                <div class="stat-box">
                    <h3>Total Exams</h3>
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM exams");
                    echo "<p>" . $stmt->fetchColumn() . "</p>";
                    ?>
                </div>
                <div class="stat-box">
                    <h3>Total Questions</h3>
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM questions");
                    echo "<p>" . $stmt->fetchColumn() . "</p>";
                    ?>
                </div>
                <div class="stat-box">
                    <h3>Exams Taken</h3>
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM results");
                    echo "<p>" . $stmt->fetchColumn() . "</p>";
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 