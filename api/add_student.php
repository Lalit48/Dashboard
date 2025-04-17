<?php
require_once '../config/db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
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

    // Add batch enrollments
    if (isset($_POST['batches']) && is_array($_POST['batches'])) {
        $stmt = $pdo->prepare("INSERT INTO batch_enrollments (student_id, batch_id) VALUES (?, ?)");
        foreach ($_POST['batches'] as $batchId) {
            $stmt->execute([$studentId, $batchId]);
        }
    }

    // Add workshop enrollments
    if (isset($_POST['workshops']) && is_array($_POST['workshops'])) {
        $stmt = $pdo->prepare("INSERT INTO workshop_enrollments (student_id, workshop_id) VALUES (?, ?)");
        foreach ($_POST['workshops'] as $workshopId) {
            $stmt->execute([$studentId, $workshopId]);
        }
    }

    // Add internship enrollments
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