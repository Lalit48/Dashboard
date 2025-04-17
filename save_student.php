<?php
// Assuming you have already established a database connection
include 'db_connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $choice = $_POST['choice'];

    // Determine the selected ID based on the choice
    switch ($choice) {
        case 'batch':
            $id = $_POST['batch_id'];
            $sql = "INSERT INTO student_batches (student_id, batch_id) VALUES (:student_id, :id)";
            break;
        case 'workshop':
            $id = $_POST['workshop_id'];
            $sql = "INSERT INTO student_workshops (student_id, workshop_id) VALUES (:student_id, :id)";
            break;
        case 'internship':
            $id = $_POST['internship_id'];
            $sql = "INSERT INTO student_internships (student_id, internship_id) VALUES (:student_id, :id)";
            break;
        default:
            die('Invalid selection type');
    }

    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':student_id' => $studentId,
        ':id' => $id
    ]);

    // Redirect or show a success message
    header('Location: success.php'); // Redirect to a success page
    exit();
}
?>
