<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$exam_id = $_GET['id'];

// First get the exam details
$stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();

if (!$exam) {
    header("Location: dashboard.php");
    exit();
}

// Get random questions for this exam
$stmt = $pdo->prepare("
    SELECT * FROM questions 
    WHERE exam_id = :exam_id 
    ORDER BY RAND() 
    LIMIT :limit
");
$stmt->bindValue(':exam_id', $exam_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', (int)$exam['num_questions'], PDO::PARAM_INT);
$stmt->execute();
$questions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Exam - MCQ Exam System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="timer" id="timer"></div>
    <div class="container">
        <h2><?php echo htmlspecialchars($exam['title']); ?></h2>
        <form id="examForm" method="POST" action="submit_exam.php">
            <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
            <?php foreach($questions as $index => $question): ?>
                <div class="question">
                    <p><?php echo ($index + 1) . ". " . htmlspecialchars($question['question_text']); ?></p>
                    <div class="options">
                        <label>
                            <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="1" required>
                            <?php echo htmlspecialchars($question['option1']); ?>
                        </label>
                        <label>
                            <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="2" required>
                            <?php echo htmlspecialchars($question['option2']); ?>
                        </label>
                        <label>
                            <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="3" required>
                            <?php echo htmlspecialchars($question['option3']); ?>
                        </label>
                        <label>
                            <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="4" required>
                            <?php echo htmlspecialchars($question['option4']); ?>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit">Submit Exam</button>
        </form>
    </div>

    <script>
        // Timer functionality
        let timeLeft = <?php echo $exam['duration'] * 60; ?>;
        const timerElement = document.getElementById('timer');
        
        const timer = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft === 0) {
                document.getElementById('examForm').submit();
            }
            
            timeLeft--;
        }, 1000);
    </script>
</body>
</html> 