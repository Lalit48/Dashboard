<?php
require_once 'config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

if (!isset($_GET['student_id'])) {
    http_response_code(400);
    exit('Student ID is required');
}

$student_id = $_GET['student_id'];

try {
    // Get student details and their programs
    $query = "SELECT s.*, 
              w.title as workshop_title,
              we.enrollment_date as workshop_start,
              b.batch_name as batch_title,
              be.enrollment_date as batch_start,
              i.title as internship_title,
              ie.enrollment_date as internship_start
              FROM students s
              LEFT JOIN workshop_enrollments we ON s.id = we.student_id
              LEFT JOIN workshops w ON we.workshop_id = w.id
              LEFT JOIN batch_enrollments be ON s.id = be.student_id
              LEFT JOIN batches b ON be.batch_id = b.id
              LEFT JOIN internship_enrollments ie ON s.id = ie.student_id
              LEFT JOIN internships i ON ie.internship_id = i.id
              WHERE s.id = :student_id
              AND (w.id IS NOT NULL OR b.id IS NOT NULL OR i.id IS NOT NULL)";

    $stmt = $pdo->prepare($query);
    $stmt->execute([':student_id' => $student_id]);
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($programs) {
        $student_name = $programs[0]['name'];
        $certificates = [];

        foreach ($programs as $program) {
            if (!empty($program['workshop_title'])) {
                $certificates[] = [
                    'type' => 'Workshop',
                    'title' => $program['workshop_title'],
                    'start_date' => $program['workshop_start'],
                    'end_date' => date('Y-m-d', strtotime($program['workshop_start'] . ' + 3 months')) // Example: 3 months duration
                ];
            }
            if (!empty($program['batch_title'])) {
                $certificates[] = [
                    'type' => 'Batch',
                    'title' => $program['batch_title'],
                    'start_date' => $program['batch_start'],
                    'end_date' => date('Y-m-d', strtotime($program['batch_start'] . ' + 6 months')) // Example: 6 months duration
                ];
            }
            if (!empty($program['internship_title'])) {
                $certificates[] = [
                    'type' => 'Internship',
                    'title' => $program['internship_title'],
                    'start_date' => $program['internship_start'],
                    'end_date' => date('Y-m-d', strtotime($program['internship_start'] . ' + 3 months')) // Example: 3 months duration
                ];
            }
        }

        $response = [
            'name' => $student_name,
            'certificates' => $certificates
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Student not found or no programs enrolled']);
    }
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 