<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

// Include database connection
require_once '../config/database.php';

$error_message = '';
$success_message = '';

// Check for timeout message
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $error_message = 'Your session has expired. Please login again.';
}

// Check for logout message
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $success_message = 'You have been logged out successfully.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        try {
            // Prepare statement to prevent SQL injection
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['last_activity'] = time();
                
                // Update last login time
                $update_stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = :id");
                $update_stmt->execute(['id' => $admin['id']]);
                
                // Redirect to dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                $error_message = 'Invalid username or password.';
            }
        } catch(PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
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
    <title>Admin Login - DentalCare</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Login CSS -->
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-tooth"></i>
                </div>
                <h1>dental<span>Care</span></h1>
                <p>Admin Panel Login</p>
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
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        Username
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Enter your username" 
                        required 
                        autofocus
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password" 
                        required
                    >
                </div>
                
                <button type="submit" name="login" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </button>
            </form>
            
            <div class="login-footer">
                <p><i class="fas fa-info-circle"></i> Default: admin / admin123</p>
                <a href="../index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Website
                </a>
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
