<?php
require_once 'config/db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get current tab and filter
$current_tab = $_GET['tab'] ?? 'batches';
$current_filter = $_GET['filter'] ?? 'all';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    try {
        // Get the JSON data from the form
        $student_ids = json_decode($_POST['student_ids'], true);
        $certification_status = json_decode($_POST['certification_status'], true);

        if (!empty($student_ids) && !empty($certification_status)) {
            // Prepare the update query
            $update_query = "UPDATE students SET certification_status = :status WHERE id = :id";
            $stmt = $pdo->prepare($update_query);
            
            // Update each student's status
            foreach ($student_ids as $student_id) {
                if (isset($certification_status[$student_id])) {
                    $status = $certification_status[$student_id];
                    // Validate status value
                    if (in_array($status, ['pending', 'approved', 'blocked'])) {
                        $stmt->execute([
                            ':status' => $status,
                            ':id' => $student_id
                        ]);
                    }
                }
            }
            
            $_SESSION['success_message'] = "Certification status updated successfully!";
        } else {
            $_SESSION['error_message'] = "No students selected for status update.";
        }
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Error updating certification status: " . $e->getMessage();
    }
    
    header("Location: certification.php?tab=" . $current_tab . "&filter=" . $current_filter);
    exit();
}

// Base query for students
$base_query = "SELECT s.*, 
              GROUP_CONCAT(DISTINCT w.title) as workshops,
              GROUP_CONCAT(DISTINCT b.batch_name) as batches,
              GROUP_CONCAT(DISTINCT i.title) as internships
              FROM students s
              LEFT JOIN workshop_enrollments we ON s.id = we.student_id
              LEFT JOIN workshops w ON we.workshop_id = w.id
              LEFT JOIN batch_enrollments be ON s.id = be.student_id
              LEFT JOIN batches b ON be.batch_id = b.id
              LEFT JOIN internship_enrollments ie ON s.id = ie.student_id
              LEFT JOIN internships i ON ie.internship_id = i.id";

// Add tab-specific conditions
switch ($current_tab) {
    case 'batches':
        $base_query .= " WHERE b.id IS NOT NULL";
        break;
    case 'workshops':
        $base_query .= " WHERE w.id IS NOT NULL";
        break;
    case 'internships':
        $base_query .= " WHERE i.id IS NOT NULL";
        break;
}

// Add filter conditions
if ($current_filter !== 'all') {
    $base_query .= ($current_tab !== 'all' ? " AND" : " WHERE") . " s.certification_status = :filter";
}

$base_query .= " GROUP BY s.id ORDER BY s.name";

