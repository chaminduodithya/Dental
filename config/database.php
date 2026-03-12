<?php
/**
 * Database Configuration File
 * Centralizes database connection for the entire application
 */

// Prevent direct access
if (!defined('DB_CONFIG_LOADED')) {
    define('DB_CONFIG_LOADED', true);
}

// ============================================
// XAMPP Local Configuration
// ============================================
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', ''); // Empty password for XAMPP default
define('DB_NAME', 'dental_db');

// ============================================
// Create Database Connection using PDO
// ============================================
try {
    // Adding port=3306 and using 127.0.0.1 forces TCP/IP instead of socket
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=3306;dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch(PDOException $e) {
    // Log error and show detailed message for debugging
    $error = $e->getMessage();
    $host = DB_HOST;
    $db = DB_NAME;
    $user = DB_USER;
    die("<div style='color:red; font-family:sans-serif; padding:20px; border:1px solid red; background:#fff5f5;'>
        <h2>Database Connection Failed (PDO)</h2>
        <p><strong>Error:</strong> $error</p>
        <hr>
        <p><strong>Connection Details:</strong><br>
        Host: $host<br>
        Database: $db<br>
        User: $user</p>
        <p><strong>Troubleshooting:</strong><br>
        1. Ensure MySQL is STARTED in XAMPP.<br>
        2. Verify database '$db' exists in <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a>.<br>
        3. Double check the password in <code>config/database.php</code>.</p>
    </div>");
}

// ============================================
// Create MySQLi Connection (for backward compatibility)
// ============================================
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    $error = mysqli_connect_error();
    die("Database Connection Failed (MySQLi): " . $error . "<br><br>Possible reasons:<br>1. MySQL is not running in XAMPP.<br>2. Database '" . DB_NAME . "' does not exist.<br>3. Credentials are incorrect.");
}

// Set charset
mysqli_set_charset($conn, 'utf8mb4');

/**
 * Helper function to prevent SQL injection
 * @param mysqli $conn Database connection
 * @param string $data Data to escape
 * @return string Escaped data
 */
function escape_string($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

/**
 * Helper function to sanitize input
 * @param string $data Data to sanitize
 * @return string Sanitized data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
?>
