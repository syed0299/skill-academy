<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/header.php';

// Fetch courses logic
$search = $_GET['search'] ?? '';
$params = [];

$sql = "SELECT c.*, u.name as instructor_name, 
        (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as enrolled_count
        FROM courses c 
        JOIN users u ON c.instructor_id = u.id";

if (!empty($search)) {
    $sql .= " WHERE c.title LIKE ? OR c.description LIKE ? OR u.name LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$sql .= " ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();

$isDashboard = strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/courses/create_course.php') !== false;

if (isLoggedIn() && getUserRole() !== 'student') {
    require_once '../includes/sidebar.php';
}
?>

<div class="<?php echo (isLoggedIn() && getUserRole() !== 'student') ? 'main-content' : 'container py-5 mt-5'; ?>">
    <!-- Search and Header Section -->
    <div class="row mb-5 align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <h1 class="fw-bold mb-2">Explore Courses</h1>
            <p class="text-muted">Discover the perfect course to advance your career.</p>
        </div>
        <div class="col-lg-6">
            <form action="view_courses.php" method="GET" class="d-flex bg-white rounded-pill p-2 shadow-sm border">
                <input type="text" name="search" class="form-control border-0 shadow-none bg-transparent ms-2" placeholder="What do you want to learn?" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: var(--primary-color);">Search</button>
            </form>
        </div>
    </div>

    <?php if (!empty($search)): ?>
        <h4 class="mb-4">Search results for <span class="text-primary">"<?php echo htmlspecialchars($search); ?>"</span> (<?php echo count($courses); ?> found)</h4>
    <?php endif; ?>

    <!-- Course Grid -->
    <div class="row g-4 mb-5">
        <?php if (count($courses) > 0): ?>
            <?php foreach ($courses as $course): ?>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 d-flex">
                    <div class="card w-100 border-0 shadow-sm course-card overflow-hidden" style="border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s;">
                        <a href="course_details.php?id=<?php echo $course['id']; ?>" class="text-decoration-none text-dark">
                            <div class="position-relative">
                                <?php $img = !empty($course['image']) ? $course['image'] : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=400&q=80'; ?>
                                <img src="<?php echo htmlspecialchars($img); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($course['title']); ?>" style="height: 180px; object-fit: cover;">
                                <?php if ($course['price'] == 0): ?>
                                    <span class="badge bg-success position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill shadow-sm">Free</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <p class="text-muted small mb-2 d-flex align-items-center">
                                    <i class="fas fa-user-tie me-2 text-primary"></i> <?php echo htmlspecialchars($course['instructor_name']); ?>
                                </p>
                                <h5 class="card-title fw-bold text-truncate-2 mb-3" style="font-size: 1.1rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </h5>
                                
                                <div class="mt-auto">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="text-warning small">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                            <span class="text-muted ms-1">(120)</span>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fas fa-users"></i> <?php echo $course['enrolled_count']; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                        <span class="fs-5 fw-bold text-primary">
                                            <?php echo $course['price'] > 0 ? '$' . number_format($course['price'], 2) : 'Free'; ?>
                                        </span>
                                        <span class="btn btn-sm btn-outline-primary rounded-pill px-3">View Details</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="bg-white p-5 rounded-4 shadow-sm border d-inline-block">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="fw-bold">No courses found</h4>
                    <p class="text-muted">We couldn't find any courses matching your criteria. Try adjusting your search.</p>
                    <a href="view_courses.php" class="btn btn-primary px-4 mt-2">Clear Search</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .course-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }
</style>

<?php require_once '../includes/footer.php'; ?>
