<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireRole('instructor');
$user_id = $_SESSION['user_id'];

// Get aggregate stats securely
$stmt = $pdo->prepare("
    SELECT 
        COUNT(DISTINCT c.id) as total_courses,
        COUNT(e.id) as total_students,
        COALESCE(SUM(p.amount), 0) as total_earnings
    FROM courses c
    LEFT JOIN enrollments e ON c.id = e.course_id
    LEFT JOIN payments p ON e.id = p.enrollment_id AND p.status = 'completed'
    WHERE c.instructor_id = ?
");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();

// Get recently created courses
$stmt = $pdo->prepare("
    SELECT c.*, COUNT(e.id) as student_count 
    FROM courses c 
    LEFT JOIN enrollments e ON c.id = e.course_id 
    WHERE c.instructor_id = ?
    GROUP BY c.id
    ORDER BY c.created_at DESC
");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="d-flex justify-content-between align-items-end mb-4 pb-3 border-bottom">
            <div>
                <h2 class="fw-bold mb-1">Instructor Dashboard</h2>
                <p class="text-muted mb-0">Here's what's happening with your courses today.</p>
            </div>
            <a href="../courses/create_course.php" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i> Create Course</a>
        </div>
        
        <!-- Stats Row -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="card-body p-4 border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="text-muted fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.8rem;">My Courses</h6>
                            <i class="fas fa-video text-muted"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-0"><?php echo number_format($stats['total_courses']); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="card-body p-4 border-start border-4 border-success">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="text-muted fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.8rem;">Total Students</h6>
                            <i class="fas fa-users text-muted"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-0"><?php echo number_format($stats['total_students']); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                    <div class="card-body p-4 border-start border-4 border-info">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="text-muted fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.8rem;">Lifetime Earnings</h6>
                            <i class="fas fa-hand-holding-usd text-muted"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-0">$<?php echo number_format($stats['total_earnings'], 2); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="fw-bold mb-4">Manage Your Courses</h4>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-5">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 fw-bold text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Course Info</th>
                                <th class="fw-bold text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Price</th>
                                <th class="fw-bold text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Students</th>
                                <th class="fw-bold text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Published Date</th>
                                <th class="text-end pe-4 fw-bold text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php if(count($courses) > 0): ?>
                                <?php foreach($courses as $course): ?>
                                <tr style="cursor: pointer;">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <?php $img = !empty($course['image']) ? $course['image'] : 'https://via.placeholder.com/80x50'; ?>
                                            <img src="<?php echo htmlspecialchars($img); ?>" class="rounded me-3 object-fit-cover shadow-sm" style="width: 80px; height: 50px;" alt="...">
                                            <div>
                                                <a href="../courses/course_details.php?id=<?php echo $course['id']; ?>" class="fw-bold text-dark text-decoration-none d-block mb-1" style="font-size: 0.95rem;"><?php echo htmlspecialchars($course['title']); ?></a>
                                                <span class="badge bg-success bg-opacity-10 text-success fw-normal">Published</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="fw-bold text-dark"><?php echo $course['price'] > 0 ? '$'.number_format($course['price'], 2) : 'Free'; ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-graduate text-muted me-2"></i>
                                            <span class="fw-semibold text-dark"><?php echo number_format($course['student_count']); ?></span>
                                        </div>
                                    </td>
                                    <td><span class="text-muted small"><?php echo date('M d, Y', strtotime($course['created_at'])); ?></span></td>
                                    <td class="text-end pe-4">
                                        <a href="../courses/course_details.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-light border shadow-sm rounded-pill text-primary fw-bold px-3">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open mb-3 fs-4 d-block mx-auto text-muted"></i>
                                        You haven't created any courses yet.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
