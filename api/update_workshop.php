<?php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE workshops 
            SET title = ?, 
                description = ?, 
                workshop_date = ?, 
                duration = ?, 
                capacity = ?, 
                instructor = ?,
                status = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $_POST['title'],
            $_POST['description'],
            $_POST['workshop_date'],
            $_POST['duration'],
            $_POST['capacity'],
            $_POST['instructor'],
            $_POST['status'],
            $_POST['workshop_id']
        ]);

        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?> 