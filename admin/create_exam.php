<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $category_id = $_POST['category_id'];
    $duration = $_POST['duration'];
    $num_questions = $_POST['num_questions']; // Number of random questions to select
    
    $pdo->beginTransaction();
    
    try {
        $stmt = $pdo->prepare("INSERT INTO exams (title, category_id, duration, num_questions) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $category_id, $duration, $num_questions]);
        $exam_id = $pdo->lastInsertId();
        
        // Insert questions
        if (isset($_POST['question_text']) && is_array($_POST['question_text'])) {
            for ($i = 0; $i < count($_POST['question_text']); $i++) {
                $stmt = $pdo->prepare("INSERT INTO questions (exam_id, question_text, option1, option2, option3, option4, correct_answer) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $exam_id,
                    $_POST['question_text'][$i],
                    $_POST['option1'][$i],
                    $_POST['option2'][$i],
                    $_POST['option3'][$i],
                    $_POST['option4'][$i],
                    $_POST['correct_answer'][$i]
                ]);
            }
        }
        
        $pdo->commit();
        $success = "Exam created successfully";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error creating exam: " . $e->getMessage();
    }
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Exam - MCQ Exam System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <h2>Create New Exam</h2>
            <?php 
            if(isset($error)) echo "<p class='error'>$error</p>";
            if(isset($success)) echo "<p class='success'>$success</p>";
            ?>
            <div class="form-container">
                <form method="POST" id="examForm">
                    <div class="form-group">
                        <label>Title:</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Category:</label>
                        <select name="category_id" required>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Duration (minutes):</label>
                        <input type="number" name="duration" required>
                    </div>
                    <div class="form-group">
                        <label>Number of Random Questions to Display:</label>
                        <input type="number" name="num_questions" required>
                        <small>This is the number of questions that will be randomly selected for each student</small>
                    </div>
                    <div id="questions">
                        <h3>Questions</h3>
                        <button type="button" onclick="addQuestion()">Add Question</button>
                        <div id="questions-container"></div>
                    </div>
                    <button type="submit">Create Exam</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function addQuestion() {
        const container = document.getElementById('questions-container');
        const questionDiv = document.createElement('div');
        const index = container.children.length;
        questionDiv.className = 'question-item';
        questionDiv.innerHTML = `
            <div class="form-group">
                <label>Question:</label>
                <textarea name="question_text[]" required></textarea>
            </div>
            <div class="form-group">
                <label>Option 1:</label>
                <input type="text" name="option1[]" required>
            </div>
            <div class="form-group">
                <label>Option 2:</label>
                <input type="text" name="option2[]" required>
            </div>
            <div class="form-group">
                <label>Option 3:</label>
                <input type="text" name="option3[]" required>
            </div>
            <div class="form-group">
                <label>Option 4:</label>
                <input type="text" name="option4[]" required>
            </div>
            <div class="form-group">
                <label>Correct Answer (1-4):</label>
                <input type="number" name="correct_answer[]" min="1" max="4" required>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="btn-danger">Remove Question</button>
            <hr>
        `;
        container.appendChild(questionDiv);
    }
    </script>
</body>
</html> 