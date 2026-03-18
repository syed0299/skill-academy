<?php
// register.php
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $role = sanitizeInput($_POST['role'] ?? 'student');

    if (!empty($name) && !empty($email) && !empty($password)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Strictly check roles to prevent injection of 'admin' role
            if (!in_array($role, ['student', 'instructor'])) {
                $role = 'student';
            }

            if (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters long.';
            } else {
                try {
                    // Check if email already exists using a prepared statement
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                    $stmt->execute([$email]);
                    
                    if ($stmt->fetch()) {
                        $error = 'An account with this email is already registered.';
                    } else {
                        // Securely hash the password using bcrypt
                        $hash = password_hash($password, PASSWORD_BCRYPT);
                        
                        // Insert new user using a prepared statement
                        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                        if ($stmt->execute([$name, $email, $hash, $role])) {
                            $success = 'Registration successful! You can now <a href="login.php" class="fw-bold alert-link">log in</a>.';
                        } else {
                            $error = 'Something went wrong during registration. Please try again.';
                        }
                    }
                } catch (PDOException $e) {
                    error_log("Registration error: " . $e->getMessage());
                    $error = 'A database error occurred. Please try again later.';
                }
            }
        } else {
            $error = 'Please enter a valid email address.';
        }
    } else {
        $error = 'Please fill in all the required fields.';
    }
}

require_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center mt-4">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0 p-md-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold">Create an Account</h3>
                        <p class="text-muted">Join Skill Academy and start learning today.</p>
                    </div>
                    
                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger shadow-sm border-0"><i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if(!empty($success)): ?>
                        <div class="alert alert-success shadow-sm border-0"><i class="fas fa-check-circle me-2"></i> <?php echo $success; ?></div>
                    <?php else: ?>
                    
                        <form method="POST" action="register.php">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-lg" placeholder="John Doe" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg" placeholder="name@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg" placeholder="Min. 6 characters" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">I want to be a:</label>
                                <select name="role" class="form-select form-select-lg border-0 bg-light shadow-sm">
                                    <option value="student" <?php echo (isset($_POST['role']) && $_POST['role'] == 'student') ? 'selected' : ''; ?>>Student</option>
                                    <option value="instructor" <?php echo (isset($_POST['role']) && $_POST['role'] == 'instructor') ? 'selected' : ''; ?>>Instructor</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3 shadow-sm">
                                <i class="fas fa-user-plus me-2"></i> Sign Up
                            </button>
                            <p class="text-center mb-0 mt-4">
                                Already have an account? <a href="login.php" class="text-decoration-none fw-bold">Log in</a>
                            </p>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
