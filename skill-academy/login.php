<?php
// login.php
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $role = getUserRole();
    if($role === 'admin') header('Location: dashboard/admin.php');
    elseif($role === 'instructor') header('Location: dashboard/instructor.php');
    else header('Location: dashboard/student.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                // Prepared statement to prevent SQL Injection
                $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                // Verify user exists and password is correct using password_verify
                if ($user && password_verify($password, $user['password'])) {
                    // Regenerate session ID to prevent session fixation attacks
                    session_regenerate_id(true);
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Route to correct dashboard
                    if($user['role'] === 'admin') header('Location: dashboard/admin.php');
                    elseif($user['role'] === 'instructor') header('Location: dashboard/instructor.php');
                    else header('Location: dashboard/student.php');
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            } catch (PDOException $e) {
                error_log("Login error: " . $e->getMessage());
                $error = 'An error occurred during login. Please try again.';
            }
        } else {
            $error = 'Please enter a valid email address.';
        }
    } else {
        $error = 'Please fill in both email and password fields.';
    }
}

require_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0 p-md-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold">Welcome Back</h3>
                        <p class="text-muted">Log in to continue your learning journey.</p>
                    </div>
                    
                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger shadow-sm border-0"><i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg" placeholder="name@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3 shadow-sm">
                            <i class="fas fa-sign-in-alt me-2"></i> Log In
                        </button>
                        <p class="text-center mb-0 mt-4">
                            Don't have an account? <a href="register.php" class="text-decoration-none fw-bold">Sign up</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
