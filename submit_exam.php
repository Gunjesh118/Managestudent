<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$exam_id = $_POST['exam_id'];
$answers = $_POST['answers'];

// Get correct answers
$stmt = $pdo->prepare("SELECT id, correct_answer FROM questions WHERE exam_id = ?");
$stmt->execute([$exam_id]);
$questions = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Calculate score
$score = 0;
foreach ($answers as $question_id => $answer) {
    if (isset($questions[$question_id]) && $questions[$question_id] == $answer) {
        $score++;
    }
}

// Save result
$stmt = $pdo->prepare("INSERT INTO results (user_id, exam_id, score) VALUES (?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $exam_id, $score]);

// Redirect to results page
header("Location: result.php?exam_id=" . $exam_id);
exit();
?> 