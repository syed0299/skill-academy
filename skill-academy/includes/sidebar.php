<style>
    /* Modern SaaS Sidebar Stylings */
    .sidebar {
        width: var(--sidebar-width);
        background-color: var(--sidebar-bg);
        color: var(--sidebar-text);
        height: calc(100vh - 64px); /* Full height minus navbar */
        position: fixed;
        left: 0;
        top: 64px;
        z-index: 1000;
        overflow-y: auto;
        transition: all 0.3s ease-in-out;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
    }

    /* Webkit scrollbar for sidebar */
    .sidebar::-webkit-scrollbar { width: 6px; }
    .sidebar::-webkit-scrollbar-track { background: transparent; }
    .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .sidebar-header {
        padding: 1.5rem 1.5rem 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        margin-bottom: 1rem;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0 0.75rem;
        margin: 0;
        flex-grow: 1;
    }

    .sidebar-item {
        margin-bottom: 0.25rem;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: var(--sidebar-text);
        text-decoration: none;
        border-radius: 0.5rem;
        transition: all 0.2s;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .sidebar-link i {
        font-size: 1.1rem;
        width: 1.25rem;
        text-align: center;
        transition: color 0.2s;
    }

    .sidebar-link:hover {
        background-color: var(--sidebar-hover);
        color: var(--sidebar-active);
    }

    .sidebar-link.active {
        background-color: var(--primary-color);
        color: #ffffff;
    }

    .sidebar-heading {
        padding: 1.5rem 1rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: rgba(255,255,255,0.4);
    }
    
    .sidebar-footer {
        padding: 1rem;
        border-top: 1px solid rgba(255,255,255,0.05);
        margin-top: auto;
    }

    /* Adjust main content area to accommodate fixed sidebar */
    .main-content {
        flex-grow: 1;
        padding: 2rem;
        background-color: var(--bg-body);
        min-height: 100%;
        margin-left: 0;
        transition: margin 0.3s ease-in-out;
    }

    @media (min-width: 992px) {
        .main-content {
            margin-left: var(--sidebar-width); /* Push content right by sidebar width */
        }
    }

    @media (max-width: 991.98px) {
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.show {
            transform: translateX(0);
        }
    }
</style>

<aside class="sidebar d-none d-lg-flex" id="dashboardSidebar">
    <div class="sidebar-menu mt-3">
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <li class="sidebar-heading">Navigation</li>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
            <li class="sidebar-item">
                <a href="/skill-academy/dashboard/student.php" class="sidebar-link <?php echo $current_page == 'student.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> <span>My Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/skill-academy/index.php" class="sidebar-link">
                    <i class="fas fa-search"></i> <span>Explore Courses</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="fas fa-certificate"></i> <span>My Certificates</span>
                </a>
            </li>
            
        <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] === 'instructor'): ?>
            <li class="sidebar-item">
                <a href="/skill-academy/dashboard/instructor.php" class="sidebar-link <?php echo $current_page == 'instructor.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> <span>Dashboard Overview</span>
                </a>
            </li>
            <li class="sidebar-heading">Course Management</li>
            <li class="sidebar-item">
                <a href="/skill-academy/courses/view_courses.php" class="sidebar-link <?php echo $current_page == 'view_courses.php' ? 'active' : ''; ?>">
                    <i class="fas fa-layer-group"></i> <span>My Courses</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/skill-academy/courses/create_course.php" class="sidebar-link <?php echo $current_page == 'create_course.php' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-square"></i> <span>Create New Course</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="fas fa-users"></i> <span>My Students</span>
                </a>
            </li>

        <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li class="sidebar-item">
                <a href="/skill-academy/dashboard/admin.php" class="sidebar-link <?php echo $current_page == 'admin.php' ? 'active' : ''; ?>">
                    <i class="fas fa-satellite-dish"></i> <span>Control Panel</span>
                </a>
            </li>
            <li class="sidebar-heading">Administration</li>
            <li class="sidebar-item">
                <a href="/skill-academy/admin/manage_users.php" class="sidebar-link <?php echo $current_page == 'manage_users.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users-cog"></i> <span>Manage Users</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/skill-academy/admin/manage_courses.php" class="sidebar-link <?php echo $current_page == 'manage_courses.php' ? 'active' : ''; ?>">
                    <i class="fas fa-book-open"></i> <span>Manage Courses</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i class="fas fa-money-check-alt"></i> <span>Transactions</span>
                </a>
            </li>
        <?php endif; ?>

        <li class="sidebar-heading mt-3">Account</li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link">
                <i class="fas fa-cog"></i> <span>Settings</span>
            </a>
        </li>
    </div>
    
    <div class="sidebar-footer">
        <a href="/skill-academy/logout.php" class="sidebar-link text-danger" style="background: rgba(220, 53, 69, 0.1);">
            <i class="fas fa-sign-out-alt"></i> <span>Log Out</span>
        </a>
    </div>
</aside>

<!-- Optional JavaScript to toggle sidebar on mobile devices if button is clicked -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggler = document.querySelector('.navbar-toggler');
        const sidebar = document.getElementById('dashboardSidebar');
        if(toggler && sidebar) {
            toggler.addEventListener('click', () => {
                sidebar.classList.toggle('d-none');
                sidebar.classList.toggle('show');
            });
        }
    });
</script>
