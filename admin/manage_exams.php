<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'delete') {
            $stmt = $pdo->prepare("DELETE FROM exams WHERE id = ?");
            $stmt->execute([$_POST['id']]);
        }
    }
}

$stmt = $pdo->query("
    SELECT e.*, c.name as category_name, 
    (SELECT COUNT(*) FROM questions WHERE exam_id = e.id) as question_count
    FROM exams e
    LEFT JOIN categories c ON e.category_id = c.id
    ORDER BY e.created_at DESC
");
$exams = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Exams - MCQ Exam System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <h2>Manage Exams</h2>
            <a href="create_exam.php" class="btn">Create New Exam</a>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Duration</th>
                            <th>Questions</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($exams as $exam): ?>
                            <tr>
                                <td><?php echo $exam['id']; ?></td>
                                <td><?php echo htmlspecialchars($exam['title']); ?></td>
                                <td><?php echo htmlspecialchars($exam['category_name']); ?></td>
                                <td><?php echo $exam['duration']; ?> mins</td>
                                <td><?php echo $exam['question_count']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($exam['created_at'])); ?></td>
                                <td class="action-buttons">
                                    <a href="edit_exam.php?id=<?php echo $exam['id']; ?>" class="btn">Edit</a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $exam['id']; ?>">
                                        <button type="submit" onclick="return confirm('Are you sure? This will delete all related questions and results.')" class="btn-danger">Delete</button>
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