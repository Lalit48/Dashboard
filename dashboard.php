<?php
session_start();
require_once 'config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get dynamic data from database
try {
    // Get counts
    $batchCount = $pdo->query("SELECT COUNT(*) FROM batches WHERE status='active'")->fetchColumn();
    $workshopCount = $pdo->query("SELECT COUNT(*) FROM workshops WHERE status='scheduled'")->fetchColumn();
    $studentCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $certificationCount = $pdo->query("SELECT COUNT(*) FROM students WHERE certification_status='approved'")->fetchColumn();
    $internshipCount = $pdo->query("SELECT COUNT(*) FROM internships WHERE status='active'")->fetchColumn();
    
    // Get recent activities
    $recentActivities = $pdo->query("
        (SELECT 
            'New Batch Enrollment' as activity,
            s.name as student_name,
            b.batch_name as program_name,
            be.enrollment_date as activity_date
        FROM batch_enrollments be
        JOIN students s ON be.student_id = s.id
        JOIN batches b ON be.batch_id = b.id
        ORDER BY be.enrollment_date DESC
        LIMIT 5)
        UNION ALL
        (SELECT 
            'New Workshop Enrollment' as activity,
            s.name as student_name,
            w.title as program_name,
            we.enrollment_date as activity_date
        FROM workshop_enrollments we
        JOIN students s ON we.student_id = s.id
        JOIN workshops w ON we.workshop_id = w.id
        ORDER BY we.enrollment_date DESC
        LIMIT 5)
        UNION ALL
        (SELECT 
            'New Internship Enrollment' as activity,
            s.name as student_name,
            i.title as program_name,
            ie.enrollment_date as activity_date
        FROM internship_enrollments ie
        JOIN students s ON ie.student_id = s.id
        JOIN internships i ON ie.internship_id = i.id
        ORDER BY ie.enrollment_date DESC
        LIMIT 5)
        ORDER BY activity_date DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $batchCount = 0;
    $workshopCount = 0;
    $studentCount = 0;
    $certificationCount = 0;
    $internshipCount = 0;
    $recentActivities = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Add Font Awesome for icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            margin-top: 0;
            color: #333;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .card h3 i {
            margin-right: 10px;
            font-size: 20px;
        }

        .card .number {
            font-size: 36px;
            font-weight: bold;
            margin: 15px 0;
            color: #007bff;
        }

        .card .icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 40px;
            opacity: 0.1;
        }

        .recent-activity {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }

        .recent-activity h3 {
            margin-top: 0;
            color: #333;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #007bff;
        }

        .activity-details {
            flex: 1;
        }

        .activity-title {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .activity-time {
            font-size: 12px;
            color: #6c757d;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header-content h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }

        .user-name {
            font-weight: 500;
        }

        .user-role {
            font-size: 12px;
            color: #6c757d;
        }

        .card.batch {
            border-left: 4px solid #007bff;
        }

        .card.workshop {
            border-left: 4px solid #28a745;
        }

        .card.student {
            border-left: 4px solid #ffc107;
        }

        .card.certification {
            border-left: 4px solid #17a2b8;
        }

        .card.internship {
            border-left: 4px solid #6f42c1;
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
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
                <li><a href="dashboard.php" class="sidebar-btn active">Dashboard</a></li>
                <li><a href="batches.php">Batches</a></li>
                <li><a href="workshops.php">Workshops</a></li>
                <li><a href="internships.php">Internships</a></li>    
                <li><a href="students.php">Students</a></li>
                <li><a href="certification.php">Certification</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <main class="content">
            <header>
                <div class="header-content">
                    <h1>Dashboard</h1>
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                            <div class="user-role">Administrator</div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="dashboard-cards">
                <div class="card batch">
                    <h3><i class="fa fa-graduation-cap"></i> Active Batches</h3>
                    <p class="number"><?php echo $batchCount; ?></p>
                    <i class="fa fa-graduation-cap icon"></i>
                </div>
                
                <div class="card workshop">
                    <h3><i class="fa fa-calendar"></i> Upcoming Workshops</h3>
                    <p class="number"><?php echo $workshopCount; ?></p>
                    <i class="fa fa-calendar icon"></i>
                </div>
                
                <div class="card student">
                    <h3><i class="fa fa-users"></i> Total Students</h3>
                    <p class="number"><?php echo $studentCount; ?></p>
                    <i class="fa fa-users icon"></i>
                </div>
                
                <div class="card certification">
                    <h3><i class="fa fa-certificate"></i> Certified Students</h3>
                    <p class="number"><?php echo $certificationCount; ?></p>
                    <i class="fa fa-certificate icon"></i>
                </div>
                
                <div class="card internship">
                    <h3><i class="fa fa-briefcase"></i> Active Internships</h3>
                    <p class="number"><?php echo $internshipCount; ?></p>
                    <i class="fa fa-briefcase icon"></i>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Recent Activity</h3>
                <ul class="activity-list">
                    <?php foreach ($recentActivities as $activity): ?>
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fa fa-<?php 
                                echo match($activity['activity']) {
                                    'New Batch Enrollment' => 'graduation-cap',
                                    'New Workshop Enrollment' => 'calendar',
                                    'New Internship Enrollment' => 'briefcase',
                                    default => 'user'
                                };
                            ?>"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">
                                <?php echo htmlspecialchars($activity['student_name']); ?> enrolled in 
                                <?php echo htmlspecialchars($activity['program_name']); ?>
                            </div>
                            <div class="activity-time">
                                <?php echo date('M j, Y g:i A', strtotime($activity['activity_date'])); ?>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </main>
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
</body>
</html> 