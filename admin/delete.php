<?php
// Protect this page
require_once 'includes/auth.php';
require_once '../config/database.php';

// Get appointment ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: appointments.php');
    exit();
}

$appointment_id = (int)$_GET['id'];

try {
    // Check if appointment exists
    $check_stmt = $pdo->prepare("SELECT id FROM appointments WHERE id = :id LIMIT 1");
    $check_stmt->execute(['id' => $appointment_id]);
    
    if (!$check_stmt->fetch()) {
        $_SESSION['error_message'] = 'Appointment not found.';
        header('Location: appointments.php');
        exit();
    }
    
    // Delete appointment
    $delete_stmt = $pdo->prepare("DELETE FROM appointments WHERE id = :id");
    $result = $delete_stmt->execute(['id' => $appointment_id]);
    
    if ($result) {
        $_SESSION['success_message'] = 'Appointment deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete appointment.';
    }
    
} catch(PDOException $e) {
    error_log("Delete Error: " . $e->getMessage());
    $_SESSION['error_message'] = 'An error occurred while deleting.';
}

// Redirect back to appointments page
header('Location: appointments.php');
exit();
?>
