<?php require_once 'config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Add Font Awesome for icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        /* Tab styles */
        .tabs-container {
            margin-bottom: 20px;
        }
        
        .tabs {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 20px;
        }
        
        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            color: #666;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.3s ease;
        }
        
        .tab-btn:hover {
            color: #007bff;
        }
        
        .tab-btn.active {
            color: #007bff;
            border-bottom: 2px solid #007bff;
        }
        
        .tab-content {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .tab-content.active {
            display: block;
            opacity: 1;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .close-btn {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .close-btn:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-group select[multiple] {
            height: 100px;
        }

        .form-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-submit,
        .btn-cancel {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-submit {
            background-color: #007bff;
            color: white;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }

        .detail-row {
            margin-bottom: 15px;
            display: flex;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .detail-label {
            font-weight: 500;
            width: 120px;
            color: #666;
        }

        .detail-value {
            flex: 1;
        }

        /* Add these styles to the existing style section */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            margin-right: 4px;
            color: white;
        }

        .badge-primary {
            background-color: #007bff;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }

        .type-column {
            min-width: 200px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .data-table th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .data-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-view, .btn-edit, .btn-delete {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .btn-view {
            background-color: #17a2b8;
            color: white;
        }

        .btn-edit {
            background-color: #ffc107;
            color: #000;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-view:hover { background-color: #138496; }
        .btn-edit:hover { background-color: #e0a800; }
        .btn-delete:hover { background-color: #c82333; }

        /* Improve modal styling */
        .modal-content {
            margin: 2% auto;
            max-height: 96vh;
        }

        .form-group select[multiple] {
            height: 120px;
        }

        /* Improve header styling */
        .header-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .search-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <button class="menu-toggle">
        <i class="fa fa-bars"></i>
    </button>
    <div class="container">
        <nav class="sidebar">
            <div class="logo">Admin Panel</div>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="batches.php">Batches</a></li>
                <li><a href="workshops.php">Workshops</a></li>
                <li><a href="internships.php">Internships</a></li>
                <li><a href="students.php" class="sidebar-btn active">Students</a></li>
                <li><a href="certification.php">Certification</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <main class="content">
            <header>
                <div class="header-content">
                    <h1>Student Management</h1>
                    <div class="header-actions">
                        <input type="text" id="searchStudent" placeholder="Search students..." class="search-input">
                        <button type="button" onclick="showAddStudentForm()" class="btn-primary">Add New Student</button>
                    </div>
                </div>
            </header>

            <!-- Tabs -->
            <div class="tabs-container">
                <div class="tabs">
                    <button type="button" class="tab-btn active" onclick="switchTab('all')">All Students</button>
                    <button type="button" class="tab-btn" onclick="switchTab('batches')">By Batches</button>
                    <button type="button" class="tab-btn" onclick="switchTab('workshops')">By Workshops</button>
                    <button type="button" class="tab-btn" onclick="switchTab('internships')">By Internships</button>
                </div>

                <!-- All Students Tab -->
                <div id="all" class="tab-content active">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody">
                                <?php
                                $stmt = $pdo->query("
                                    SELECT 
                                        s.*,
                                        GROUP_CONCAT(DISTINCT 
                                            CASE 
                                                WHEN be.id IS NOT NULL THEN 'Batch'
                                                WHEN we.id IS NOT NULL THEN 'Workshop'
                                                WHEN ie.id IS NOT NULL THEN 'Internship'
                                            END
                                        ) as enrollment_types
                                    FROM students s 
                                    LEFT JOIN batch_enrollments be ON s.id = be.student_id
                                    LEFT JOIN workshop_enrollments we ON s.id = we.student_id
                                    LEFT JOIN internship_enrollments ie ON s.id = ie.student_id
                                    GROUP BY s.id
                                    ORDER BY s.name
                                ");
                                while ($row = $stmt->fetch()) {
                                    $types = explode(',', $row['enrollment_types']);
                                    $typeHtml = '';
                                    foreach ($types as $type) {
                                        $badgeClass = '';
                                        switch ($type) {
                                            case 'Batch':
                                                $badgeClass = 'badge-primary';
                                                break;
                                            case 'Workshop':
                                                $badgeClass = 'badge-success';
                                                break;
                                            case 'Internship':
                                                $badgeClass = 'badge-warning';
                                                break;
                                        }
                                        $typeHtml .= "<span class='badge {$badgeClass}'>{$type}</span> ";
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td>{$row['name']}</td>";
                                    echo "<td>{$row['email']}</td>";
                                    echo "<td>{$row['phone']}</td>";
                                    echo "<td class='type-column'>{$typeHtml}</td>";
                                    echo "<td class='action-buttons'>
                                            <button onclick='viewStudentDetails({$row['id']})' class='btn-view'>View</button>
                                            <button onclick='editStudent({$row['id']})' class='btn-edit'>Edit</button>
                                            <button onclick='deleteStudent({$row['id']})' class='btn-delete'>Delete</button>
                                          </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Batches Tab -->
                <div id="batches" class="tab-content">
                    <?php
                    $batchStmt = $pdo->query("SELECT * FROM batches WHERE status='active'");
                    while ($batch = $batchStmt->fetch()) {
                        echo "<div class='batch-section'>";
                        echo "<h3 class='batch-title'>{$batch['batch_name']}</h3>";
                        echo "<div class='table-responsive'>";
                        echo "<table class='data-table'>";
                        echo "<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Type</th><th>Actions</th></tr></thead>";
                        echo "<tbody>";
                        
                        $studentStmt = $pdo->prepare("
                            SELECT 
                                s.*,
                                GROUP_CONCAT(DISTINCT 
                                    CASE 
                                        WHEN be2.id IS NOT NULL THEN 'Batch'
                                        WHEN we.id IS NOT NULL THEN 'Workshop'
                                        WHEN ie.id IS NOT NULL THEN 'Internship'
                                    END
                                ) as enrollment_types
                            FROM students s
                            JOIN batch_enrollments be ON s.id = be.student_id
                            LEFT JOIN batch_enrollments be2 ON s.id = be2.student_id
                            LEFT JOIN workshop_enrollments we ON s.id = we.student_id
                            LEFT JOIN internship_enrollments ie ON s.id = ie.student_id
                            WHERE be.batch_id = ?
                            GROUP BY s.id
                            ORDER BY s.name
                        ");
                        $studentStmt->execute([$batch['id']]);
                        
                        while ($student = $studentStmt->fetch()) {
                            $types = explode(',', $student['enrollment_types']);
                            $typeHtml = '';
                            foreach ($types as $type) {
                                $badgeClass = '';
                                switch ($type) {
                                    case 'Batch':
                                        $badgeClass = 'badge-primary';
                                        break;
                                    case 'Workshop':
                                        $badgeClass = 'badge-success';
                                        break;
                                    case 'Internship':
                                        $badgeClass = 'badge-warning';
                                        break;
                                }
                                $typeHtml .= "<span class='badge {$badgeClass}'>{$type}</span> ";
                            }
                            
                            echo "<tr>";
                            echo "<td>{$student['name']}</td>";
                            echo "<td>{$student['email']}</td>";
                            echo "<td>{$student['phone']}</td>";
                            echo "<td class='type-column'>{$typeHtml}</td>";
                            echo "<td class='action-buttons'>
                                    <button onclick='viewStudentDetails({$student['id']})' class='btn-view'>View</button>
                                    <button onclick='editStudent({$student['id']})' class='btn-edit'>Edit</button>
                                    <button onclick='deleteStudent({$student['id']})' class='btn-delete'>Delete</button>
                                  </td>";
                            echo "</tr>";
                        }
                        
                        echo "</tbody></table></div></div>";
                    }
                    ?>
                </div>

                <!-- Workshops Tab -->
                <div id="workshops" class="tab-content">
                    <?php
                    $workshopStmt = $pdo->query("SELECT * FROM workshops WHERE status='scheduled'");
                    while ($workshop = $workshopStmt->fetch()) {
                        echo "<div class='workshop-section'>";
                        echo "<h3 class='workshop-title'>{$workshop['title']}</h3>";
                        echo "<div class='table-responsive'>";
                        echo "<table class='data-table'>";
                        echo "<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Type</th><th>Actions</th></tr></thead>";
                        echo "<tbody>";
                        
                        $studentStmt = $pdo->prepare("
                            SELECT 
                                s.*,
                                GROUP_CONCAT(DISTINCT 
                                    CASE 
                                        WHEN be.id IS NOT NULL THEN 'Batch'
                                        WHEN we2.id IS NOT NULL THEN 'Workshop'
                                        WHEN ie.id IS NOT NULL THEN 'Internship'
                                    END
                                ) as enrollment_types
                            FROM students s
                            JOIN workshop_enrollments we ON s.id = we.student_id
                            LEFT JOIN batch_enrollments be ON s.id = be.student_id
                            LEFT JOIN workshop_enrollments we2 ON s.id = we2.student_id
                            LEFT JOIN internship_enrollments ie ON s.id = ie.student_id
                            WHERE we.workshop_id = ?
                            GROUP BY s.id
                            ORDER BY s.name
                        ");
                        $studentStmt->execute([$workshop['id']]);
                        
                        while ($student = $studentStmt->fetch()) {
                            $types = explode(',', $student['enrollment_types']);
                            $typeHtml = '';
                            foreach ($types as $type) {
                                $badgeClass = '';
                                switch ($type) {
                                    case 'Batch':
                                        $badgeClass = 'badge-primary';
                                        break;
                                    case 'Workshop':
                                        $badgeClass = 'badge-success';
                                        break;
                                    case 'Internship':
                                        $badgeClass = 'badge-warning';
                                        break;
                                }
                                $typeHtml .= "<span class='badge {$badgeClass}'>{$type}</span> ";
                            }
                            
                            echo "<tr>";
                            echo "<td>{$student['name']}</td>";
                            echo "<td>{$student['email']}</td>";
                            echo "<td>{$student['phone']}</td>";
                            echo "<td class='type-column'>{$typeHtml}</td>";
                            echo "<td class='action-buttons'>
                                    <button onclick='viewStudentDetails({$student['id']})' class='btn-view'>View</button>
                                    <button onclick='editStudent({$student['id']})' class='btn-edit'>Edit</button>
                                    <button onclick='deleteStudent({$student['id']})' class='btn-delete'>Delete</button>
                                  </td>";
                            echo "</tr>";
                        }
                        
                        echo "</tbody></table></div></div>";
                    }
                    ?>
                </div>

                <!-- Internships Tab -->
                <div id="internships" class="tab-content">
                    <?php
                    $internshipStmt = $pdo->query("SELECT * FROM internships WHERE status='active'");
                    while ($internship = $internshipStmt->fetch()) {
                        echo "<div class='internship-section'>";
                        echo "<h3 class='internship-title'>{$internship['title']}</h3>";
                        echo "<div class='table-responsive'>";
                        echo "<table class='data-table'>";
                        echo "<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Type</th><th>Actions</th></tr></thead>";
                        echo "<tbody>";
                        
                        $studentStmt = $pdo->prepare("
                            SELECT 
                                s.*,
                                GROUP_CONCAT(DISTINCT 
                                    CASE 
                                        WHEN be.id IS NOT NULL THEN 'Batch'
                                        WHEN we.id IS NOT NULL THEN 'Workshop'
                                        WHEN ie2.id IS NOT NULL THEN 'Internship'
                                    END
                                ) as enrollment_types
                            FROM students s
                            JOIN internship_enrollments ie ON s.id = ie.student_id
                            LEFT JOIN batch_enrollments be ON s.id = be.student_id
                            LEFT JOIN workshop_enrollments we ON s.id = we.student_id
                            LEFT JOIN internship_enrollments ie2 ON s.id = ie2.student_id
                            WHERE ie.internship_id = ?
                            GROUP BY s.id
                            ORDER BY s.name
                        ");
                        $studentStmt->execute([$internship['id']]);
                        
                        while ($student = $studentStmt->fetch()) {
                            $types = explode(',', $student['enrollment_types']);
                            $typeHtml = '';
                            foreach ($types as $type) {
                                $badgeClass = '';
                                switch ($type) {
                                    case 'Batch':
                                        $badgeClass = 'badge-primary';
                                        break;
                                    case 'Workshop':
                                        $badgeClass = 'badge-success';
                                        break;
                                    case 'Internship':
                                        $badgeClass = 'badge-warning';
                                        break;
                                }
                                $typeHtml .= "<span class='badge {$badgeClass}'>{$type}</span> ";
                            }
                            
                            echo "<tr>";
                            echo "<td>{$student['name']}</td>";
                            echo "<td>{$student['email']}</td>";
                            echo "<td>{$student['phone']}</td>";
                            echo "<td class='type-column'>{$typeHtml}</td>";
                            echo "<td class='action-buttons'>
                                    <button onclick='viewStudentDetails({$student['id']})' class='btn-view'>View</button>
                                    <button onclick='editStudent({$student['id']})' class='btn-edit'>Edit</button>
                                    <button onclick='deleteStudent({$student['id']})' class='btn-delete'>Delete</button>
                                  </td>";
                            echo "</tr>";
                        }
                        
                        echo "</tbody></table></div></div>";
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Student Modal -->
    <div id="studentModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeStudentModal()">&times;</span>
            <h2 id="modalTitle">Add New Student</h2>
            <form id="studentForm" method="POST" action="save_student.php">
                <input type="hidden" name="student_id" id="studentId">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="tel" name="phone" id="phone" required>
                </div>
                <div class="form-group">
                    <label>Choose one:</label>
                    <div>
                        <input type="radio" name="choice" id="choiceBatch" value="batch" required>
                        <label for="choiceBatch">Batch</label>
                        <select name="batch_id" id="batchIds" disabled>
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
                        <select name="workshop_id" id="workshopIds" disabled>
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
                        <select name="internship_id" id="internshipIds" disabled>
                            <?php
                            $internships = $pdo->query("SELECT id, title FROM internships WHERE status='active'");
                            while ($internship = $internships->fetch()) {
                                echo "<option value='{$internship['id']}'>{$internship['title']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div id="validationMessage" class="validation-message" style="display: none; color: red; margin-bottom: 10px;"></div>
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Save Student</button>
                    <button type="button" onclick="closeStudentModal()" class="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Student Details Modal -->
    <div id="viewStudentModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeViewModal()">&times;</span>
            <h2>Student Details</h2>
            <div id="studentDetails" class="student-details">
                <!-- Details will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            // Checkbox functionality
            document.querySelectorAll('input[name="choice[]"]').forEach((checkbox) => {
                checkbox.addEventListener('change', function() {
                    // Disable all selects initially
                    document.getElementById('batchIds').disabled = true;
                    document.getElementById('workshopIds').disabled = true;
                    document.getElementById('internshipIds').disabled = true;

                    // Enable the corresponding select if the checkbox is checked
                    if (this.checked) {
                        if (this.value === 'batch') {
                            document.getElementById('batchIds').disabled = false;
                        } else if (this.value === 'workshop') {
                            document.getElementById('workshopIds').disabled = false;
                        } else if (this.value === 'internship') {
                            document.getElementById('internshipIds').disabled = false;
                        }
                    }
                });
            });

            // Form validation
            document.getElementById('studentForm').addEventListener('submit', function(event) {
                const checkboxes = document.querySelectorAll('input[name="choice[]"]:checked');
                if (checkboxes.length !== 1) {
                    event.preventDefault();
                    document.getElementById('validationMessage').style.display = 'block';
                    document.getElementById('validationMessage').innerText = 'Please select exactly one option.';
                } else {
                    document.getElementById('validationMessage').style.display = 'none';
                }
            });

            // Radio button functionality
            document.querySelectorAll('input[name="choice"]').forEach((radio) => {
                radio.addEventListener('change', function() {
                    document.getElementById('batchIds').disabled = this.value !== 'batch';
                    document.getElementById('workshopIds').disabled = this.value !== 'workshop';
                    document.getElementById('internshipIds').disabled = this.value !== 'internship';
                });
            });

            // Mobile menu toggle functionality
            const menuToggle = document.querySelector('.menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            const content = document.querySelector('.content');

            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                content.classList.toggle('shifted');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                        sidebar.classList.remove('active');
                        content.classList.remove('shifted');
                    }
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('active');
                    content.classList.remove('shifted');
                }
            });
        });

    </script>
    <script src="js/students.js"></script>
</body>
</html> 