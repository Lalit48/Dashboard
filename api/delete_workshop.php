<?php
header('Content-Type: application/json');
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['id'])) {
            throw new Exception('ID is required');
        }

        $stmt = $pdo->prepare("DELETE FROM workshops WHERE id = ?");
        $result = $stmt->execute([$_POST['id']]);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Failed to delete workshop');
        }
    } catch(Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?> 