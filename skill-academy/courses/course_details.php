<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

$course_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$course_id) {
    header("Location: view_courses.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT c.*, u.name as instructor_name, u.email as instructor_email,
            (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as students_count
            FROM courses c JOIN users u ON c.instructor_id = u.id WHERE c.id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    if (!$course) {
        die("Course not found.");
    }

    $is_enrolled = false;
    $is_own_course = false;

    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $role = getUserRole();
        
        if ($role === 'student' || $role === 'admin') {
            $stmt = $pdo->prepare("SELECT id, enrolled_at FROM enrollments WHERE student_id = ? AND course_id = ?");
            $stmt->execute([$user_id, $course_id]);
            $enrollment = $stmt->fetch();
            if ($enrollment) $is_enrolled = true;
        } elseif ($role === 'instructor') {
            if ($course['instructor_id'] == $user_id) {
                $is_own_course = true;
            }
        }
    }
} catch (PDOException $e) {
    die("Database Error");
}

require_once '../includes/header.php';
$isDashboardState = (isLoggedIn() && getUserRole() !== 'student');
if ($isDashboardState) {
    require_once '../includes/sidebar.php';
}

$cover_img = !empty($course['image']) ? $course['image'] : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1920&q=80';
?>

<div class="<?php echo $isDashboardState ? 'main-content ' : ''; ?>p-0 m-0">
    <!-- Hero Banner -->
    <div class="bg-dark text-white position-relative <?php echo !$isDashboardState ? 'mt-5' : ''; ?>" style="overflow: hidden;">
        <div class="position-absolute w-100 h-100" style="background: url('<?php echo htmlspecialchars($cover_img); ?>') center center / cover no-repeat; opacity: 0.3; filter: blur(4px); transform: scale(1.05);"></div>
        <div class="container py-5 py-md-5 position-relative z-index-1">
            <div class="row py-md-4">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="view_courses.php" class="text-info text-decoration-none">Courses</a></li>
                            <li class="breadcrumb-item active text-white-50" aria-current="page">Course Details</li>
                        </ol>
                    </nav>
                    <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($course['title']); ?></h1>
                    <p class="fs-5 text-white-50 mb-4 lh-base" style="max-width: 800px;">Learn exactly what it takes to master this topic, step-by-step.</p>
                    
                    <div class="d-flex flex-wrap align-items-center gap-4 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: bold;">
                                <?php echo strtoupper(substr($course['instructor_name'], 0, 1)); ?>
                            </div>
                            <span>Created by <span class="text-info text-decoration-underline"><?php echo htmlspecialchars($course['instructor_name']); ?></span></span>
                        </div>
                        <div class="d-flex align-items-center text-warning">
                            <i class="fas fa-star me-1"></i>
                            <i class="fas fa-star me-1"></i>
                            <i class="fas fa-star me-1"></i>
                            <i class="fas fa-star me-1"></i>
                            <i class="fas fa-star-half-alt me-2"></i>
                            <span class="text-white">(4.5 rating)</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users me-2 text-white-50"></i>
                            <span><?php echo number_format($course['students_count']); ?> students enrolled</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5 position-relative">
        <div class="row align-items-start position-relative">
            <!-- Left Content Column -->
            <div class="col-lg-8 pe-lg-5">
                <div class="card border-0 shadow-sm p-4 p-md-5 mb-5 rounded-4 bg-white">
                    <h3 class="fw-bold mb-4 border-bottom pb-3">Course Overview</h3>
                    <div class="description-content fs-6 text-dark" style="line-height: 1.8; white-space: pre-wrap;"><?php echo htmlspecialchars($course['description']); ?></div>
                </div>
                
                <div class="card border-0 shadow-sm p-4 p-md-5 mb-5 rounded-4 bg-white">
                    <h3 class="fw-bold mb-4">What you'll learn</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3 d-flex"><i class="fas fa-check text-success mt-1 me-3"></i> <span>Build real-world projects from scratch.</span></div>
                        <div class="col-md-6 mb-3 d-flex"><i class="fas fa-check text-success mt-1 me-3"></i> <span>Understand complex architectural concepts easily.</span></div>
                        <div class="col-md-6 mb-3 d-flex"><i class="fas fa-check text-success mt-1 me-3"></i> <span>Industry standard best practices & workflows.</span></div>
                        <div class="col-md-6 mb-3 d-flex"><i class="fas fa-check text-success mt-1 me-3"></i> <span>Interview readiness and portfolio preparation.</span></div>
                    </div>
                </div>
            </div>

            <!-- Right Registration/CTA Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden position-sticky" style="top: 100px; margin-top: -150px; z-index: 10;">
                    <img src="<?php echo htmlspecialchars($cover_img); ?>" class="w-100 object-fit-cover border-bottom" style="height: 220px;" alt="Course Cover">
                    
                    <div class="card-body p-4 p-md-5 bg-white text-center">
                        <div class="fw-bold text-dark display-5 mb-4 d-flex justify-content-center align-items-center gap-2">
                            <?php echo $course['price'] > 0 ? '$'.number_format($course['price'], 2) : 'Free'; ?>
                        </div>
                        
                        <?php if (isLoggedIn()): ?>
                            <?php if ($is_enrolled): ?>
                                <button class="btn btn-success btn-lg w-100 disabled py-3 fw-bold rounded-pill mb-3">
                                    <i class="fas fa-check-circle me-2"></i> Enrolled on <?php echo date('M d, Y', strtotime($enrollment['enrolled_at'])); ?>
                                </button>
                                <a href="../dashboard/student.php" class="btn btn-outline-primary btn-lg w-100 outline-none rounded-pill fw-bold">Go to Dashboard</a>
                            <?php elseif ($is_own_course): ?>
                                <button class="btn btn-dark btn-lg w-100 disabled py-3 fw-bold rounded-pill mb-3">
                                    <i class="fas fa-crown me-2"></i> You created this course
                                </button>
                            <?php else: ?>
                                <!-- Enrollment Form -->
                                <form action="enroll.php" method="POST">
                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo bin2hex(random_bytes(32)); ?>"> <!-- Simple demonstration -->
                                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-pill shadow mb-3" style="background-color: var(--primary-color);">
                                        <i class="fas fa-bolt me-2"></i> Enroll Now
                                    </button>
                                </form>
                                <p class="text-muted small">Full lifetime access. 30-Day Money-Back Guarantee.</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="../login.php" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-pill shadow mb-3">
                                Log In to Enroll
                            </a>
                        <?php endif; ?>
                        
                        <hr class="my-4">
                        
                        <div class="text-start">
                            <h6 class="fw-bold mb-3">This course includes:</h6>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2 text-muted small">
                                <li><i class="fas fa-video w-20px text-center me-2"></i> 14 hours on-demand video</li>
                                <li><i class="fas fa-file w-20px text-center me-2"></i> 22 articles and resources</li>
                                <li><i class="fas fa-mobile-alt w-20px text-center me-2"></i> Access on mobile and TV</li>
                                <li><i class="fas fa-trophy w-20px text-center me-2"></i> Certificate of completion</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .w-20px { width: 20px; }
</style>

<?php require_once '../includes/footer.php'; ?>
