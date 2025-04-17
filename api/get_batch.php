<?php
require_once '../config/db_connect.php';

if(isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM batches WHERE id = ?");
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