    <?php $isDashboard = strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/courses/create_course.php') !== false; ?>
    
    <?php if($isDashboard): ?>
    </div> <!-- End wrapper from header.php -->
    <?php endif; ?>
    
    <?php if(!$isDashboard): ?>
    <!-- Standard Footer for public pages -->
    <footer class="bg-white border-top py-5 mt-auto">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6 pr-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="bg-primary text-white rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <span class="fs-4 fw-bold text-primary">SkillAcademy</span>
                    </div>
                    <p class="text-muted mb-4" style="line-height: 1.6;">
                        Empowering global learners with cutting-edge skills. Access world-class courses taught by industry leading experts.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-muted fs-5 hover-primary" style="transition: color 0.2s;"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-muted fs-5 hover-primary" style="transition: color 0.2s;"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-muted fs-5 hover-primary" style="transition: color 0.2s;"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-muted fs-5 hover-primary" style="transition: color 0.2s;"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="fw-bold mb-4 text-dark uppercase tracking-wider fs-7">Platform</h6>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                        <li><a href="/skill-academy/index.php" class="text-muted text-decoration-none" style="transition: color 0.2s;">Browse Courses</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Mentorship</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Certificates</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Pricing</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="fw-bold mb-4 text-dark uppercase tracking-wider fs-7">Company</h6>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                        <li><a href="#" class="text-muted text-decoration-none">About Us</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Careers</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Blog</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-bold mb-4 text-dark">Subscribe to Newsletter</h6>
                    <p class="text-muted mb-3">Get the latest course updates and special offers directly in your inbox.</p>
                    <form class="d-flex gap-2">
                        <input type="email" class="form-control" placeholder="Email address" style="max-width: 250px;">
                        <button class="btn btn-primary" type="button">Subscribe</button>
                    </form>
                </div>
            </div>
            
            <div class="row border-top mt-5 pt-4">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-muted mb-0 small">&copy; <?php echo date('Y'); ?> Skill Academy. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                    <ul class="list-inline mb-0 small">
                        <li class="list-inline-item"><a href="#" class="text-muted text-decoration-none">Privacy Policy</a></li>
                        <li class="list-inline-item"><span class="text-muted">&bull;</span></li>
                        <li class="list-inline-item"><a href="#" class="text-muted text-decoration-none">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <!-- Hover effect styles for public footer -->
    <style>
        .hover-primary:hover { color: var(--primary-color) !important; }
        footer a.text-muted:hover { color: var(--primary-color) !important; }
    </style>

    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar mobile toggle functionality can be added here
            const toggler = document.querySelector('.navbar-toggler');
            const sidebar = document.querySelector('.sidebar');
            
            if(toggler && sidebar && window.innerWidth < 992) {
                toggler.addEventListener('click', function(e) {
                    if(!document.getElementById('navbarContent').classList.contains('show')) {
                       // Custom behavior for sidebar toggling on mobile if needed
                    }
                });
            }
        });
    </script>
</body>
</html>
