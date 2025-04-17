<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE internships 
            SET title = ?, description = ?, start_date = ?, 
                end_date = ?, duration = ?, stipend = ?, 
                location = ?, requirements = ?, status = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $_POST['title'],
            $_POST['description'],
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['duration'],
            $_POST['stipend'],
            $_POST['location'],
            $_POST['requirements'],
            $_POST['status'],
            $_POST['internship_id']
        ]);

        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?> 