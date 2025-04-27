<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    
    if (!empty($_POST['new_password'])) {
        if (password_verify($_POST['current_password'], $user['password'])) {
            $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $password, $_SESSION['user_id']]);
        } else {
            $error = "Current password is incorrect";
        }
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        $stmt->execute([$full_name, $email, $_SESSION['user_id']]);
    }
    
    if (!isset($error)) {
        $success = "Profile updated successfully";
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile - MCQ Exam System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Profile Settings</h2>
        <?php 
        if(isset($error)) echo "<p class='error'>$error</p>";
        if(isset($success)) echo "<p class='success'>$success</p>";
        ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Current Password:</label>
                <input type="password" name="current_password">
            </div>
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="new_password">
            </div>
            <button type="submit">Update Profile</button>
        </form>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html> 