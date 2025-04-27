<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit();
}

$exam_id = isset($_GET['exam_id']) ? $_GET['exam_id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'delete') {
            $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
            $stmt->execute([$_POST['id']]);
        }
    }
}

$query = "
    SELECT q.*, e.title as exam_title 
    FROM questions q
    JOIN exams e ON q.exam_id = e.id
";
$params = [];

if ($exam_id) {
    $query .= " WHERE q.exam_id = ?";
    $params[] = $exam_id;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$questions = $stmt->fetchAll();

// Get exams for filter
$exams = $pdo->query("SELECT id, title FROM exams ORDER BY title")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Questions - MCQ Exam System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <h2>Manage Questions</h2>
            
            <div class="filters">
                <form method="GET">
                    <select name="exam_id" onchange="this.form.submit()">
                        <option value="">All Exams</option>
                        <?php foreach($exams as $exam): ?>
                            <option value="<?php echo $exam['id']; ?>" <?php echo $exam_id == $exam['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($exam['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Exam</th>
                            <th>Question</th>
                            <th>Options</th>
                            <th>Correct</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($questions as $question): ?>
                            <tr>
                                <td><?php echo $question['id']; ?></td>
                                <td><?php echo htmlspecialchars($question['exam_title']); ?></td>
                                <td><?php echo htmlspecialchars($question['question_text']); ?></td>
                                <td>
                                    1. <?php echo htmlspecialchars($question['option1']); ?><br>
                                    2. <?php echo htmlspecialchars($question['option2']); ?><br>
                                    3. <?php echo htmlspecialchars($question['option3']); ?><br>
                                    4. <?php echo htmlspecialchars($question['option4']); ?>
                                </td>
                                <td><?php echo $question['correct_answer']; ?></td>
                                <td class="action-buttons">
                                    <a href="edit_question.php?id=<?php echo $question['id']; ?>" class="btn">Edit</a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $question['id']; ?>">
                                        <button type="submit" onclick="return confirm('Are you sure?')" class="btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html> 