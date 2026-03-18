<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session parameters before starting
    ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookie
    ini_set('session.use_only_cookies', 1); // Only use cookies for sessions
    // ini_set('session.cookie_secure', 1); // Uncomment if using HTTPS
    
    session_start();
}

/**
 * Check if the user is currently logged in.
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get the current user's role.
 * @return string|null
 */
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

/**
 * Require a specific role to access the page.
 * @param string $role
 */
function requireRole($role) {
    if (!isLoggedIn()) {
        header("Location: /skill-academy/login.php");
        exit;
    }
    
    if (getUserRole() !== $role) {
        // Log unauthorized access attempt here if necessary
        http_response_code(403);
        die("Unauthorized access. You do not have the required permissions.");
    }
}

/**
 * Require the user to be logged in to access the page.
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /skill-academy/login.php");
        exit;
    }
}

/**
 * Sanitize user input to prevent XSS
 * @param string $data
 * @return string
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
?>