try {
    $stmt = $pdo->prepare($base_query);
    if ($current_filter !== 'all') {
        $stmt->bindParam(':filter', $current_filter);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group students by type for respective tabs
    if ($current_tab === 'internships' || $current_tab === 'batches' || $current_tab === 'workshops') {
        $grouped_result = [];
        foreach ($result as $row) {
            switch ($current_tab) {
                case 'internships':
                    if (!empty($row['internships'])) {
                        $items = explode(',', $row['internships']);
                        foreach ($items as $item) {
                            $item = trim($item);
                            if (!isset($grouped_result[$item])) {
                                $grouped_result[$item] = [];
                            }
                            $grouped_result[$item][] = $row;
                        }
                    }
                    break;
                case 'batches':
                    if (!empty($row['batches'])) {
                        $items = explode(',', $row['batches']);
                        foreach ($items as $item) {
                            $item = trim($item);
                            if (!isset($grouped_result[$item])) {
                                $grouped_result[$item] = [];
                            }
                            $grouped_result[$item][] = $row;
                        }
                    }
                    break;
                case 'workshops':
                    if (!empty($row['workshops'])) {
                        $items = explode(',', $row['workshops']);
                        foreach ($items as $item) {
                            $item = trim($item);
                            if (!isset($grouped_result[$item])) {
                                $grouped_result[$item] = [];
                            }
                            $grouped_result[$item][] = $row;
                        }
                    }
                    break;
            }
        }
        $result = $grouped_result;
    }
} catch(PDOException $e) {
    $_SESSION['error_message'] = "Error fetching student data.";
    $result = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certification Management</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
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
            margin: 2% auto;
            padding: 20px;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            max-height: 96vh;
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

        /* Badge styles */
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

        .badge-secondary {
            background-color: #6c757d;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-approved {
            background-color: #28a745;
            color: white;
        }

        .badge-blocked {
            background-color: #dc3545;
            color: white;
        }

        /* Form select styles */
        .form-select {
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 12px;
            color: #495057;
            background-color: #fff;
            cursor: pointer;
            transition: border-color 0.15s ease-in-out;
        }

        .form-select:focus {
            outline: none;
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .form-select option {
            padding: 8px;
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

        /* Header Content Styles */
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .header-content h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            width: 250px;
        }

        .search-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Filter section styles */
        .filter-section {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            background: #f8f9fa;
        }

        .filter-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        /* Certificate styles */
        .certificate {
            width: 185mm; /* A5 landscape width */
            height: 200mm; /* A5 landscape height */
            margin: 0 auto;
            padding: 20mm;
            background-image: url('images/certificate-bg.jpg');
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-family: 'Times New Roman', serif;
            background-color: #fff; /* Fallback background color */
            max-width: 100%;
            max-height: 100vh;
            object-fit: contain;
        }

        .certificate-content {
            position: relative;
            z-index: 2;
            padding: 40px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100%;
            width: 100%;
            position: absolute;
            top: 19%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .certificate-header {
            margin-bottom: 20px;
            text-align: center;
        }

        .certificate-title {
            left: 50%;

            font-size: 48px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .certificate-subtitle {
            font-size: 24px;
            color: #34495e;
            margin-bottom: 5px;
        }

        .certificate-body {
            margin: 20px 0;
            text-align: center;
        }

        .certificate-text {
            font-size: 20px;
            line-height: 1.6;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .student-name {
            left: 50%;

            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin: 15px 0;
            text-transform: uppercase;
        }

        .course-name {
            right: 50%;
            font-size: 24px;
            color: #34495e;
            margin-bottom: 15px;
        }

        .certificate-footer {
            margin-top: 20px;
            text-align: center;
        }

        .certificate-date {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }

        .signature-line {
            width: 200px;
            border-top: 2px solid #2c3e50;
            margin: 5px auto;
        }

        .signature-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 5px;
        }

        .signature-title {
            font-size: 16px;
            color: #7f8c8d;
        }

        .certificate-container {
            width: 297mm; /* A4 width */
            height: 210mm; /* A4 height */
            margin: 0 auto;
            padding: 20mm;
            background: #f5f5f5;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        #certificatesContainer {
            width: 297mm; /* A4 width */
            height: 210mm; /* A4 height */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 40px;
            padding: 20px;
            position: relative;
        }

        .page-break {
            page-break-after: always;
            margin: 40px 0;
        }

        /* Remove responsive styles for certificate container */
        @media screen and (max-width: 768px) {
            .certificate-container {
                width: 297mm;
                height: 210mm;
                padding: 20mm;
            }

            #certificatesContainer {
                width: 297mm;
                height: 210mm;
            }
        }

        @media print {
            .certificate-container {
                width: 297mm;
                height: 210mm;
                padding: 0;
                margin: 0;
            }

            #certificatesContainer {
                width: 297mm;
                height: 210mm;
                gap: 0;
                padding: 0;
            }
        }

        /* Modal styles for certificate */
        .modal-content.certificate-container {
            max-width: 100%;
            width: 100%;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .modal-footer {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            position: sticky;
            bottom: 0;
            background: #f5f5f5;
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .certificate-name {
            position: absolute;
            top: 78%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
            width: 100%;
            text-align: center;
        }

        .certificate-type {
            position: absolute;
            top: 88%;
            left: 110%;
            font-weight: bold;

            transform: translate(-50%, -50%);
            font-size: 13.5px;
            color: #34495e;
            margin: 0;
            text-align: left;
            width: 100%;
        }

        .certificate-dates {
            position: absolute;
            top: 94.8%;
            left: 46.5%;
            font-weight: bold;

            transform: translate(-50%, -50%);
            font-size: 13.5px;
            color: #34495e;
            margin: 0;
            text-align: center;
            width: 100%;
        }

        /* Remove any responsive styles that might affect these positions */
        @media screen and (max-width: 768px) {
            .certificate-name,
            .certificate-type,
            .certificate-dates {
                position: absolute;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 100%;
                text-align: center;
            }
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
                <li><a href="students.php">Students</a></li>
                <li><a href="certification.php" class="sidebar-btn active">Certification</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul> 
        
        </nav>

        <main class="content">
            <header>
                
            </header>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <div class="header-content">
                        <h2>Student Certification</h2>
                        <div class="header-actions">
                            <input type="text" class="search-input" id="searchInput" placeholder="Search students..." onkeyup="filterTable()">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tabs -->
                    <div class="tabs">
                        <button class="tab-btn <?php echo $current_tab === 'batches' ? 'active' : ''; ?>" 
                                onclick="switchTab('batches')">By Batches</button>
                        <button class="tab-btn <?php echo $current_tab === 'workshops' ? 'active' : ''; ?>" 
                                onclick="switchTab('workshops')">By Workshops</button>
                        <button class="tab-btn <?php echo $current_tab === 'internships' ? 'active' : ''; ?>" 
                                onclick="switchTab('internships')">By Internships</button>
                    </div>

                    <div class="filter-section">
                        <button class="filter-btn <?php echo $current_filter === 'all' ? 'active' : ''; ?>" 
                                onclick="applyFilter('all')">All</button>
                        <button class="filter-btn <?php echo $current_filter === 'pending' ? 'active' : ''; ?>" 
                                onclick="applyFilter('pending')">Pending</button>
                        <button class="filter-btn <?php echo $current_filter === 'approved' ? 'active' : ''; ?>" 
                                onclick="applyFilter('approved')">Approved</button>
                        <button class="filter-btn <?php echo $current_filter === 'blocked' ? 'active' : ''; ?>" 
                                onclick="applyFilter('blocked')">Blocked</button>
                    </div>

                    <form method="POST" id="certificationForm">
                        <input type="hidden" name="action" value="update_status">
                        <div class="table-responsive">
                            <?php foreach ($result as $title => $students): ?>
                                <div class="section-title"><?php echo htmlspecialchars($title); ?></div>
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" class="selectAll" onchange="toggleAllCheckboxes(this, '<?php echo md5($title); ?>')"></th>
                                            <th>Student Name</th>
                                            <th>Email</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $row): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" 
                                                           name="student_ids[]" 
                                                           value="<?php echo $row['id']; ?>"
                                                           class="student-checkbox <?php echo md5($title); ?>"
                                                           <?php echo ($row['certification_status'] ?? '') === 'blocked' ? 'disabled' : ''; ?>>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td>
                                                    <?php
                                                    switch ($current_tab) {
                                                        case 'batches':
                                                            echo '<span class="badge badge-success">Batch</span>';
                                                            break;
                                                        case 'workshops':
                                                            echo '<span class="badge badge-primary">Workshop</span>';
                                                            break;
                                                        case 'internships':
                                                            echo '<span class="badge badge-warning">Internship</span>';
                                                            break;
                                                        default:
                                                            echo '<span class="badge badge-secondary">None</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <select name="certification_status[<?php echo $row['id']; ?>]" 
                                                            class="form-select" 
                                                            onchange="updateStatus(this, <?php echo $row['id']; ?>)">
                                                        <option value="pending" <?php echo ($row['certification_status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="approved" <?php echo ($row['certification_status'] ?? '') === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                        <option value="blocked" <?php echo ($row['certification_status'] ?? '') === 'blocked' ? 'selected' : ''; ?>>Blocked</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button type="button" class="btn-view" onclick="viewCertificate(<?php echo $row['id']; ?>)">
                                                            View
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-submit" onclick="approveSelected()">Approve Selected</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Certificate Modal -->
    <div class="modal" id="certificateModal">
        <div class="modal-content certificate-container">
            <span class="close-btn" onclick="closeCertificateModal()">&times;</span>
            <div id="certificatesContainer">
                <!-- Certificates will be listed here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCertificateModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="downloadCertificate()">Download Certificate</button>
            </div>
        </div>
    </div>

    <!-- Add New Modal -->
    <div class="modal" id="addModal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeAddModal()">&times;</span>
            <h2>Add New Certification</h2>
            <form id="addForm">
                <div class="form-group">
                    <label for="student_name">Student Name</label>
                    <input type="text" id="student_name" name="student_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="blocked">Blocked</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Add Certification</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const tables = document.querySelectorAll('.data-table');

            tables.forEach(table => {
                const rows = table.getElementsByTagName('tr');
                for (let i = 1; i < rows.length; i++) { // Start from 1 to skip header row
                    const nameCell = rows[i].getElementsByTagName('td')[1]; // Name column
                    const emailCell = rows[i].getElementsByTagName('td')[2]; // Email column
                    const typeCell = rows[i].getElementsByTagName('td')[3]; // Type column

                    if (nameCell && emailCell && typeCell) {
                        const name = nameCell.textContent || nameCell.innerText;
                        const email = emailCell.textContent || emailCell.innerText;
                        const type = typeCell.textContent || typeCell.innerText;

                        if (name.toLowerCase().indexOf(filter) > -1 || 
                            email.toLowerCase().indexOf(filter) > -1 || 
                            type.toLowerCase().indexOf(filter) > -1) {
                            rows[i].style.display = '';
                        } else {
                            rows[i].style.display = 'none';
                        }
                    }
                }
            });
        }

        function viewCertificate(studentId) {
            // Fetch certificate data from the server
            fetch(`get_certificate_data.php?student_id=${studentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    // Create certificates container
                    const container = document.getElementById('certificatesContainer');
                    container.innerHTML = '';

                    // Create a certificate for each program
                    data.certificates.forEach((cert, index) => {
                        const certificate = document.createElement('div');
                        certificate.className = 'certificate';
                        
                        // Determine program type based on the certificate data
                        let programType = '';
                        if (cert.type === 'batch') {
                            programType = 'Batch';
                        } else if (cert.type === 'workshop') {
                            programType = 'Workshop';
                        } else if (cert.type === 'internship') {
                            programType = 'Internship';
                        }

                        certificate.innerHTML = `
                            <div class="certificate-content">
                                <div class="certificate-name">${data.name}</div>
                                <div class="certificate-type">${programType}</div>
                                <div class="certificate-dates">
                                    ${formatDate(cert.start_date)} &nbsp &nbsp &nbsp  ${formatDate(cert.end_date)}
                                </div>
                                <div class="certificate-id">CERT-${String(studentId).padStart(6, '0')}</div>
                            </div>
                        `;
                        container.appendChild(certificate);

                        // Add a page break between certificates
                        if (index < data.certificates.length - 1) {
                            container.appendChild(document.createElement('div')).className = 'page-break';
                        }
                    });

                    // Show the modal
                    document.getElementById('certificateModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching certificate data.');
                });
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            const month = date.toLocaleDateString('en-US', {
                month: 'long'
            }).substring(0, 5); // Get only first 5 characters of month
            const year = date.getFullYear();
            return `${month} ${year}`;
        }

        function closeCertificateModal() {
            document.getElementById('certificateModal').style.display = 'none';
        }

        async function downloadCertificate() {
            try {
                const certificate = document.querySelector('.certificate');
                const canvas = await html2canvas(certificate, {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    backgroundColor: null
                });

                const blob = await new Promise(resolve => 
                    canvas.toBlob(resolve, 'image/jpeg', 0.95)
                );

                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'certificate.jpg';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Error downloading certificate:', error);
                alert('An error occurred while downloading the certificate.');
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('certificateModal');
            if (event.target == modal) {
                closeCertificateModal();
            }
        }

        function switchTab(tabName) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('tab', tabName);
            window.location.href = currentUrl.toString();
        }

        function applyFilter(filterValue) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('filter', filterValue);
            window.location.href = currentUrl.toString();
        }

        function toggleAllCheckboxes(source, titleHash) {
            const checkboxes = document.getElementsByClassName('student-checkbox ' + titleHash);
            for(let i = 0; i < checkboxes.length; i++) {
                if(!checkboxes[i].disabled) {
                    checkboxes[i].checked = source.checked;
                }
            }
        }

        function updateStatus(select, studentId) {
            const newStatus = select.value;
            const confirmMessage = `Are you sure you want to ${newStatus} this student's certification?`;
            
            if (confirm(confirmMessage)) {
                const formData = new FormData();
                formData.append('action', 'update_status');
                formData.append('student_ids', JSON.stringify([studentId]));
                formData.append('certification_status', JSON.stringify({[studentId]: newStatus}));
                
                fetch('certification.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        throw new Error('Network response was not ok');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the status.');
                });
            } else {
                // Reset the select to previous value if user cancels
                select.value = select.getAttribute('data-previous-value') || 'pending';
            }
        }

        function approveSelected() {
            const selectedStudents = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(checkbox => checkbox.value);
            
            if (selectedStudents.length === 0) {
                alert('Please select at least one student to approve.');
                return;
            }
            
            if (confirm(`Are you sure you want to approve ${selectedStudents.length} selected students?`)) {
                const statusUpdates = {};
                selectedStudents.forEach(studentId => {
                    statusUpdates[studentId] = 'approved';
                });
                
                const formData = new FormData();
                formData.append('action', 'update_status');
                formData.append('student_ids', JSON.stringify(selectedStudents));
                formData.append('certification_status', JSON.stringify(statusUpdates));
                
                fetch('certification.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        throw new Error('Network response was not ok');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving students.');
                });
            }
        }

        // Store previous value when status changes
        document.querySelectorAll('.form-select').forEach(select => {
            select.addEventListener('change', function() {
                this.setAttribute('data-previous-value', this.value);
            });
        });

        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function editStudent(id) {
            // Implement edit functionality
            console.log('Edit student:', id);
        }

        function deleteStudent(id) {
            if(confirm('Are you sure you want to delete this student?')) {
                // Implement delete functionality
                console.log('Delete student:', id);
            }
        }

        // Handle form submission
        document.getElementById('addForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Implement form submission logic here
            closeAddModal();
        });

        // Mobile menu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
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
</body>
</html> 