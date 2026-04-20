<?php
// Protect this page
require_once 'includes/auth.php';
require_once '../config/database.php';

$page_title = 'Dashboard';

// Get statistics
try {
    // Total appointments
    $total_stmt = $pdo->query("SELECT COUNT(*) as total FROM appointments");
    $total_appointments = $total_stmt->fetch()['total'];

    // Pending appointments
    $pending_stmt = $pdo->query("SELECT COUNT(*) as total FROM appointments WHERE status = 'pending'");
    $pending_appointments = $pending_stmt->fetch()['total'];

    // Confirmed appointments
    $confirmed_stmt = $pdo->query("SELECT COUNT(*) as total FROM appointments WHERE status = 'confirmed'");
    $confirmed_appointments = $confirmed_stmt->fetch()['total'];

    // Completed appointments
    $completed_stmt = $pdo->query("SELECT COUNT(*) as total FROM appointments WHERE status = 'completed'");
    $completed_appointments = $completed_stmt->fetch()['total'];

    // Recent appointments (last 5)
    $recent_stmt = $pdo->query("SELECT * FROM appointments ORDER BY created_at DESC LIMIT 5");
    $recent_appointments = $recent_stmt->fetchAll();

    // Upcoming appointments (next 5)
    $upcoming_stmt = $pdo->query("SELECT * FROM appointments WHERE date >= NOW() ORDER BY date ASC LIMIT 5");
    $upcoming_appointments = $upcoming_stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $error_message = 'Failed to load dashboard data.';
}

include 'includes/header.php';
?>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $total_appointments; ?></h3>
            <p>Total Appointments</p>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $pending_appointments; ?></h3>
            <p>Pending</p>
        </div>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $confirmed_appointments; ?></h3>
            <p>Confirmed</p>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon">
            <i class="fas fa-flag-checkered"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $completed_appointments; ?></h3>
            <p>Completed</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="appointments.php" class="action-btn">
        <i class="fas fa-list"></i>
        View All Appointments
    </a>
    <a href="add_appointment.php" class="action-btn">
        <i class="fas fa-plus"></i>
        New Appointment
    </a>
</div>

<!-- Two Column Layout -->
<div class="dashboard-grid">
    <!-- Upcoming Appointments -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2><i class="fas fa-calendar-day"></i> Upcoming Appointments</h2>
        </div>
        <div class="card-body">
            <?php if (count($upcoming_appointments) > 0): ?>
                <div class="appointments-list">
                    <?php foreach ($upcoming_appointments as $appointment): ?>
                        <div class="appointment-item">
                            <div class="appointment-info">
                                <h4><?php echo htmlspecialchars($appointment['name']); ?></h4>
                                <p>
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('M d, Y h:i A', strtotime($appointment['date'])); ?>
                                </p>
                                <p>
                                    <i class="fas fa-phone"></i>
                                    <?php echo htmlspecialchars($appointment['number']); ?>
                                </p>
                            </div>
                            <div class="appointment-status">
                                <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                    <?php echo ucfirst($appointment['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p>No upcoming appointments</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Appointments -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2><i class="fas fa-history"></i> Recent Appointments</h2>
        </div>
        <div class="card-body">
            <?php if (count($recent_appointments) > 0): ?>
                <div class="appointments-list">
                    <?php foreach ($recent_appointments as $appointment): ?>
                        <div class="appointment-item">
                            <div class="appointment-info">
                                <h4><?php echo htmlspecialchars($appointment['name']); ?></h4>
                                <p>
                                    <i class="fas fa-envelope"></i>
                                    <?php echo htmlspecialchars($appointment['email']); ?>
                                </p>
                                <p>
                                    <i class="fas fa-clock"></i>
                                    <?php echo date('M d, Y', strtotime($appointment['created_at'])); ?>
                                </p>
                            </div>
                            <div class="appointment-status">
                                <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                    <?php echo ucfirst($appointment['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No recent appointments</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>