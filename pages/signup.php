<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Get and sanitize inputs
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // 2. Validate data
    $errors = [];

    // Check for existing email
    try {
        $checkEmail = $pdo->prepare("SELECT UserID FROM User WHERE Email = ?");
        $checkEmail->execute([$email]);
        if ($checkEmail->rowCount() > 0) {
            $errors[] = 'duplicate_email';
        }
    } catch (PDOException $e) {
        header("Location: ../signup.html?error=database");
        exit;
    }

    // Other validations
    if (empty($firstName) || empty($lastName)) {
        $errors[] = 'empty_name';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'invalid_email';
    }

    if (strlen($password) < 8) {
        $errors[] = 'short_password';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'password_mismatch';
    }

    // 3. Process if no errors
    if (empty($errors)) {
        try {
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO `User` 
                                  (FirstName, LastName, Email, DOB, PasswordHash) 
                                  VALUES (?, ?, ?, ?, ?)");

            if (!$stmt->execute([$firstName, $lastName, $email, $dob, $passwordHash])) {
                throw new Exception("Failed to execute query");
            }

            // Clear sensitive data
            unset($password, $confirmPassword, $passwordHash);

            // Redirect to login page
            header("Location: ../login.html?signup=success");
            exit;

        } catch (PDOException $e) {
            error_log("Signup Error: " . $e->getMessage());
            header("Location: ../signup.html?error=database_error");
            exit;
        } catch (Exception $e) {
            error_log("Signup Error: " . $e->getMessage());
            header("Location: ../signup.html?error=unknown_error");
            exit;
        }
    } else {
        // Redirect with specific error codes
        $errorParams = [];
        foreach ($errors as $error) {
            $errorParams[] = "error[]=$error";
        }
        header("Location: ../signup.html?" . implode('&', $errorParams) . "&email=" . urlencode($email));
        exit;
    }
}
?>
