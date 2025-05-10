<?php
require_once '../includes/config.php';

// Set response header
header('Content-Type: text/html; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate and sanitize email
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../forgotpass.html?message=invalid_email");
            exit;
        }

        // Check if email exists
        $stmt = $pdo->prepare("SELECT UserID FROM User WHERE Email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate secure token (valid for 1 hour)
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);

            // Store token in database
            $stmt = $pdo->prepare("UPDATE User SET ResetToken = ?, ResetExpires = ? WHERE UserID = ?");
            $stmt->execute([$token, $expires, $user['UserID']]);

            // Create reset link (adjust domain as needed)
            $resetLink = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/pages/reset-password.php?token=$token";
            
            // For now, log the link (replace with actual email in production)
            error_log("Password reset link for $email: $resetLink");
            
            // In production, you would send an email here:
            // $subject = "AlkanSave Password Reset";
            // $message = "Click this link to reset your password: $resetLink";
            // mail($email, $subject, $message);
        }

        // Always show success (security best practice)
        header("Location: ../forgotpass.html?message=reset_sent");
        exit;

    } catch (PDOException $e) {
        error_log("Database error in forgot-password.php: " . $e->getMessage());
        header("Location: ../forgotpass.html?message=error");
        exit;
    }
}

// If not POST request, redirect back
header("Location: ../forgotpass.html");
exit;
?>