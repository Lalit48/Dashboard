<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO workshops (title, description, workshop_date, duration, capacity, instructor, status) VALUES (?, ?, ?, ?, ?, ?, 'scheduled')");
        
        $stmt->execute([
            $_POST['title'],
            $_POST['description'],
            $_POST['workshop_date'],
            $_POST['duration'],
            $_POST['capacity'],
            $_POST['instructor']
        ]);

        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?> 