<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$user['id'], $token, $expires]);
        
        // In a real application, you would send an email here
        $resetLink = "http://yourwebsite.com/reset_password.php?token=" . $token;
        $success = "Password reset link has been sent to your email";
    } else {
        $error = "Email not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - MCQ Exam System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <?php 
        if(isset($error)) echo "<p class='error'>$error</p>";
        if(isset($success)) echo "<p class='success'>$success</p>";
        ?>
        <form method="POST">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <button type="submit">Reset Password</button>
        </form>
        <p><a href="login.php">Back to Login</a></p>
    </div>
</body>
</html> 