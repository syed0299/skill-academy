<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireRole('admin');

$success = '';
$error = '';

// Handle User Deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['user_id'])) {
    $target_user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    
    if ($target_user_id) {
        if ($target_user_id !== $_SESSION['user_id']) { // Check against deleting own account
            try {
                if ($_POST['action'] === 'delete') {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                    if ($stmt->execute([$target_user_id])) {
                        $success = "User account cleanly deleted from the system.";
                    } else {
                        $error = "System failed to delete the user account.";
                    }
                }
            } catch (PDOException $e) {
                error_log("Failed deleting user: " . $e->getMessage());
                // In a production app, you might check if foreign key constraint failed
                $error = "A database error occurred. Ensure there are no critical constraints blocked.";
            }
        } else {
            $error = "Self-deletion is restricted for security purposes.";
        }
    } else {
        $error = "Invalid user ID provided.";
    }
}

// Fetch all users securely
try {
    // Add logic to show how many courses an instructor has or how many courses a student is enrolled in
    $stmt = $pdo->query("
        SELECT u.*, 
            (SELECT COUNT(*) FROM courses WHERE instructor_id = u.id) as created_courses,
            (SELECT COUNT(*) FROM enrollments WHERE student_id = u.id) as enrolled_courses
        FROM users u 
        ORDER BY u.created_at DESC
    ");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Failed to fetch users list.");
}

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content bg-light pb-5">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-end mb-4 pb-3 border-bottom">
            <div>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="../dashboard/admin.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
                <h2 class="fw-bold mb-0">Manage Users</h2>
            </div>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary bg-white shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i> Filter Role
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item" href="#">All Users</a></li>
                        <li><a class="dropdown-item" href="#">Students Only</a></li>
                        <li><a class="dropdown-item" href="#">Instructors Only</a></li>
                        <li><a class="dropdown-item" href="#">Admins Only</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger shadow-sm border-0 alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(!empty($success)): ?>
            <div class="alert alert-success shadow-sm border-0 alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Users Table Card -->
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">User Accounts Directory</h6>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><?php echo count($users); ?> Total</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; width: 60px;">ID</th>
                            <th class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">User Info</th>
                            <th class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Role Status</th>
                            <th class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Platform Activity</th>
                            <th class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Joined Date</th>
                            <th class="text-end pe-4 text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if(count($users) > 0): ?>
                            <?php foreach($users as $u): ?>
                                <tr>
                                    <td class="ps-4 fw-medium text-dark">#<?php echo $u['id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 45px; height: 45px; flex-shrink: 0;">
                                                <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($u['name']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($u['email']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $badgeClass = 'primary';
                                            if($u['role'] === 'admin') $badgeClass = 'danger';
                                            elseif($u['role'] === 'instructor') $badgeClass = 'success';
                                        ?>
                                        <span class="badge rounded-pill bg-<?php echo $badgeClass; ?> bg-opacity-10 border border-<?php echo $badgeClass; ?> border-opacity-25 text-<?php echo $badgeClass; ?> fw-semibold px-3 py-1 text-capitalize">
                                            <?php echo $u['role']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($u['role'] === 'instructor'): ?>
                                            <div class="small fw-semibold text-dark"><i class="fas fa-video text-muted me-1"></i> <?php echo $u['created_courses']; ?> Published</div>
                                        <?php else: ?>
                                            <div class="small fw-semibold text-dark"><i class="fas fa-book-reader text-muted me-1"></i> <?php echo $u['enrolled_courses']; ?> Enrolled</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="text-muted small fw-medium"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('WARNING: Are you sure you want to completely delete <?php echo htmlspecialchars(addslashes($u['name'])); ?>? This will cascade and delete all their associated courses and enrollments. This action cannot be undone.');">
                                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold shadow-sm" title="Delete User">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary disabled rounded-pill px-3 shadow-none">Active Admin</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <i class="fas fa-users-slash fs-1 text-muted mb-3 opacity-50"></i>
                                        <h5 class="fw-bold">No Users Found</h5>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white border-0 py-3">
                <!-- Pagination placeholder for future enhancement -->
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm justify-content-end mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
