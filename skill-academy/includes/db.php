<?php
// includes/db.php

$host = 'localhost';
$dbname = 'skill_academy';
$user = 'root';
$pass = '';

try {
    // Implement secure PDO connection
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays by default
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Disable emulation to ensure actual prepared statements are used
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // In production, log the error instead of displaying it to the user
    error_log("Database connection failed: " . $e->getMessage());
    die("A database connection error occurred. Please try again later.");
}
?>
