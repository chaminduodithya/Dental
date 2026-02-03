<?php
// Protect this page
require_once 'includes/auth.php';
require_once '../config/database.php';

$page_title = 'Edit Appointment';

$success_message = '';
$error_message = '';

// Get appointment ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: appointments.php');
    exit();
}

$appointment_id = (int)$_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $number = sanitize_input($_POST['number']);
    $date = sanitize_input($_POST['date']);
    $status = sanitize_input($_POST['status']);
    $notes = sanitize_input($_POST['notes']);
    
    // Validation
    if (empty($name) || empty($email) || empty($number) || empty($date)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE appointments SET name = :name, email = :email, number = :number, date = :date, status = :status, notes = :notes WHERE id = :id");
            
            $result = $stmt->execute([
                'name' => $name,
                'email' => $email,
                'number' => $number,
                'date' => $date,
                'status' => $status,
                'notes' => $notes,
                'id' => $appointment_id
            ]);
            
            if ($result) {
                $success_message = 'Appointment updated successfully!';
                // Redirect after 2 seconds
                header("Refresh: 2; url=appointments.php");
            } else {
                $error_message = 'Failed to update appointment.';
            }
        } catch(PDOException $e) {
            error_log("Update Error: " . $e->getMessage());
            $error_message = 'An error occurred while updating.';
        }
    }
}

// Fetch appointment data
try {
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $appointment_id]);
    $appointment = $stmt->fetch();
    
    if (!$appointment) {
        header('Location: appointments.php');
        exit();
    }
} catch(PDOException $e) {
    error_log("Fetch Error: " . $e->getMessage());
    $error_message = 'Failed to load appointment.';
}

include 'includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Appointment #<?php echo $appointment_id; ?></h2>
    <a href="appointments.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Appointments
    </a>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo htmlspecialchars($success_message); ?>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $appointment_id; ?>" method="POST" class="edit-form">
        <div class="form-row">
            <div class="form-group">
                <label for="name">
                    <i class="fas fa-user"></i> Patient Name <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-input"
                    value="<?php echo htmlspecialchars($appointment['name']); ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email Address <span class="required">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input"
                    value="<?php echo htmlspecialchars($appointment['email']); ?>"
                    required
                >
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="number">
                    <i class="fas fa-phone"></i> Phone Number <span class="required">*</span>
                </label>
                <input 
                    type="tel" 
                    id="number" 
                    name="number" 
                    class="form-input"
                    value="<?php echo htmlspecialchars($appointment['number']); ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="date">
                    <i class="fas fa-calendar"></i> Appointment Date <span class="required">*</span>
                </label>
                <input 
                    type="datetime-local" 
                    id="date" 
                    name="date" 
                    class="form-input"
                    value="<?php echo date('Y-m-d\TH:i', strtotime($appointment['date'])); ?>"
                    required
                >
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="status">
                    <i class="fas fa-info-circle"></i> Status <span class="required">*</span>
                </label>
                <select id="status" name="status" class="form-select" required>
                    <option value="pending" <?php echo $appointment['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $appointment['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="cancelled" <?php echo $appointment['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="completed" <?php echo $appointment['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="notes">
                <i class="fas fa-sticky-note"></i> Notes
            </label>
            <textarea 
                id="notes" 
                name="notes" 
                class="form-textarea" 
                rows="4"
                placeholder="Additional notes or comments..."
            ><?php echo htmlspecialchars($appointment['notes'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="update" class="btn-primary">
                <i class="fas fa-save"></i> Update Appointment
            </button>
            <a href="appointments.php" class="btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
