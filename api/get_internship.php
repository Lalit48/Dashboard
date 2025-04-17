<?php
require_once '../config/db_connect.php';

if(isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM internships WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $internship = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($internship) {
            echo json_encode($internship);
        } else {
            echo json_encode(['error' => 'Internship not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?> 