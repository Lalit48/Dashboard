<?php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

if(isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM workshops WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $workshop = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($workshop) {
            echo json_encode($workshop);
        } else {
            echo json_encode(['error' => 'Workshop not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?> 