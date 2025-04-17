<?php
require_once '../config/db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['student_id'])) {
    echo json_encode(['success' => false, 'error' => 'Student ID is required']);
    exit;
}

// Validate that at least one selection is made
$batches = $_POST['batches'] ?? [];
$workshops = $_POST['workshops'] ?? [];
$internships = $_POST['internships'] ?? [];

if (empty($batches) && empty($workshops) && empty($internships)) {
    echo json_encode(['success' => false, 'error' => 'Please select at least one Batch, Workshop, or Internship']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Update student details
    $stmt = $pdo->prepare("
        UPDATE students 
        SET name = ?, email = ?, phone = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['student_id']
    ]);

    $studentId = $_POST['student_id'];

    // Update batch enrollments
    $stmt = $pdo->prepare("DELETE FROM batch_enrollments WHERE student_id = ?");
    $stmt->execute([$studentId]);
    
    if (isset($_POST['batches']) && is_array($_POST['batches'])) {
        $stmt = $pdo->prepare("INSERT INTO batch_enrollments (student_id, batch_id) VALUES (?, ?)");
        foreach ($_POST['batches'] as $batchId) {
            $stmt->execute([$studentId, $batchId]);
        }
    }

    // Update workshop enrollments
    $stmt = $pdo->prepare("DELETE FROM workshop_enrollments WHERE student_id = ?");
    $stmt->execute([$studentId]);
    
    if (isset($_POST['workshops']) && is_array($_POST['workshops'])) {
        $stmt = $pdo->prepare("INSERT INTO workshop_enrollments (student_id, workshop_id) VALUES (?, ?)");
        foreach ($_POST['workshops'] as $workshopId) {
            $stmt->execute([$studentId, $workshopId]);
        }
    }

    // Update internship enrollments
    $stmt = $pdo->prepare("DELETE FROM internship_enrollments WHERE student_id = ?");
    $stmt->execute([$studentId]);
    
    if (isset($_POST['internships']) && is_array($_POST['internships'])) {
        $stmt = $pdo->prepare("INSERT INTO internship_enrollments (student_id, internship_id) VALUES (?, ?)");
        foreach ($_POST['internships'] as $internshipId) {
            $stmt->execute([$studentId, $internshipId]);
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?> 