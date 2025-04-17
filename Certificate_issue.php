<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add/Edit Student</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .validation-message {
            display: none;
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php
// Include your database connection file
require_once 'config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $choice = $_POST['choice'];

    // Check if the email already exists
    $stmt = $pdo->prepare("SELECT * FROM students WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $existingStudent = $stmt->fetch();

    if ($existingStudent) {
        echo "<div class='alert alert-danger' role='alert'>Email address already exists. Please use a different email.</div>";
    } else {
        // Insert student details into the students table
        $stmt = $pdo->prepare("INSERT INTO students (name, email, phone) VALUES (:name, :email, :phone)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone
        ]);

        // Get the last inserted student ID
        $studentId = $pdo->lastInsertId();

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
        echo "<div class='alert alert-success' role='alert'>Student saved successfully!</div>";
        // header('Location: success.php'); // Uncomment this line to redirect to a success page
        exit();
    }
}
?>

<div class="container mt-5">
    <h2>Add New Student</h2>
    <form id="studentForm" method="POST" action="">
        <input type="hidden" name="student_id" id="studentId">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" name="name" id="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="tel" class="form-control" name="phone" id="phone" required>
        </div>
        <div class="form-group">
            <label>Choose one:</label>
            <div>
                <input type="radio" name="choice" id="choiceBatch" value="batch" required>
                <label for="choiceBatch">Batch</label>
                <select name="batch_id" id="batchIds" class="form-control" disabled>
                    <?php
                    $batches = $pdo->query("SELECT id, batch_name FROM batches WHERE status='active'");
                    while ($batch = $batches->fetch()) {
                        echo "<option value='{$batch['id']}'>{$batch['batch_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <input type="radio" name="choice" id="choiceWorkshop" value="workshop">
                <label for="choiceWorkshop">Workshop</label>
                <select name="workshop_id" id="workshopIds" class="form-control" disabled>
                    <?php
                    $workshops = $pdo->query("SELECT id, title FROM workshops WHERE status='scheduled'");
                    while ($workshop = $workshops->fetch()) {
                        echo "<option value='{$workshop['id']}'>{$workshop['title']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <input type="radio" name="choice" id="choiceInternship" value="internship">
                <label for="choiceInternship">Internship</label>
                <select name="internship_id" id="internshipIds" class="form-control" disabled>
                    <?php
                    $internships = $pdo->query("SELECT id, title FROM internships WHERE status='active'");
                    while ($internship = $internships->fetch()) {
                        echo "<option value='{$internship['id']}'>{$internship['title']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div id="validationMessage" class="validation-message"></div>
        <div class="form-group text-right">
            <button type="submit" class="btn btn-success">Save Student</button>
            <button type="reset" class="btn btn-danger">Cancel</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.querySelectorAll('input[name="choice"]').forEach((elem) => {
        elem.addEventListener("change", function(event) {
            document.querySelectorAll('select').forEach((selectElem) => {
                selectElem.disabled = true;
            });
            const selectedValue = event.target.value;
            if (selectedValue === 'batch') {
                document.getElementById('batchIds').disabled = false;
            } else if (selectedValue === 'workshop') {
                document.getElementById('workshopIds').disabled = false;
            } else if (selectedValue === 'internship') {
                document.getElementById('internshipIds').disabled = false;
            }
        });
    });
</script>

</body>
</html>
