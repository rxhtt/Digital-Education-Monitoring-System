<?php
require_once "includes/auth.php";
require_once "config/db.php";

if(!isset($_GET['id']) || empty($_GET['id'])){
    header("location: students.php");
    exit;
}

$id = $_GET['id'];

// Fetch Student details with school
$stmt = $pdo->prepare("SELECT s.*, sch.school_name, sch.district, sch.school_code FROM students s LEFT JOIN schools sch ON s.school_id = sch.id WHERE s.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if(!$student){
    header("location: students.php");
    exit;
}

// Fetch Attendance Stat
$att_stmt = $pdo->prepare("SELECT 
    COUNT(*) as total_days,
    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_days,
    SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent_days,
    SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late_days
    FROM attendance WHERE student_id = ?");
$att_stmt->execute([$id]);
$att_stats = $att_stmt->fetch();

$total_working = $att_stats['total_days'] > 0 ? $att_stats['total_days'] : 0;
$present_count = $att_stats['present_days'] > 0 ? $att_stats['present_days'] : 0;
$att_percentage = $total_working > 0 ? round(($present_count / $total_working) * 100, 1) : 0;

// Fetch Marks
$marks_stmt = $pdo->prepare("SELECT * FROM marks WHERE student_id = ? ORDER BY entered_on DESC");
$marks_stmt->execute([$id]);
$marks = $marks_stmt->fetchAll();

// Fetch Attendance History
$hist_stmt = $pdo->prepare("SELECT * FROM attendance WHERE student_id = ? ORDER BY attendance_date DESC LIMIT 20");
$hist_stmt->execute([$id]);
$history = $hist_stmt->fetchAll();

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold text-navy mb-0">Student Dossier</h3>
        <p class="text-secondary small mb-0">Complete academic & administrative monitoring profile</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <button onclick="window.print()" class="btn btn-outline-navy me-2">
            <i class="fas fa-print me-2"></i> Print Dossier
        </button>
        <a href="edit_student.php?id=<?php echo $id; ?>" class="btn btn-navy px-4">
            <i class="fas fa-user-edit me-2"></i> Edit Record
        </a>
    </div>
</div>

<div class="row g-4 animate-fade-in">
    <!-- Profile Card -->
    <div class="col-lg-8">
        <div class="card dossier-header border-0 shadow-sm p-4 h-100">
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['full_name']); ?>&background=0a2351&color=fff&size=150" class="profile-img-lg shadow-sm" alt="Profile">
                </div>
                <div class="col-md-9 border-start-md ps-md-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-navy mb-2"><?php echo $student['student_id']; ?></span>
                            <h2 class="fw-bold text-navy mb-1"><?php echo htmlspecialchars($student['full_name']); ?></h2>
                            <p class="text-muted mb-3"><i class="fas fa-school me-2"></i> <?php echo htmlspecialchars($student['school_name']); ?> (<?php echo $student['school_code']; ?>)</p>
                        </div>
                        <span class="badge <?php echo $student['status'] == 'active' ? 'bg-success' : 'bg-danger'; ?> rounded-pill px-3">
                            <?php echo ucfirst($student['status']); ?>
                        </span>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold text-uppercase">Class</label>
                            <p class="mb-0 fw-semibold"><?php echo $student['class_name']; ?> - <?php echo $student['section']; ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold text-uppercase">Gender</label>
                            <p class="mb-0 fw-semibold"><?php echo $student['gender']; ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold text-uppercase">Date of Birth</label>
                            <p class="mb-0 fw-semibold"><?php echo date('d M Y', strtotime($student['dob'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 opacity-10">
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3">
                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Father's Name</label>
                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($student['father_name']); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3">
                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Mother's Name</label>
                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($student['mother_name']); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3">
                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Contact</label>
                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($student['guardian_contact']); ?></p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3 bg-light rounded-3">
                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Permanent Address</label>
                        <p class="mb-0 fw-medium"><?php echo htmlspecialchars($student['address']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Widget -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h5 class="fw-bold text-navy mb-4">Attendance Summary</h5>
            <div class="text-center mb-4">
                <div class="attendance-ring mx-auto mb-2" style="border-color: <?php echo $att_percentage >= 75 ? '#2ecc71' : ($att_percentage >= 60 ? '#f1c40f' : '#e74c3c'); ?>66;">
                    <?php echo $att_percentage; ?>%
                </div>
                <p class="text-muted small">Overall Attendance Status</p>
            </div>
            
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Total Working Days</span>
                    <span class="fw-bold"><?php echo $total_working; ?></span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Days Present</span>
                    <span class="fw-bold text-success"><?php echo $present_count; ?></span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Days Absent</span>
                    <span class="fw-bold text-danger"><?php echo $att_stats['absent_days']; ?></span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0">
                    <span class="text-muted">Late Arrivals</span>
                    <span class="fw-bold text-warning"><?php echo $att_stats['late_days']; ?></span>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $att_percentage; ?>%" aria-valuenow="<?php echo $att_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic History -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="fw-bold text-navy m-0">Academic Performance</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Subject</th>
                                <th>Exam Type</th>
                                <th>Marks</th>
                                <th>Status</th>
                                <th class="pe-4">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($marks)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">No marks records found.</td></tr>
                            <?php endif; ?>
                            <?php foreach($marks as $mark): 
                                $pct = ($mark['marks_obtained'] / $mark['max_marks']) * 100;
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo $mark['subject_name']; ?></td>
                                <td><?php echo $mark['exam_type']; ?></td>
                                <td>
                                    <span class="fw-bold"><?php echo $mark['marks_obtained']; ?></span> / <?php echo $mark['max_marks']; ?>
                                    <small class="text-muted d-block"><?php echo round($pct, 1); ?>%</small>
                                </td>
                                <td>
                                    <span class="badge <?php echo $pct >= 35 ? 'badge-present' : 'badge-absent'; ?>">
                                        <?php echo $pct >= 35 ? 'PASS' : 'FAIL'; ?>
                                    </span>
                                </td>
                                <td class="pe-4 small text-muted"><?php echo date('d M Y', strtotime($mark['entered_on'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance History -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="fw-bold text-navy m-0">Attendance Timeline</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Status</th>
                                <th class="pe-4">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($history)): ?>
                            <tr><td colspan="3" class="text-center py-4 text-muted">No attendance history.</td></tr>
                            <?php endif; ?>
                            <?php foreach($history as $row): ?>
                            <tr>
                                <td class="ps-4 small"><?php echo date('d M Y', strtotime($row['attendance_date'])); ?></td>
                                <td>
                                    <span class="badge rounded-pill <?php 
                                        echo $row['status'] == 'Present' ? 'badge-present' : ($row['status'] == 'Absent' ? 'badge-absent' : 'badge-late'); 
                                    ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="pe-4 x-small text-muted"><?php echo date('H:i A', strtotime($row['timestamp_marked'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
