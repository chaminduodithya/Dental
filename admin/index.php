<?php
// Redirect to dashboard if already logged in, otherwise show login
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit();
?>
