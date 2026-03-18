<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireRole('student');

$user_id = $_SESSION['user_id'];

// Get aggregate stats
$stmt = $pdo->prepare("
    SELECT 
        COUNT(e.id) as total_enrolled,
        COALESCE(SUM(p.amount), 0) as total_spent
    FROM enrollments e
    LEFT JOIN payments p ON e.id = p.enrollment_id AND p.status = 'completed'
    WHERE e.student_id = ?
");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();

// Get enrolled courses
$stmt = $pdo->prepare("
    SELECT c.id, c.title, c.description, c.image, u.name as instructor_name, e.enrolled_at 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.id 
    JOIN users u ON c.instructor_id = u.id 
    WHERE e.student_id = ?
    ORDER BY e.enrolled_at DESC
");
$stmt->execute([$user_id]);
$enrollments = $stmt->fetchAll();

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="d-flex justify-content-between align-items-end mb-4 pb-3 border-bottom">
            <div>
                <h2 class="fw-bold mb-1">My Learning</h2>
                <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>! Let's continue your journey.</p>
            </div>
            <a href="../courses/view_courses.php" class="btn btn-primary shadow-sm"><i class="fas fa-search me-2"></i> Browse New Courses</a>
        </div>
        
        <!-- Stats Row -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="text-muted fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.8rem;">Enrolled Courses</h6>
                                <h2 class="fw-bold text-dark mb-0"><?php echo number_format($stats['total_enrolled']); ?></h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-book-reader fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="text-muted fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.8rem;">Certificates</h6>
                                <h2 class="fw-bold text-dark mb-0">0</h2>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-award fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden position-relative">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="text-muted fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.8rem;">Total Investment</h6>
                                <h2 class="fw-bold text-dark mb-0">$<?php echo number_format($stats['total_spent'], 2); ?></h2>
                            </div>
                            <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="fw-bold mb-4">Courses In Progress</h4>
        <div class="row g-4">
            <?php if(count($enrollments) > 0): ?>
                <?php foreach($enrollments as $course): ?>
                    <div class="col-md-6 col-lg-4 col-xl-4 d-flex">
                        <div class="card w-100 border-0 shadow-sm rounded-4 overflow-hidden course-card" style="transition: transform 0.2s, box-shadow 0.2s;">
                            <?php $img = !empty($course['image']) ? $course['image'] : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=400&q=80'; ?>
                            <div class="position-relative">
                                <img src="<?php echo htmlspecialchars($img); ?>" class="card-img-top" alt="Course Cover" style="height: 160px; object-fit: cover;">
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <small class="text-primary fw-bold mb-2 d-block">Enrolled: <?php echo date('M d, Y', strtotime($course['enrolled_at'])); ?></small>
                                <h5 class="card-title fw-bold text-dark text-truncate-2 mb-2" style="font-size: 1.1rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <a href="../courses/course_details.php?id=<?php echo $course['id']; ?>" class="text-dark text-decoration-none">
                                        <?php echo htmlspecialchars($course['title']); ?>
                                    </a>
                                </h5>
                                <p class="text-muted small mb-3"><i class="fas fa-chalkboard-teacher me-1"></i> <?php echo htmlspecialchars($course['instructor_name']); ?></p>
                                
                                <!-- Progress Bar Mockup -->
                                <div class="mb-4 mt-auto">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Progress</span>
                                        <span class="fw-bold text-dark">0%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                
                                <div class="mt-auto">
                                    <a href="../courses/course_details.php?id=<?php echo $course['id']; ?>" class="btn btn-light w-100 border border-1 fw-bold text-primary shadow-sm hover-primary-bg" style="transition: background 0.2s;">Continue Course <i class="fas fa-arrow-right ms-2 fs-7"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="bg-white p-5 rounded-4 shadow-sm border d-inline-block">
                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                        <h4 class="fw-bold">No Enrollments Yet</h4>
                        <p class="text-muted">Start learning today. Browse our marketplace to find the perfect course.</p>
                        <a href="../courses/view_courses.php" class="btn btn-primary px-4 py-2 mt-2">Find a Course</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .course-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important; }
    .hover-primary-bg:hover { background-color: var(--primary-color) !important; color: white !important; border-color: var(--primary-color) !important; }
</style>

<?php require_once '../includes/footer.php'; ?>
