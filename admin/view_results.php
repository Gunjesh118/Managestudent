<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit();
}

$exam_id = isset($_GET['exam_id']) ? $_GET['exam_id'] : null;
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

$query = "
    SELECT r.*, e.title as exam_title, u.username, u.full_name,
    (SELECT COUNT(*) FROM questions WHERE exam_id = e.id) as total_questions
    FROM results r
    JOIN exams e ON r.exam_id = e.id
    JOIN users u ON r.user_id = u.id
    WHERE 1=1
";
$params = [];

if ($exam_id) {
    $query .= " AND r.exam_id = ?";
    $params[] = $exam_id;
}

if ($user_id) {
    $query .= " AND r.user_id = ?";
    $params[] = $user_id;
}

$query .= " ORDER BY r.completed_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll();

// Get exams and users for filters
$exams = $pdo->query("SELECT id, title FROM exams ORDER BY title")->fetchAll();
$users = $pdo->query("SELECT id, username, full_name FROM users ORDER BY username")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Results - MCQ Exam System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <h2>Exam Results</h2>
            
            <div class="filters">
                <form method="GET">
                    <select name="exam_id">
                        <option value="">All Exams</option>
                        <?php foreach($exams as $exam): ?>
                            <option value="<?php echo $exam['id']; ?>" <?php echo $exam_id == $exam['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($exam['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="user_id">
                        <option value="">All Users</option>
                        <?php foreach($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo $user_id == $user['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($user['username']); ?>
                                (<?php echo htmlspecialchars($user['full_name']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="submit">Filter</button>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Exam</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($results as $result): ?>
                            <tr>
                                <td><?php echo $result['id']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($result['username']); ?>
                                    (<?php echo htmlspecialchars($result['full_name']); ?>)
                                </td>
                                <td><?php echo htmlspecialchars($result['exam_title']); ?></td>
                                <td><?php echo $result['score']; ?> / <?php echo $result['total_questions']; ?></td>
                                <td><?php echo round(($result['score'] / $result['total_questions']) * 100, 2); ?>%</td>
                                <td><?php echo date('Y-m-d H:i', strtotime($result['completed_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html> 