<?php
require_once "includes/auth.php";
require_once "config/db.php";

$message = "";
$message_type = "";

// Handle Marks Entry
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_marks'])){
    $student_id = $_POST['student_id'];
    $subject = $_POST['subject'];
    $exam = $_POST['exam'];
    $obtained = $_POST['obtained'];
    $max = $_POST['max'];
    $remarks = $_POST['remarks'];
    
    try {
        $sql = "INSERT INTO marks (student_id, subject_name, exam_type, marks_obtained, max_marks, remarks, entered_on, entered_by_admin_id) 
                VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$student_id, $subject, $exam, $obtained, $max, $remarks, $_SESSION['id']]);
        
        $message = "Academic record saved!";
        $message_type = "success";
    } catch(Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "danger";
    }
}

$students = $pdo->query("SELECT id, student_id, full_name FROM students ORDER BY full_name ASC")->fetchAll();
$subjects = ['Mathematics', 'Science', 'Social Science', 'Kannada', 'English', 'Physical Education'];

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold text-navy mb-0">Academic Performance</h3>
        <p class="text-secondary small mb-0">Record and monitor student examination results</p>
    </div>
</div>

<?php if($message): ?>
<div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h5 class="fw-bold text-navy mb-4">New Marks Entry</h5>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Select Student</label>
                    <select name="student_id" class="form-select" required>
                        <option value="">Choose Student...</option>
                        <?php foreach($students as $s): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo $s['student_id'] . ' - ' . $s['full_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Subject</label>
                    <select name="subject" class="form-select" required>
                        <option value="">Select Subject</option>
                        <?php foreach($subjects as $sub) echo "<option value='$sub'>$sub</option>"; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Exam Type</label>
                    <select name="exam" class="form-select" required>
                        <option value="Unit Test I">Unit Test I</option>
                        <option value="Mid-Term">Mid-Term</option>
                        <option value="Unit Test II">Unit Test II</option>
                        <option value="Final Exam">Final Exam</option>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Marks Obtained</label>
                        <input type="number" name="obtained" step="0.5" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Max Marks</label>
                        <input type="number" name="max" class="form-control" value="100" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Remarks</label>
                    <input type="text" name="remarks" class="form-control" placeholder="Optional">
                </div>
                <div class="d-grid">
                    <button type="submit" name="add_marks" class="btn btn-navy py-2 fw-bold">Save Performance Record</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white p-4 border-0">
                <h5 class="fw-bold text-navy m-0">Recent Academic Records</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 text-start">Student</th>
                                <th>Subject</th>
                                <th>Exam</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th class="pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recent_marks = $pdo->query("SELECT m.*, s.full_name FROM marks m JOIN students s ON m.student_id = s.id ORDER BY m.id DESC LIMIT 10")->fetchAll();
                            if(empty($recent_marks)):
                            ?>
                            <tr><td colspan="6" class="py-5 text-muted">No records found.</td></tr>
                            <?php endif; ?>
                            
                            <?php foreach($recent_marks as $row): 
                                $pct = ($row['marks_obtained'] / $row['max_marks']) * 100;
                            ?>
                            <tr>
                                <td class="ps-4 text-start fw-semibold"><?php echo $row['full_name']; ?></td>
                                <td class="small"><?php echo $row['subject_name']; ?></td>
                                <td class="small"><?php echo $row['exam_type']; ?></td>
                                <td class="fw-bold"><?php echo $row['marks_obtained']; ?> / <?php echo $row['max_marks']; ?></td>
                                <td><span class="small"><?php echo round($pct, 1); ?>%</span></td>
                                <td class="pe-4">
                                    <span class="badge <?php echo $pct >= 35 ? 'badge-present' : 'badge-absent'; ?> rounded-pill">
                                        <?php echo $pct >= 35 ? 'PASS' : 'FAIL'; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-center p-3 border-0">
                <a href="reports.php" class="btn btn-sm btn-link text-navy text-decoration-none fw-bold">View Detailed Performance Analytics</a>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
