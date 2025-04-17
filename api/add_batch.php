<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO batches (batch_name, start_date, end_date, capacity, status) VALUES (?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['batch_name'],
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['capacity'],
            $_POST['status'] ?? 'upcoming' // Default status if not provided
        ]);

        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?> 