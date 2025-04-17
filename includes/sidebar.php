<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'students.php' ? 'active' : ''; ?>" href="students.php">
                    <i class="fas fa-users"></i>
                    Students
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'batches.php' ? 'active' : ''; ?>" href="batches.php">
                    <i class="fas fa-layer-group"></i>
                    Batches
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'workshops.php' ? 'active' : ''; ?>" href="workshops.php">
                    <i class="fas fa-chalkboard-teacher"></i>
                    Workshops
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'internships.php' ? 'active' : ''; ?>" href="internships.php">
                    <i class="fas fa-briefcase"></i>
                    Internships
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'certifications.php' ? 'active' : ''; ?>" href="certifications.php">
                    <i class="fas fa-certificate"></i>
                    Certifications
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'user_pass.php' ? 'active' : ''; ?>" href="user_pass.php">
                    <i class="fas fa-user-cog"></i>
                    User Settings
                </a>
            </li>
        </ul>
    </div>
</nav> 