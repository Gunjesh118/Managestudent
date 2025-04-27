<?php
// Production database configuration for Infinity
$host = 'sql312.infinityfree.com';
$dbname = 'if0_38687049_exam';
$username = 'if0_38687049';
$password = '4KvZYCiRSgmw5Ql';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    die("A database error occurred. Please try again later.");
}
?> 