<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$exam_id = $_GET['exam_id'];

$stmt = $pdo->prepare("
    SELECT r.*, e.title, 
    (SELECT COUNT(*) FROM questions WHERE exam_id = e.id) as total_questions
    FROM results r
    JOIN exams e ON r.exam_id = e.id
    WHERE r.user_id = ? AND r.exam_id = ?
    ORDER BY r.completed_at DESC
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id'], $exam_id]);
$result = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Exam Result - MCQ Exam System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Exam Result</h2>
        <div class="result">
            <h3><?php echo htmlspecialchars($result['title']); ?></h3>
            <p>Score: <?php echo $result['score']; ?> out of <?php echo $result['total_questions']; ?></p>
            <p>Percentage: <?php echo round(($result['score'] / $result['total_questions']) * 100, 2); ?>%</p>
        </div>
        <a href="dashboard.php"><button>Back to Dashboard</button></a>
    </div>
</body>
</html> 