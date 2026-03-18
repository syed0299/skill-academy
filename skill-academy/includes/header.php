<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Academy - Premium Learning Platform</title>
    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS (Modern SaaS UI) -->
    <link rel="stylesheet" href="/skill-academy/assets/css/style.css">
</head>
<?php $isDashboard = strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/courses/create_course.php') !== false; ?>
<body class="<?php echo $isDashboard ? 'has-dashboard' : ''; ?>">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container<?php echo $isDashboard ? '-fluid' : ''; ?>">
            <div class="<?php echo $isDashboard ? 'navbar-brand-wrapper' : ''; ?>">
                <a class="navbar-brand" href="/skill-academy/index.php">
                    <div class="bg-primary text-white rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span>SkillAcademy</span>
                </a>
            </div>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <i class="fas fa-bars text-dark"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <form class="mx-auto my-2 my-lg-0 d-flex w-100 justify-content-center" action="/skill-academy/index.php" method="GET">
                    <div class="search-container w-100" style="max-width: 400px;">
                        <i class="fas fa-search search-icon"></i>
                        <input class="search-bar" type="search" name="search" placeholder="Search courses, skills, or instructors..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>
                </form>
                
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <?php if(isLoggedIn()): ?>
                        <?php 
                            $dash_url = '/skill-academy/dashboard/student.php';
                            if($_SESSION['role'] === 'instructor') $dash_url = '/skill-academy/dashboard/instructor.php';
                            if($_SESSION['role'] === 'admin') $dash_url = '/skill-academy/dashboard/admin.php';
                        ?>
                        <li class="nav-item d-none d-lg-block">
                            <a class="nav-link text-muted position-relative" href="#" title="Notifications">
                                <i class="far fa-bell fs-5"></i>
                                <span class="position-absolute top-25 start-75 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 36px; height: 36px; font-size: 14px;">
                                    <?php echo strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)); ?>
                                </div>
                                <div class="d-none d-lg-block text-start" style="line-height: 1.2;">
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 120px; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
                                    <div class="text-muted small text-capitalize" style="font-size: 0.75rem;"><?php echo htmlspecialchars($_SESSION['role']); ?></div>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item py-2" href="<?php echo $dash_url; ?>"><i class="fas fa-columns text-muted me-2 w-15px"></i> Dashboard</a></li>
                                <li><a class="dropdown-item py-2" href="#"><i class="far fa-user float-end text-muted me-2 w-15px"></i> Profile Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2 text-danger" href="/skill-academy/logout.php"><i class="fas fa-sign-out-alt me-2 w-15px"></i> Log out</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link fw-medium text-dark" href="/skill-academy/login.php">Log in</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary shadow-sm px-4" href="/skill-academy/register.php">Sign up</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <?php if($isDashboard): ?>
    <div class="wrapper">
    <?php endif; ?>
