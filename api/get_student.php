<?php
require_once '../config/db_connect.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Student ID is required']);
    exit;
}

$id = $_GET['id'];

try {
    // Get student details
    $stmt = $pdo->prepare("
        SELECT s.* 
        FROM students s 
        WHERE s.id = ?
    ");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(['error' => 'Student not found']);
        exit;
    }

    // Get enrolled batches
    $stmt = $pdo->prepare("
        SELECT b.id, b.batch_name 
        FROM batches b
        JOIN batch_enrollments be ON b.id = be.batch_id
        WHERE be.student_id = ?
    ");
    $stmt->execute([$id]);
    $student['batches'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get enrolled workshops
    $stmt = $pdo->prepare("
        SELECT w.id, w.title 
        FROM workshops w
        JOIN workshop_enrollments we ON w.id = we.workshop_id
        WHERE we.student_id = ?
    ");
    $stmt->execute([$id]);
    $student['workshops'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get enrolled internships
    $stmt = $pdo->prepare("
        SELECT i.id, i.title 
        FROM internships i
        JOIN internship_enrollments ie ON i.id = ie.internship_id
        WHERE ie.student_id = ?
    ");
    $stmt->execute([$id]);
    $student['internships'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($student);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 