<?php
session_start();
require_once '../config/database.php';

$error_message = '';
$success_message = '';
$reset_link = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['forgot_password'])) {
    $email = trim($_POST['email']);
    if (empty($email)) {
        $error_message = 'Please enter your email address.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $admin = $stmt->fetch();

            if ($admin) {
                // Generate a token
                $token = bin2hex(random_bytes(32));

                $update_stmt = $pdo->prepare("UPDATE admin_users SET reset_token = :token, reset_token_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = :id");
                $update_stmt->execute(['token' => $token, 'id' => $admin['id']]);

                // For testing locally, display link. In production, this would be emailed.
                $url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
                $reset_link = $url;
                $success_message = 'Password reset instructions have been generated.';
            } else {
                $error_message = 'No admin account found with that email address.';
            }
        } catch (PDOException $e) {
            $error_message = 'An error occurred. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Admin Panel</title>
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
                <p>Forgot Password</p>
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
                <?php if ($reset_link): ?>
                    <div style="background: #e8f5e9; border: 1px solid #4CAF50; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-size: 0.9em; word-break: break-all;">
                        <strong>[LOCAL TESTING ONLY]</strong><br>
                        Click here to reset your password:<br>
                        <a href="<?php echo htmlspecialchars($reset_link); ?>" style="color: #1565c0; font-weight: 500;"><?php echo htmlspecialchars($reset_link); ?></a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (empty($success_message)): ?>
            <form action="" method="POST" class="login-form">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your registered email" required autofocus>
                </div>
                <button type="submit" name="forgot_password" class="login-btn">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>
            </form>
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
