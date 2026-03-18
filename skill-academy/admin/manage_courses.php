<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireRole('admin');

$success = '';
$error = '';

// Handle Course Deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['course_id'])) {
    $target_course_id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
    
    if ($target_course_id) {
        try {
            if ($_POST['action'] === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
                if ($stmt->execute([$target_course_id])) {
                    $success = "Course permanently removed from the catalog.";
                } else {
                    $error = "System failed to delete the course.";
                }
            }
        } catch (PDOException $e) {
            error_log("Failed deleting course: " . $e->getMessage());
            $error = "A database error occurred during deletion.";
        }
    } else {
        $error = "Invalid course ID provided.";
    }
}

// Fetch all courses securely 
try {
    $stmt = $pdo->query("
        SELECT c.*, u.name as instructor_name,
            (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as enrolled_count
        FROM courses c 
        JOIN users u ON c.instructor_id = u.id 
        ORDER BY c.created_at DESC
    ");
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Failed to fetch courses list.");
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
                    <li class="breadcrumb-item active" aria-current="page">Courses</li>
                </ol>
                <h2 class="fw-bold mb-0">Manage Courses</h2>
            </div>
            
            <div class="d-flex gap-2">
                <form class="position-relative">
                    <input type="text" class="form-control rounded-pill shadow-sm pe-5 border-0" placeholder="Search courses..." style="width: 250px;">
                    <i class="fas fa-search position-absolute top-50 translate-middle-y text-muted" style="right: 15px;"></i>
                </form>
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

        <!-- Courses Table Card -->
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">Platform Course Directory</h6>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><?php echo count($courses); ?> Published</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; width: 60px;">ID</th>
                            <th class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Course Overview</th>
                            <th class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Instructor</th>
                            <th class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Price</th>
                            <th class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Students</th>
                            <th class="text-end pe-4 text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Management</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if(count($courses) > 0): ?>
                            <?php foreach($courses as $c): ?>
                                <tr>
                                    <td class="ps-4 fw-medium text-dark">#<?php echo $c['id']; ?></td>
                                    <td style="max-width: 300px;">
                                        <div class="d-flex align-items-center">
                                            <?php $img = !empty($c['image']) ? $c['image'] : 'https://via.placeholder.com/60x40'; ?>
                                            <img src="<?php echo htmlspecialchars($img); ?>" class="rounded me-3 object-fit-cover shadow-sm" style="width: 60px; height: 40px; flex-shrink: 0;" alt="Cover">
                                            <div>
                                                <a href="../courses/course_details.php?id=<?php echo $c['id']; ?>" class="fw-bold text-dark text-decoration-none text-truncate d-block mb-1" style="font-size: 0.95rem; max-width: 220px;" title="<?php echo htmlspecialchars($c['title']); ?>"><?php echo htmlspecialchars($c['title']); ?></a>
                                                <small class="text-muted">Published <?php echo date('M d, Y', strtotime($c['created_at'])); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-chalkboard-teacher text-muted me-2"></i>
                                            <span class="fw-semibold text-dark"><?php echo htmlspecialchars($c['instructor_name']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($c['price'] > 0): ?>
                                            <span class="badge bg-primary fw-bold px-2 py-1 fs-7">$<?php echo number_format($c['price'], 2); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success fw-bold px-2 py-1 fs-7">Free</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-users text-muted me-2"></i>
                                            <span class="fw-bold text-dark"><?php echo number_format($c['enrolled_count']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="../courses/course_details.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-light border shadow-sm rounded-pill text-primary fw-bold px-3 me-2" title="View Course">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        
                                        <form method="POST" class="d-inline" onsubmit="return confirm('WARNING: Are you sure you want to permanently delete \'<?php echo htmlspecialchars(addslashes($c['title'])); ?>\'? All related enrollments, payments, and reviews will be cascade-deleted. This is irreversible.');">
                                            <input type="hidden" name="course_id" value="<?php echo $c['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold shadow-sm" title="Delete Course">
                                                <i class="fas fa-trash-alt me-1"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <i class="fas fa-box-open fs-1 text-muted mb-3 opacity-50"></i>
                                        <h5 class="fw-bold">No Courses Found</h5>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white border-0 py-3">
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
