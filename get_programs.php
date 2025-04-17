<?php
require_once 'config/db_connect.php';

// Check if type parameter is provided
if (!isset($_GET['type'])) {
    echo json_encode(['error' => 'Program type is required']);
    exit;
}

$type = $_GET['type'];

try {
    switch ($type) {
        case 'batch':
            $stmt = $pdo->query("SELECT id, batch_name as name FROM batches WHERE status='active'");
            break;
        case 'workshop':
            $stmt = $pdo->query("SELECT id, title as name FROM workshops WHERE status='scheduled'");
            break;
        case 'internship':
            $stmt = $pdo->query("SELECT id, title as name FROM internships WHERE status='active'");
            break;
        default:
            echo json_encode(['error' => 'Invalid program type']);
            exit;
    }

    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($programs);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 