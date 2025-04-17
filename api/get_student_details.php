<?php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

if(isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT s.*, b.batch_name 
            FROM students s 
            LEFT JOIN batches b ON s.batch_id = b.id 
            WHERE s.id = ?
        ");
        $stmt->execute([$_GET['id']]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($student) {
            echo json_encode($student);
        } else {
            echo json_encode(['error' => 'Student not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?> 