<?php
require_once '../config/db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'Student ID is required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Delete workshop enrollments
    $stmt = $pdo->prepare("DELETE FROM workshop_enrollments WHERE student_id = ?");
    $stmt->execute([$_POST['id']]);

    // Delete internship enrollments
    $stmt = $pdo->prepare("DELETE FROM internship_enrollments WHERE student_id = ?");
    $stmt->execute([$_POST['id']]);

    // Delete student
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$_POST['id']]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?> 