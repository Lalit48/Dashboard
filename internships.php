<?php require_once 'config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Management</title>
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

        /* Internship Status Badge Styles */
        .badge-open {
            background-color: #28a745;
            color: white;
        }

        .badge-closed {
            background-color: #dc3545;
            color: white;
        }

        .badge-upcoming {
            background-color: #17a2b8;
            color: white;
        }

        .badge-active {
            background-color: #28a745;
            color: white;
        }

        .badge-completed {
            background-color: #6c757d;
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
                <li><a href="internships.php" class="sidebar-btn active">Internships</a></li>
                <li><a href="students.php">Students</a></li>
                <li><a href="certification.php">Certification</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <main class="content">
            <header>
                <div class="header-content">
                    <h1>Internship Management</h1>
                    <div class="header-actions">
                        <input type="text" id="searchInternship" placeholder="Search internships..." class="search-input">
                        <button onclick="showAddInternshipForm()" class="btn-primary">
                            <i class="fa fa-plus"></i> Add New Internship
                        </button>
                    </div>
                </div>
            </header>

            <!-- Internship Status Filter -->
            <div class="filter-section">
                <button class="filter-btn active" onclick="filterInternships('all')">All</button>
                <button class="filter-btn" onclick="filterInternships('open')">Open</button>
                <button class="filter-btn" onclick="filterInternships('closed')">Closed</button>
                <button class="filter-btn" onclick="filterInternships('upcoming')">Upcoming</button>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Duration</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Stipend</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="internshipTableBody">
                        <?php
                        $stmt = $pdo->query("SELECT * FROM internships ORDER BY start_date DESC");
                        while ($row = $stmt->fetch()) {
                            echo "<tr data-status='{$row['status']}'>";
                            echo "<td>{$row['title']}</td>";
                            echo "<td>{$row['duration']} months</td>";
                            echo "<td>" . date('d M Y', strtotime($row['start_date'])) . "</td>";
                            echo "<td>" . date('d M Y', strtotime($row['end_date'])) . "</td>";
                            echo "<td>₹{$row['stipend']}</td>";
                            echo "<td>{$row['location']}</td>";
                            echo "<td><span class='badge badge-{$row['status']}'>{$row['status']}</span></td>";
                            echo "<td class='action-buttons'>
                                    <button onclick='viewInternshipDetails({$row['id']})' class='btn-view'>View</button>
                                    <button onclick='editInternship({$row['id']})' class='btn-edit'>Edit</button>
                                    <button onclick='deleteInternship({$row['id']})' class='btn-delete'>Delete</button>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add/Edit Internship Modal -->
    <div id="internshipModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeInternshipModal()">&times;</span>
            <h2 id="modalTitle">Add New Internship</h2>
            <form id="internshipForm">
                <input type="hidden" name="internship_id" id="internshipId">
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" name="title" id="title" required>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" id="description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Start Date:</label>
                    <input type="date" name="start_date" id="startDate" required>
                </div>
                <div class="form-group">
                    <label>End Date:</label>
                    <input type="date" name="end_date" id="endDate" required>
                </div>
                <div class="form-group">
                    <label>Duration (months):</label>
                    <input type="number" name="duration" id="duration" required min="1">
                </div>
                <div class="form-group">
                    <label>Stipend:</label>
                    <input type="number" name="stipend" id="stipend" required min="0">
                </div>
                <div class="form-group">
                    <label>Location:</label>
                    <input type="text" name="location" id="location" required>
                </div>
                <div class="form-group">
                    <label>Requirements:</label>
                    <textarea name="requirements" id="requirements" required></textarea>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" id="status" required>
                        <option value="upcoming">Upcoming</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Save Internship</button>
                    <button type="button" onclick="closeInternshipModal()" class="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Internship Details Modal -->
    <div id="viewInternshipModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeViewModal()">&times;</span>
            <h2>Internship Details</h2>
            <div id="internshipDetails" class="internship-details">
                <!-- Details will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <script>
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
    <script src="js/internships.js"></script>
</body>
</html>