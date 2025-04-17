<?php
require_once '../config/db_connect.php';

if(isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT b.*, 
                   (SELECT COUNT(*) FROM students WHERE batch_id = b.id) as current_enrollment
            FROM batches b 
            WHERE b.id = ?
        ");
        $stmt->execute([$_GET['id']]);
        $batch = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($batch) {
            echo json_encode($batch);
        } else {
            echo json_encode(['error' => 'Batch not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?> 