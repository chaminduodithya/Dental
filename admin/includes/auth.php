<?php
/**
 * Authentication Check
 * Include this file at the top of every admin page to protect it
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page
    header('Location: login.php');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Session timeout after 30 minutes of inactivity
$timeout_duration = 1800; // 30 minutes in seconds

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session expired
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit();
}
?>
