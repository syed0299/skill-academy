<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

$searchQuery = $_GET['search'] ?? '';

// Fetch courses
$sql = "SELECT c.*, u.name as instructor_name FROM courses c JOIN users u ON c.instructor_id = u.id";
$params = [];
if (!empty($searchQuery)) {
    $sql .= " WHERE c.title LIKE ? OR c.description LIKE ?";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
}
$sql .= " ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();
?>

<!-- Hero Section -->
<div class="bg-dark text-white text-center py-5 mb-5" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80') no-repeat center center; background-size: cover;">
    <div class="container py-5">
        <h1 class="display-3 fw-bold mb-4">Master Your Future With Skill Academy</h1>
        <p class="lead mb-4">Learn practically any skill from experts across the globe. Improve yourself, improve your career.</p>
        <a href="#courses" class="btn btn-primary btn-lg px-5 my-2">Explore Courses</a>
    </div>
</div>

<div class="container my-5" id="courses">
    <?php if(!empty($searchQuery)): ?>
        <h2 class="mb-4">Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
    <?php else: ?>
        <h2 class="mb-4 fw-bold">Top Courses</h2>
    <?php endif; ?>

    <div class="row g-4">
        <?php if(count($courses) > 0): ?>
            <?php foreach($courses as $course): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 course-card">
                        <?php 
                        $img = !empty($course['image']) ? $course['image'] : 'https://via.placeholder.com/300x160?text=Course+Image'; 
                        ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" class="card-img-top" alt="Course Image">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-dark text-decoration-none">
                                <a href="courses/course_details.php?id=<?php echo $course['id']; ?>" class="text-dark text-decoration-none">
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </a>
                            </h5>
                            <p class="text-muted small mb-2"><i class="fas fa-chalkboard-teacher"></i> <?php echo htmlspecialchars($course['instructor_name']); ?></p>
                            
                            <p class="card-text flex-grow-1 text-muted">
                                <?php echo htmlspecialchars(substr($course['description'], 0, 80)) . '...'; ?>
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="course-price">$<?php echo htmlspecialchars($course['price']); ?></span>
                                <a href="courses/course_details.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <h4 class="text-muted">No courses found. Check back later!</h4>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
