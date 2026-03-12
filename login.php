<?php
session_start();
require_once 'config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if (isset($_POST['login'])) {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields!';
    } else {
        try {
            // Check user
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_number'] = $user['number'];

                header('Location: index.php');
                exit();
            } else {
                $error = 'Invalid email or password!';
            }
        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            $error = 'An error occurred. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DentalCare</title>
    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- bootstrap link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f9ff;
            padding: 20px;
        }

        .auth-card {
            background: #fff;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .1);
            width: 100%;
            max-width: 450px;
        }

        .auth-card h3 {
            font-size: 2.5rem;
            color: #444;
            text-align: center;
            margin-bottom: 2rem;
            text-transform: capitalize;
        }

        .auth-card .box {
            width: 100%;
            margin: .7rem 0;
            border-radius: .5rem;
            background: #f7f7f7;
            padding: 1.2rem 1.4rem;
            font-size: 1.6rem;
            color: #444;
            text-transform: none;
            border: none;
        }

        .auth-card .link-btn {
            width: 100%;
            margin-top: 1rem;
            cursor: pointer;
        }

        .auth-card p {
            font-size: 1.5rem;
            color: #666;
            margin-top: 1.5rem;
            text-align: center;
        }

        .auth-card p a {
            color: #007bff;
            text-decoration: underline;
        }

        .message {
            padding: 1rem;
            border-radius: .5rem;
            margin-bottom: 1rem;
            font-size: 1.5rem;
            text-align: center;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>

    <div class="auth-container">
        <div class="auth-card">
            <h3>Login to Continue</h3>
            <form action="" method="post">
                <?php if ($error): ?>
                    <p class="message error"><?php echo $error; ?></p>
                <?php endif; ?>

                <input type="email" name="email" placeholder="Enter your email" class="box" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <input type="password" name="password" placeholder="Enter your password" class="box" required>

                <input type="submit" value="login" name="login" class="link-btn">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                <p><a href="index.php">Back to Home</a></p>
            </form>
        </div>
    </div>

</body>

</html>