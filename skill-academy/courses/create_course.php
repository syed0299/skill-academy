<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireRole('instructor');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $price = filter_var($_POST['price'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $image = filter_var($_POST['image'] ?? '', FILTER_SANITIZE_URL);

    if (!empty($title) && !empty($description)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (instructor_id, title, description, price, image) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$_SESSION['user_id'], $title, $description, $price, $image])) {
                $course_id = $pdo->lastInsertId();
                $success = "Course created successfully! <a href='course_details.php?id={$course_id}' class='alert-link'>View Course</a>";
            } else {
                $error = "Failed to create course. Please try again.";
            }
        } catch (PDOException $e) {
            error_log("Error creating course: " . $e->getMessage());
            $error = "A database error occurred. Please try again.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid py-4 max-w-4xl mx-auto" style="max-width: 900px;">
        
        <div class="d-flex align-items-center mb-4">
            <a href="../dashboard/instructor.php" class="btn btn-light shadow-sm me-3"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h2 class="mb-0 fw-bold">Create New Course</h2>
                <p class="text-muted mb-0">Share your knowledge with millions of students across the globe.</p>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4">
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger shadow-sm border-0"><i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if(!empty($success)): ?>
                <div class="alert alert-success shadow-sm border-0"><i class="fas fa-check-circle me-2"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="create_course.php">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">Basic Information</h5>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Course Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg bg-light border-0" required placeholder="e.g. Master React 18 from Scratch" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                    <small class="text-muted mt-1 d-block">A good title is clear, descriptive, and catchy.</small>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Course Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control bg-light border-0" rows="6" required placeholder="Describe what students will learn, course requirements, and who this course is for..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                
                <h5 class="fw-bold mb-4 pb-2 mt-5 border-bottom">Details & Media</h5>
                
                <div class="row gx-4">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold text-dark">Price ($) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg border-0 shadow-none">
                            <span class="input-group-text border-0 bg-light text-muted">$</span>
                            <input type="number" step="0.01" min="0" name="price" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($_POST['price'] ?? '0.00'); ?>" required>
                        </div>
                        <small class="text-muted mt-1 d-block">Set to 0.00 to offer this course for free.</small>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold text-dark">Course Cover Image (URL)</label>
                        <div class="input-group input-group-lg border-0 shadow-none">
                            <span class="input-group-text border-0 bg-light text-muted"><i class="fas fa-link"></i></span>
                            <input type="url" name="image" class="form-control bg-light border-0" placeholder="https://unsplash.com/your-image.jpg" value="<?php echo htmlspecialchars($_POST['image'] ?? ''); ?>">
                        </div>
                        <small class="text-muted mt-1 d-block">Provide a direct high-quality image link (16:9 ratio).</small>
                    </div>
                </div>
                
                <div class="mt-5 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm" style="background-color: var(--primary-color);">
                        <i class="fas fa-paper-plane me-2"></i> Publish Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
