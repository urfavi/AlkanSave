<?php
require_once '../includes/config.php';

// Set response header
header('Content-Type: text/html; charset=utf-8');

// Handle GET request (show reset form)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['token'] ?? '';
    
    try {
        // Verify token validity
        $stmt = $pdo->prepare("SELECT UserID FROM User WHERE ResetToken = ? AND ResetExpires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            die('<div style="text-align: center; margin-top: 50px; font-family: Arial;">
                <h2>Invalid or expired reset link</h2>
                <p>Please request a new password reset link.</p>
                <a href="../forgotpass.html" style="color: #4CAF50;">Go to Forgot Password</a>
                </div>');
        }

        // Show password reset form
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reset Password | AlkanSave</title>
            <link rel="stylesheet" href="../css/forgotpass.css">
        </head>
        <body>
            <div class="login-container">
                <div class="left-section">
                    <img src="../images/logo.svg" alt="AlkanSave Logo" class="logo-img">
                    <h1 class="brand-title">
                        <span class="text-alkan">Alkan</span><span class="text-save">Save</span>
                    </h1>
                    <h2 class="welcome-title"><span class="text-alkan">RESET YOUR</span> <span class="text-save">PASSWORD</span></h2>
                    <p class="subtagline">Create a new secure password</p>
                </div>
                <div class="right-section">
                    <div class="form-wrapper">
                        <h2 class="form-title">New Password</h2>
                        <p class="form-subtitle">Enter and confirm your new password</p>
                        <form method="POST" action="reset-password.php" class="login-form">
                            <input type="hidden" name="token" value="$token">
                            <input type="password" name="password" placeholder="New Password" class="input-field" required
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                   title="Must contain at least 8 characters, including uppercase, lowercase, and a number">
                            <input type="password" name="confirm_password" placeholder="Confirm Password" class="input-field" required>
                            <button type="submit" class="login-btn">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </body>
        </html>
        HTML;
        exit;

    } catch (PDOException $e) {
        die('System error. Please try again later.');
    }
}

// Handle POST request (process password update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    try {
        // Verify token first
        $stmt = $pdo->prepare("SELECT UserID FROM User WHERE ResetToken = ? AND ResetExpires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            die('Invalid or expired token. Please request a new reset link.');
        }

        // Validate passwords
        if ($password !== $confirm_password) {
            die('Passwords do not match. Please try again.');
        }

        // Validate password strength
        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
            die('Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.');
        }

        // Hash new password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Update password and clear token
        $stmt = $pdo->prepare("UPDATE User SET PasswordHash = ?, ResetToken = NULL, ResetExpires = NULL WHERE UserID = ?");
        $stmt->execute([$hashedPassword, $user['UserID']]);

        // Redirect to login with success message
        header("Location: ../login.html?message=password_reset_success");
        exit;

    } catch (PDOException $e) {
        error_log("Password reset error: " . $e->getMessage());
        die('System error. Please try again.');
    }
}

// If not GET or POST, redirect
header("Location: ../forgotpass.html");
exit;
?>