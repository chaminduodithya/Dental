<?php
// Protect this page
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
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : '';

// Pagination
$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

try {
    // Build query with filters
    $query = "SELECT * FROM appointments WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (name LIKE :search OR email LIKE :search OR number LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    if (!empty($status_filter)) {
        $query .= " AND status = :status";
        $params['status'] = $status_filter;
    }
    
    // Get total count for pagination
    $count_stmt = $pdo->prepare($query);
    $count_stmt->execute($params);
    $total_records = $count_stmt->rowCount();
    $total_pages = ceil($total_records / $items_per_page);
    
    // Get appointments with pagination
    $query .= " ORDER BY date DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($query);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $appointments = $stmt->fetchAll();
    
} catch(PDOException $e) {
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
</div>

<div class="filters-section">
    <form method="GET" action="" class="filter-form">
        <div class="filter-group">
            <input 
                type="text" 
                name="search" 
                placeholder="Search by name, email, or phone..." 
                value="<?php echo htmlspecialchars($search); ?>"
                class="search-input"
            >
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
                <?php foreach($appointments as $appointment): ?>
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
