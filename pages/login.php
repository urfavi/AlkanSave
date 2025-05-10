<?php
require_once '../includes/config.php';
session_start();

// Debugging - log POST data
error_log("Login attempt - Email: " . $_POST['email']);

try {
    // 1. Get inputs
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 2. Validate inputs
    if (empty($email) || empty($password)) {
        error_log("Empty fields detected");
        header("Location: ../login.html?error=missing_fields&email=" . urlencode($email));
        exit;
    }

    // 3. Find user by email - FIXED QUERY (added comma)
    $stmt = $pdo->prepare("SELECT UserID, PasswordHash, Role FROM User WHERE Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Debugging - log database results
    error_log("Database returned: " . print_r($user, true));

    // 4. Verify credentials
    if ($user) {
        error_log("Stored hash: " . $user['PasswordHash']);
        $isValid = password_verify($password, $user['PasswordHash']);
        error_log("Password verify result: " . ($isValid ? "VALID" : "INVALID"));
        
        if ($isValid) {
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['role'] = $user['Role'];
            
            // Debugging
            error_log("Login success - Role: " . $user['Role']);
            
            // Redirect based on role
            $redirect = ($user['Role'] === 'admin') 
                ? "../admin_dashboard.html" 
                : "../user_home.html";
            header("Location: " . $redirect);
            exit;
        }
    }

    // If we reach here, login failed
    error_log("Login failed for email: " . $email);
    header("Location: ../login.html?error=invalid_credentials&email=" . urlencode($email));
    exit;

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header("Location: ../login.html?error=database_error");
    exit;
}
?>