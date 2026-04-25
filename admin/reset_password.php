<?php
session_start();
require_once '../config/database.php';

$error_message = '';
$success_message = '';
$valid_token = false;
$admin_id = null;

if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    try {
        $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE reset_token = :token AND reset_token_expires > NOW() LIMIT 1");
        $stmt->execute(['token' => $token]);
        $admin = $stmt->fetch();

        if ($admin) {
            $valid_token = true;
            $admin_id = $admin['id'];
        } else {
            $error_message = 'Invalid or expired reset token. Please request a new one.';
        }
    } catch (PDOException $e) {
        $error_message = 'An error occurred. Please try again.';
    }
} else {
    $error_message = 'No reset token provided. Please use the link from the forgot password page.';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password']) && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($password) || empty($confirm_password)) {
        $error_message = 'Please fill out all fields.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET password = :password, reset_token = NULL, reset_token_expires = NULL WHERE id = :id");
            $stmt->execute(['password' => $hashed_password, 'id' => $admin_id]);

            $success_message = 'Your password has been successfully reset. You can now log in.';
            $valid_token = false; 
        } catch (PDOException $e) {
            $error_message = 'An error occurred while resetting the password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="logo"><i class="fas fa-tooth"></i></div>
                <h1>dental<span>Care</span></h1>
                <p>Reset Password</p>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="login.php" class="login-btn" style="text-decoration:none; display:inline-block; margin-bottom:10px;">Proceed to Login</a>
                </div>
            <?php endif; ?>

            <?php if ($valid_token && empty($success_message)): ?>
            <form action="" method="POST" class="login-form">
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> New Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter new password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                </div>
                <button type="submit" name="reset_password" class="login-btn">
                    <i class="fas fa-save"></i> Reset Password
                </button>
            </form>
            <?php endif; ?>

            <?php if (!$valid_token && empty($success_message)): ?>
                <div style="text-align: center; margin-top: 15px;">
                    <a href="forgot_password.php" class="login-btn" style="text-decoration:none; display:inline-block; background-color:#6c757d; border-color:#6c757d; margin-bottom:10px;">Request New Token</a>
                </div>
            <?php endif; ?>

            <div class="login-footer">
                <a href="login.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Login</a>
            </div>
        </div>
        <div class="background-animation">
            <div class="tooth-icon tooth-1"><i class="fas fa-tooth"></i></div>
            <div class="tooth-icon tooth-2"><i class="fas fa-tooth"></i></div>
            <div class="tooth-icon tooth-3"><i class="fas fa-tooth"></i></div>
        </div>
    </div>
</body>
</html>
