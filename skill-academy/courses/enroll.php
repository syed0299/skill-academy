<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Strict POST check and require Student or Admin role to enroll (instructors shouldn't enroll into courses typically, but if allowed, modify role)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view_courses.php");
    exit;
}

requireLogin(); // Must be logged in

$user_id = $_SESSION['user_id'];
$course_id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
$role = getUserRole();

if (!$course_id) {
    header("Location: view_courses.php");
    exit;
}

// Instructors creating their own courses shouldn't enroll in them. 
// If they want to enroll in others, they would need a student account or logic allows it. Let's allow student or admin.
if ($role === 'instructor') {
    // Optionally block instructor, or let them redirect to dashboard
    header("Location: ../dashboard/instructor.php");
    exit;
}

try {
    // Check if course exists
    $stmt = $pdo->prepare("SELECT id, price FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    if (!$course) {
        die("Invalid Course.");
    }

    // Check if already enrolled
    $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    if (!$stmt->fetch()) {
        
        $pdo->beginTransaction();
        
        // 1. Create Enrollment
        $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $course_id]);
        $enrollment_id = $pdo->lastInsertId();
        
        // 2. Mock a Payment Record if price > 0 (for schema compatibility)
        if ($course['price'] > 0) {
            $transaction_id = 'TXN_' . strtoupper(bin2hex(random_bytes(8)));
            $stmt_payment = $pdo->prepare("INSERT INTO payments (enrollment_id, amount, payment_method, status, transaction_id) VALUES (?, ?, ?, ?, ?)");
            $stmt_payment->execute([$enrollment_id, $course['price'], 'Credit Card', 'completed', $transaction_id]);
        }
        
        $pdo->commit();
    }

    // Redirect to dashboard successfully
    if ($role === 'admin') {
         header("Location: ../dashboard/admin.php");
    } else {
         header("Location: ../dashboard/student.php?enrolled=success");
    }
    exit;

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Enrollment Error: " . $e->getMessage());
    die("An error occurred during enrollment. Please try again later.");
}
?>
