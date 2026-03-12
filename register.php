<?php
session_start();
require_once 'config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $number = sanitize_input($_POST['number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($number) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format!';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters!';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->rowCount() > 0) {
                $error = 'Email already registered!';
            } else {
                // Register user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, number) VALUES (:name, :email, :password, :number)");
                $result = $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashed_password,
                    'number' => $number
                ]);

                if ($result) {
                    $success = 'Registration successful! You can now login.';
                } else {
                    $error = 'Failed to register. Please try again.';
                }
            }
        } catch (PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
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
    <title>Register - DentalCare</title>
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
            max-width: 500px;
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

        .success {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>

<body>

    <div class="auth-container">
        <div class="auth-card">
            <h3>Create Account</h3>
            <form action="" method="post">
                <?php if ($error): ?>
                    <p class="message error"><?php echo $error; ?></p>
                <?php endif; ?>
                <?php if ($success): ?>
                    <p class="message success"><?php echo $success; ?></p>
                <?php endif; ?>

                <input type="text" name="name" placeholder="Enter your full name" class="box" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                <input type="email" name="email" placeholder="Enter your email" class="box" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <input type="number" name="number" placeholder="Enter your phone number" class="box" required value="<?php echo isset($_POST['number']) ? htmlspecialchars($_POST['number']) : ''; ?>">
                <input type="password" name="password" placeholder="Create password" class="box" required>
                <input type="password" name="confirm_password" placeholder="Confirm password" class="box" required>

                <input type="submit" value="register now" name="register" class="link-btn">
                <p>Already have an account? <a href="login.php">Login here</a></p>
                <p><a href="index.php">Back to Home</a></p>
            </form>
        </div>
    </div>

</body>

</html>