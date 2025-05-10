<?php
$host = 'localhost';
$db   = 'alkansave';     // Database name (case-sensitive!)
$user = 'root';          // Default XAMPP username
$pass = '';              // Default XAMPP password (empty)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];
function checkUserRole() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Redirect if not logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.html?error=unauthorized");
        exit;
    }
    
    // Redirect admins away from user pages
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: ../admin_dashboard.html");
        exit;
    }
}
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>