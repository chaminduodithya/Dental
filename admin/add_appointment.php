<?php
// Protect this page
require_once 'includes/auth.php';
require_once '../config/database.php';

$page_title = 'Add New Appointment';

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    
    $name   = trim($_POST['name']);
    $email  = trim($_POST['email']);
    $number = trim($_POST['number']);
    $date   = $_POST['date'];
    $status = $_POST['status'];
    $notes  = trim($_POST['notes']);

    // Validation
    if (empty($name) || empty($email) || empty($number) || empty($date)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            // Check if user exists with this email to link user_id
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();
            $user_id = $user ? $user['id'] : null;

            $stmt = $pdo->prepare("INSERT INTO appointments (user_id, name, email, number, date, status, notes) VALUES (:user_id, :name, :email, :number, :date, :status, :notes)");

            $result = $stmt->execute([
                'user_id' => $user_id,
                'name'    => $name,
                'email'   => $email,
                'number'  => $number,
                'date'    => $date,
                'status'  => $status,
                'notes'   => $notes
            ]);

            if ($result) {
                $success_message = 'Appointment created successfully!';
                // Redirect after some time
                header("Refresh: 2; url=appointments.php");
            } else {
                $error_message = 'Failed to create appointment.';
            }
        } catch (PDOException $e) {
            error_log("Insert Error: " . $e->getMessage());
            $error_message = 'An error occurred while saving the appointment.';
        }
    }
}

include 'includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Add New Appointment</h2>
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
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="edit-form">
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
                    placeholder="Enter full name"
                    value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                    required>
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
                    placeholder="Enter email address"
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                    required>
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
                    placeholder="Enter contact number"
                    value="<?php echo isset($number) ? htmlspecialchars($number) : ''; ?>"
                    required>
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
                    value="<?php echo isset($date) ? htmlspecialchars($date) : ''; ?>"
                    required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="status">
                    <i class="fas fa-info-circle"></i> Status <span class="required">*</span>
                </label>
                <select id="status" name="status" class="form-select" required>
                    <option value="pending" <?php echo (isset($status) && $status === 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo (isset($status) && $status === 'confirmed') ? 'selected' : 'selected'; ?>>Confirmed</option>
                    <option value="cancelled" <?php echo (isset($status) && $status === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="completed" <?php echo (isset($status) && $status === 'completed') ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="notes">
                <i class="fas fa-sticky-note"></i> Internal Notes
            </label>
            <textarea
                id="notes"
                name="notes"
                class="form-textarea"
                rows="4"
                placeholder="Add any internal dental notes or special requests..."><?php echo isset($notes) ? htmlspecialchars($notes) : ''; ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" name="submit" class="btn-primary">
                <i class="fas fa-calendar-plus"></i> Create Appointment
            </button>
            <a href="appointments.php" class="btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>