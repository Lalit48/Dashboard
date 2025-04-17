<?php
require_once 'config/db_connect.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: students.php');
    exit;
}

// Validate required fields
if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['program_type']) || empty($_POST['program_id'])) {
    $_SESSION['error_message'] = "All fields are required";
    header('Location: students.php');
    exit;
}

try {
    // Check database connection
    if (!$pdo) {
        throw new Exception("Database connection failed");
    }

    $pdo->beginTransaction();

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception("Email already exists");
    }

    // Insert student
    $stmt = $pdo->prepare("
        INSERT INTO students (name, email, phone)
        VALUES (?, ?, ?)
    ");
    
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $_POST['phone']
    ]);
    
    $studentId = $pdo->lastInsertId();

    // Add program enrollment based on type
    $programType = $_POST['program_type'];
    $programId = $_POST['program_id'];

    // Verify program exists before enrolling
    switch ($programType) {
        case 'batch':
            $stmt = $pdo->prepare("SELECT id FROM batches WHERE id = ? AND status = 'active'");
            $stmt->execute([$programId]);
            if ($stmt->rowCount() === 0) {
                throw new Exception("Invalid or inactive batch selected");
            }
            $stmt = $pdo->prepare("INSERT INTO batch_enrollments (student_id, batch_id) VALUES (?, ?)");
            break;
        case 'workshop':
            $stmt = $pdo->prepare("SELECT id FROM workshops WHERE id = ? AND status = 'scheduled'");
            $stmt->execute([$programId]);
            if ($stmt->rowCount() === 0) {
                throw new Exception("Invalid or inactive workshop selected");
            }
            $stmt = $pdo->prepare("INSERT INTO workshop_enrollments (student_id, workshop_id) VALUES (?, ?)");
            break;
        case 'internship':
            $stmt = $pdo->prepare("SELECT id FROM internships WHERE id = ? AND status = 'active'");
            $stmt->execute([$programId]);
            if ($stmt->rowCount() === 0) {
                throw new Exception("Invalid or inactive internship selected");
            }
            $stmt = $pdo->prepare("INSERT INTO internship_enrollments (student_id, internship_id) VALUES (?, ?)");
            break;
        default:
            throw new Exception("Invalid program type: " . $programType);
    }

    $stmt->execute([$studentId, $programId]);

    $pdo->commit();
    $_SESSION['success_message'] = "Student added successfully!";
    header('Location: students.php');
    exit;

} catch(Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
    header('Location: students.php');
    exit;
}
?> 