<?php
require_once "includes/auth.php";
require_once "config/db.php";

// Fetch Stats
$total_schools = $pdo->query("SELECT COUNT(*) FROM schools")->fetchColumn();
$total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_teachers = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();

// Attendance Today
$today = date('Y-m-d');
$today_attendance_present = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE attendance_date = ? AND status = 'Present'");
$today_attendance_present->execute([$today]);
$present_count = $today_attendance_present->fetchColumn();

$today_attendance_absent = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE attendance_date = ? AND status = 'Absent'");
$today_attendance_absent->execute([$today]);
$absent_count = $today_attendance_absent->fetchColumn();

$total_today = $present_count + $absent_count;
$attendance_percentage = ($total_today > 0) ? round(($present_count / $total_today) * 100, 1) : 0;

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold text-navy">Monitoring Overview</h3>
        <p class="text-secondary">Summary of education metrics across all government schools.</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-5 animate-fade-in">
    <div class="col-md-3">
        <div class="card stat-card shadow-sm border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Schools</h6>
                        <h2 class="fw-bold mb-0 text-navy"><?php echo $total_schools; ?></h2>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-school fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card shadow-sm border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Students</h6>
                        <h2 class="fw-bold mb-0 text-navy"><?php echo $total_students; ?></h2>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-user-graduate fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card shadow-sm border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Teachers</h6>
                        <h2 class="fw-bold mb-0 text-navy"><?php echo $total_teachers; ?></h2>
                    </div>
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="fas fa-chalkboard-teacher fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card shadow-sm border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Today's Attendance</h6>
                        <h2 class="fw-bold mb-0 text-navy"><?php echo $attendance_percentage; ?>%</h2>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-calendar-check fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-navy mb-4">Monthly Attendance Trend</h5>
            <canvas id="attendanceChart" height="150"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h5 class="fw-bold text-navy mb-4">Student Distribution</h5>
            <canvas id="studentDistributionChart"></canvas>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-navy m-0">Recently Added Students</h5>
                <a href="students.php" class="btn btn-sm btn-outline-navy">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>School</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_students = $pdo->query("SELECT s.*, sch.school_name FROM students s LEFT JOIN schools sch ON s.school_id = sch.id ORDER BY s.created_at DESC LIMIT 5")->fetchAll();
                        if(empty($recent_students)) {
                            echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No students found.</td></tr>";
                        }
                        foreach($recent_students as $student): ?>
                        <tr>
                            <td><span class="fw-medium"><?php echo $student['student_id']; ?>span></td>
                            <td><?php echo $student['full_name']; ?></td>
                            <td><?php echo $student['class_name']; ?></td>
                            <td><small class="text-muted"><?php echo $student['school_name']; ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-navy mb-4">Quick Actions</h5>
            <div class="row g-3">
                <div class="col-6">
                    <a href="add_student.php" class="btn btn-light border p-4 w-100 text-center rounded-3">
                        <i class="fas fa-user-plus fa-2x mb-3 text-primary"></i>
                        <h6 class="m-0 fw-bold">Add Student</h6>
                    </a>
                </div>
                <div class="col-6">
                    <a href="attendance.php" class="btn btn-light border p-4 w-100 text-center rounded-3">
                        <i class="fas fa-calendar-check fa-2x mb-3 text-success"></i>
                        <h6 class="m-0 fw-bold">Mark Attendance</h6>
                    </a>
                </div>
                <div class="col-6">
                    <a href="add_teacher.php" class="btn btn-light border p-4 w-100 text-center rounded-3">
                        <i class="fas fa-chalkboard-teacher fa-2x mb-3 text-info"></i>
                        <h6 class="m-0 fw-bold">Add Teacher</h6>
                    </a>
                </div>
                <div class="col-6">
                    <a href="reports.php" class="btn btn-light border p-4 w-100 text-center rounded-3">
                        <i class="fas fa-file-alt fa-2x mb-3 text-warning"></i>
                        <h6 class="m-0 fw-bold">Generate Report</h6>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Attendance Trend Chart (Mock Data for presentation)
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
            datasets: [{
                label: 'Attendance %',
                data: [85, 88, 82, 90, 85, 87, 84, 89, 92, 91],
                borderColor: '#0a2351',
                backgroundColor: 'rgba(10, 35, 81, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: false, min: 70, max: 100 } }
        }
    });

    // Student Distribution Chart
    const ctx2 = document.getElementById('studentDistributionChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Rural Schools', 'Urban Schools', 'Semi-Urban'],
            datasets: [{
                data: [45, 35, 20],
                backgroundColor: ['#0a2351', '#2ecc71', '#f1c40f'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>

<?php include "includes/footer.php"; ?>
