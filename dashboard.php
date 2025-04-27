<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM exams");
$exams = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - MCQ Exam System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Available Exams</h2>
        <div class="exam-list">
            <?php foreach($exams as $exam): ?>
                <div class="exam-item">
                    <h3><?php echo htmlspecialchars($exam['title']); ?></h3>
                    <p>Duration: <?php echo $exam['duration']; ?> minutes</p>
                    <a href="take_exam.php?id=<?php echo $exam['id']; ?>">
                        <button>Start Exam</button>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="logout.php"><button>Logout</button></a>
    </div>
</body>
</html> 