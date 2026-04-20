<?php
require_once 'includes/auth.php';
require_once '../config/database.php';

$page_title = 'Manage Appointments';

$success_message = '';
$error_message = '';

// Check for session messages (from delete.php)
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}


// Handle search and filter
$search        = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Initialize to empty array so count() is always safe even if query fails
$appointments = [];
$total_records = 0;
$total_pages   = 1;

// Pagination
$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

try {
    // Build WHERE clause separately so we can reuse it for COUNT and SELECT
    $where  = "WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        // PDO with emulate_prepares=false disallows the same placeholder twice,
        // so use distinct names :s1, :s2, :s3 for each OR branch.
        $where .= " AND (name LIKE :s1 OR email LIKE :s2 OR number LIKE :s3)";
        $params['s1'] = "%$search%";
        $params['s2'] = "%$search%";
        $params['s3'] = "%$search%";
    }

    if (!empty($status_filter)) {
        $where .= " AND status = :status";
        $params['status'] = $status_filter;
    }

    // Reliable total count using COUNT(*)
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments $where");
    $count_stmt->execute($params);
    $total_records = (int) $count_stmt->fetchColumn();
    $total_pages   = max(1, ceil($total_records / $items_per_page));

    // Fetch the page of results
    $stmt = $pdo->prepare("SELECT * FROM appointments $where ORDER BY date DESC LIMIT :limit OFFSET :offset");

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit',  $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset,         PDO::PARAM_INT);

    $stmt->execute();
    $appointments = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Appointments Error: " . $e->getMessage());
    $error_message = 'Failed to load appointments.';
}

include 'includes/header.php';
?>

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

<!-- Filters and Search -->
<div class="page-header">
    <h2><i class="fas fa-calendar-check"></i> All Appointments</h2>
    <a href="add_appointment.php" class="action-btn">
        <i class="fas fa-plus"></i> New Appointment
    </a>
</div>

<div class="filters-section">
    <form method="GET" action="" class="filter-form">
        <div class="filter-group">
            <input
                type="text"
                name="search"
                placeholder="Search by name, email, or phone..."
                value="<?php echo htmlspecialchars($search); ?>"
                class="search-input">
        </div>

        <div class="filter-group">
            <select name="status" class="status-select">
                <option value="">All Status</option>
                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
        </div>

        <button type="submit" class="filter-btn">
            <i class="fas fa-search"></i> Filter
        </button>

        <a href="appointments.php" class="reset-btn">
            <i class="fas fa-redo"></i> Reset
        </a>
    </form>
</div>

<!-- Appointments Table -->
<div class="table-container">
    <?php if (count($appointments) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Appointment Date</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td>#<?php echo $appointment['id']; ?></td>
                        <td><?php echo htmlspecialchars($appointment['name']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['email']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['number']); ?></td>
                        <td>
                            <i class="fas fa-calendar"></i>
                            <?php echo date('M d, Y h:i A', strtotime($appointment['date'])); ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                <?php echo ucfirst($appointment['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($appointment['created_at'])); ?></td>
                        <td class="actions">
                            <a href="edit.php?id=<?php echo $appointment['id']; ?>" class="btn-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $appointment['id']; ?>"
                                class="btn-delete"
                                title="Delete"
                                onclick="return confirm('Are you sure you want to delete this appointment?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?>" class="page-link">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php endif; ?>

                <span class="page-info">
                    Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>
                </span>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?>" class="page-link">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="empty-state-large">
            <i class="fas fa-calendar-times"></i>
            <h3>No Appointments Found</h3>
            <p>There are no appointments matching your criteria.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>