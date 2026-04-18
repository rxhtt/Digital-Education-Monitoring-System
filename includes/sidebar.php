<!-- Sidebar -->
<nav id="sidebar" class="bg-navy text-white min-vh-100 shadow">
    <div class="sidebar-header p-4 text-center">
        <div class="logo-icon mb-2">
            <i class="fas fa-university fa-2x"></i>
        </div>
        <h5 class="m-0 fw-bold">DEMS Portal</h5>
        <small class="text-white-50">Govt. School Monitoring</small>
    </div>

    <ul class="list-unstyled components px-3 mt-4">
        <li class="nav-item mb-2">
            <a href="dashboard.php" class="nav-link p-3 rounded d-flex align-items-center <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt me-3"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="schools.php" class="nav-link p-3 rounded d-flex align-items-center <?php echo basename($_SERVER['PHP_SELF']) == 'schools.php' ? 'active' : ''; ?>">
                <i class="fas fa-school me-3"></i> Schools
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="students.php" class="nav-link p-3 rounded d-flex align-items-center <?php echo in_array(basename($_SERVER['PHP_SELF']), ['students.php', 'student_details.php']) ? 'active' : ''; ?>">
                <i class="fas fa-user-graduate me-3"></i> Students
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="teachers.php" class="nav-link p-3 rounded d-flex align-items-center <?php echo basename($_SERVER['PHP_SELF']) == 'teachers.php' ? 'active' : ''; ?>">
                <i class="fas fa-chalkboard-teacher me-3"></i> Teachers
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="attendance.php" class="nav-link p-3 rounded d-flex align-items-center <?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check me-3"></i> Attendance
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="marks.php" class="nav-link p-3 rounded d-flex align-items-center <?php echo basename($_SERVER['PHP_SELF']) == 'marks.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-invoice me-3"></i> Academic Marks
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="reports.php" class="nav-link p-3 rounded d-flex align-items-center <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line me-3"></i> Reports
            </a>
        </li>
        <hr class="bg-white-50">
        <li class="nav-item mb-2">
            <a href="logout.php" class="nav-link p-3 rounded d-flex align-items-center text-danger">
                <i class="fas fa-sign-out-alt me-3"></i> Logout
            </a>
        </li>
    </ul>

    <div class="sidebar-footer p-4 mt-auto text-center border-top border-secondary border-opacity-25">
        <div class="developer-info small">
            <p class="mb-0 text-white-50">Implementation by</p>
            <p class="mb-0 fw-semibold text-white">Anisha K</p>
            <p class="mb-0 x-small text-white-50">BCA 6th Sem, GFGC</p>
        </div>
    </div>
</nav>

<div id="content" class="flex-grow-1">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm px-4 mb-4">
        <div class="container-fluid">
            <button type="button" id="sidebarCollapse" class="btn btn-outline-navy me-3">
                <i class="fas fa-bars"></i>
            </button>
            <span class="navbar-brand mb-0 h1 text-navy d-none d-md-block">Education Management Dashboard</span>
            
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3 text-secondary d-none d-lg-inline small">
                    <i class="far fa-calendar-alt me-1"></i> <?php echo date('l, d M Y'); ?>
                </span>
                <div class="dropdown">
                    <button class="btn btn-navy dropdown-toggle btn-sm px-3" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION["full_name"]; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item py-2" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <div class="container-fluid px-4 px-lg-5 pb-5">
