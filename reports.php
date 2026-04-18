<?php
require_once "includes/auth.php";
require_once "config/db.php";

// 1. Fetch school-wise student counts
$school_stats = $pdo->query("SELECT sch.school_name, COUNT(s.id) as student_count 
                           FROM schools sch 
                           LEFT JOIN students s ON sch.id = s.school_id 
                           GROUP BY sch.id")->fetchAll();

// 2. Fetch district-wise stats
$district_stats = $pdo->query("SELECT district, COUNT(*) as count FROM schools GROUP BY district")->fetchAll();

// 3. Gender Distribution
$gender_stats = $pdo->query("SELECT gender, COUNT(*) as count FROM students GROUP BY gender")->fetchAll();

// 4. Academic Summary (Average Marks by subject)
$subject_avg = $pdo->query("SELECT subject_name, AVG((marks_obtained / max_marks) * 100) as avg_pct 
                          FROM marks GROUP BY subject_name")->fetchAll();

// 5. High Level Stats
$total_schools = $pdo->query("SELECT COUNT(*) FROM schools")->fetchColumn();
$total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$avg_attendance = $pdo->query("SELECT (COUNT(CASE WHEN status='Present' THEN 1 END) / COUNT(*)) * 100 FROM attendance")->fetchColumn() ?: 0;
$pass_rate = $pdo->query("SELECT (COUNT(CASE WHEN (marks_obtained/max_marks)*100 >= 35 THEN 1 END) / COUNT(*)) * 100 FROM marks")->fetchColumn() ?: 0;

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold text-navy mb-0">System-Wide Analytics</h3>
        <p class="text-secondary small mb-0">Formal administrative monitoring and performance reports</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <button onclick="window.print()" class="btn btn-navy px-4">
            <i class="fas fa-print me-2"></i> Print Official Report
        </button>
    </div>
</div>

<!-- Stat Row -->
<div class="row g-4 mb-5 animate-fade-in">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 bg-navy text-white text-center rounded-4">
            <h6 class="text-white-50 small fw-bold text-uppercase mb-2">Overall Attendance</h6>
            <h2 class="fw-bold mb-0"><?php echo round($avg_attendance, 1); ?>%</h2>
            <div class="mt-2 text-white-50 x-small">System Average</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 bg-success text-white text-center rounded-4">
            <h6 class="text-white-50 small fw-bold text-uppercase mb-2">Passing Rate</h6>
            <h2 class="fw-bold mb-0"><?php echo round($pass_rate, 1); ?>%</h2>
            <div class="mt-2 text-white-50 x-small">Across All Subjects</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 bg-white text-navy text-center rounded-4">
            <h6 class="text-muted small fw-bold text-uppercase mb-2">Student Enrolled</h6>
            <h2 class="fw-bold mb-0"><?php echo $total_students; ?></h2>
            <div class="mt-2 text-muted x-small">Active Records</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-4 bg-white text-navy text-center rounded-4">
            <h6 class="text-muted small fw-bold text-uppercase mb-2">Active Institutions</h6>
            <h2 class="fw-bold mb-0"><?php echo $total_schools; ?></h2>
            <div class="mt-2 text-muted x-small">Govt. Schools</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Chart 1: School Strength -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h5 class="fw-bold text-navy mb-4"><i class="fas fa-chart-bar me-2 text-primary"></i>Student Strength by School</h5>
            <canvas id="schoolBarChart" height="200"></canvas>
        </div>
    </div>
    <!-- Chart 2: Gender Ratio -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h5 class="fw-bold text-navy mb-4"><i class="fas fa-venus-mars me-2 text-danger"></i>Gender Distribution</h5>
            <div class="chart-container py-3">
                <canvas id="genderPieChart"></canvas>
            </div>
            <div class="mt-4 text-center">
                <div class="row g-0">
                    <?php foreach($gender_stats as $gs): ?>
                    <div class="col-6 border-end last-child-no-border">
                        <h4 class="fw-bold text-navy m-0"><?php echo $gs['count']; ?></h4>
                        <small class="text-muted"><?php echo $gs['gender']; ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Chart 3: Subject Performance -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-navy mb-4"><i class="fas fa-graduation-cap me-2 text-success"></i>Performance by Subject</h5>
            <canvas id="performanceChart" height="200"></canvas>
        </div>
    </div>
    <!-- Chart 4: District Distribution -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-navy mb-4"><i class="fas fa-map-marked-alt me-2 text-warning"></i>School Distribution by District</h5>
            <canvas id="districtPieChart" height="200"></canvas>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-header bg-white p-4 border-0">
        <h5 class="fw-bold text-navy m-0">Detailed Regional Report</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">School Code</th>
                        <th>Institution Name</th>
                        <th>District / Block</th>
                        <th class="text-center">Student Population</th>
                        <th class="text-center">Staff Count</th>
                        <th class="text-center">Avg marks %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $details = $pdo->query("SELECT sch.*, 
                                          (SELECT COUNT(*) FROM students WHERE school_id = sch.id) as stu_count,
                                          (SELECT COUNT(*) FROM teachers WHERE school_id = sch.id) as tea_count,
                                          (SELECT AVG((marks_obtained/max_marks)*100) FROM marks m JOIN students s ON m.student_id = s.id WHERE s.school_id = sch.id) as avg_marks
                                          FROM schools sch")->fetchAll();
                    foreach($details as $row): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-navy small"><?php echo $row['school_code']; ?></td>
                        <td class="fw-semibold"><?php echo $row['school_name']; ?></td>
                        <td>
                            <div class="fw-bold small"><?php echo $row['district']; ?></div>
                            <div class="text-muted x-small"><?php echo $row['block_or_taluk']; ?></div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3">
                                <i class="fas fa-users me-1"></i> <?php echo $row['stu_count']; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3">
                                <i class="fas fa-chalkboard-teacher me-1"></i> <?php echo $row['tea_count']; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="fw-bold <?php echo $row['avg_marks'] >= 60 ? 'text-success' : ($row['avg_marks'] >= 35 ? 'text-warning' : 'text-danger'); ?>">
                                <?php echo $row['avg_marks'] ? round($row['avg_marks'], 1) . '%' : 'N/A'; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // 1. School Bar Chart
    const barCtx = document.getElementById('schoolBarChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($school_stats, 'school_name')); ?>,
            datasets: [{
                label: 'Student Population',
                data: <?php echo json_encode(array_column($school_stats, 'student_count')); ?>,
                backgroundColor: 'rgba(10, 35, 81, 0.8)',
                hoverBackgroundColor: '#0a2351',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true }, x: { grid: { display : false } } }
        }
    });

    // 2. Gender Pie Chart
    const genderCtx = document.getElementById('genderPieChart').getContext('2d');
    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($gender_stats, 'gender')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($gender_stats, 'count')); ?>,
                backgroundColor: ['#0a2351', '#2ecc71', '#e74c3c'],
                borderWidth: 5,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            cutout: '75%',
            plugins: { legend: { display: false } }
        }
    });

    // 3. Performance Chart
    const perfCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(perfCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($subject_avg, 'subject_name')); ?>,
            datasets: [{
                label: 'Avg Pass %',
                data: <?php echo json_encode(array_column($subject_avg, 'avg_pct')); ?>,
                borderColor: '#2ecc71',
                backgroundColor: 'rgba(46, 204, 113, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointBackgroundColor: '#2ecc71'
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });

    // 4. District Pie Chart
    const pieCtx = document.getElementById('districtPieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_column($district_stats, 'district')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($district_stats, 'count')); ?>,
                backgroundColor: ['#34495e', '#2ecc71', '#f1c40f', '#e67e22', '#e74c3c'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'right' } }
        }
    });
</script>

<?php include "includes/footer.php"; ?>
