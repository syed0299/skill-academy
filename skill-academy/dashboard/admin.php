<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireRole('admin');

// Comprehensive Admin Statistics Queries
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'students' => $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn(),
    'instructors' => $pdo->query("SELECT COUNT(*) FROM users WHERE role='instructor'")->fetchColumn(),
    'courses' => $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn(),
    'enrollments' => $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn(),
    'revenue' => $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed'")->fetchColumn()
];

// Fetch Recent Data for display
$recent_users = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recent_courses = $pdo->query("SELECT c.id, c.title, c.price, c.created_at, u.name as instructor_name FROM courses c JOIN users u ON c.instructor_id = u.id ORDER BY c.created_at DESC LIMIT 5")->fetchAll();

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content bg-light">
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="d-flex justify-content-between align-items-end mb-4 pb-3 border-bottom">
            <div>
                <h2 class="fw-bold mb-1">Platform Overview</h2>
                <p class="text-muted mb-0">Monitor your ecosystem's performance and growth.</p>
            </div>
            <div class="btn-group shadow-sm">
                <a href="../admin/manage_users.php" class="btn btn-primary"><i class="fas fa-users-cog me-2"></i> Users</a>
                <a href="../admin/manage_courses.php" class="btn btn-outline-primary bg-white"><i class="fas fa-book-open me-2"></i> Courses</a>
            </div>
        </div>

        <!-- 4-Column Stats Row -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-users fs-5"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill">+2.4%</span>
                        </div>
                        <h3 class="fw-bold text-dark mb-1"><?php echo number_format($stats['users']); ?></h3>
                        <p class="text-muted fw-bold small text-uppercase mb-0" style="letter-spacing: 0.5px;">Total Users</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="bg-indigo bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: rgba(99, 102, 241, 0.1); color: #6366f1;">
                                <i class="fas fa-chalkboard-teacher fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1"><?php echo number_format($stats['instructors']); ?></h3>
                        <p class="text-muted fw-bold small text-uppercase mb-0" style="letter-spacing: 0.5px;">Active Instructors</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="bg-warning bg-opacity-10 text-warning rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-graduation-cap fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-1"><?php echo number_format($stats['enrollments']); ?></h3>
                        <p class="text-muted fw-bold small text-uppercase mb-0" style="letter-spacing: 0.5px;">Total Enrollments</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="bg-success bg-opacity-10 text-success rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-dollar-sign fs-5"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill">+12%</span>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">$<?php echo number_format($stats['revenue'], 2); ?></h3>
                        <p class="text-muted fw-bold small text-uppercase mb-0" style="letter-spacing: 0.5px;">Total Revenue</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- Recent Users Column -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Recent Registration Activity</h5>
                        <a href="../admin/manage_users.php" class="btn btn-sm btn-light fw-bold text-primary shadow-sm rounded-pill px-3">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach($recent_users as $u): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-light py-3 px-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 40px; height: 40px;">
                                        <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($u['name']); ?></h6>
                                        <small class="text-muted text-truncate d-block" style="max-width: 150px;"><?php echo htmlspecialchars($u['email']); ?></small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge rounded-pill bg-<?php echo $u['role']=='admin'?'danger':($u['role']=='instructor'?'success':'primary'); ?> bg-opacity-10 border border-<?php echo $u['role']=='admin'?'danger':($u['role']=='instructor'?'success':'primary'); ?> border-opacity-25 text-<?php echo $u['role']=='admin'?'danger':($u['role']=='instructor'?'success':'primary'); ?> fw-semibold px-2 py-1 mb-1 d-block text-capitalize"><?php echo $u['role']; ?></span>
                                    <small class="text-muted" style="font-size: 0.75rem;"><?php echo date('M d', strtotime($u['created_at'])); ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Courses Column -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Latest Published Courses</h5>
                        <a href="../admin/manage_courses.php" class="btn btn-sm btn-light fw-bold text-primary shadow-sm rounded-pill px-3">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach($recent_courses as $c): ?>
                            <a href="../courses/course_details.php?id=<?php echo $c['id']; ?>" class="list-group-item list-group-item-action border-light py-3 px-4 text-decoration-none">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h6 class="mb-1 fw-bold text-dark text-truncate"><?php echo htmlspecialchars($c['title']); ?></h6>
                                        <small class="text-muted d-flex align-items-center">
                                            <i class="fas fa-chalkboard-teacher me-1"></i> <?php echo htmlspecialchars($c['instructor_name']); ?>
                                        </small>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="fw-bold text-dark"><?php echo $c['price'] > 0 ? '$'.number_format($c['price'], 2) : 'Free'; ?></div>
                                        <small class="text-muted" style="font-size: 0.75rem;"><?php echo date('M d', strtotime($c['created_at'])); ?></small>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
