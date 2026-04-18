<?php
require_once "includes/auth.php";
require_once "config/db.php";

$message = "";
$message_type = "";

$selected_school = isset($_GET['school_id']) ? $_GET['school_id'] : '';
$selected_class = isset($_GET['class_name']) ? $_GET['class_name'] : '';
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Handle Saving Attendance
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_attendance'])){
    $att_date = $_POST['att_date'];
    $school_id = $_POST['sch_id'];
    $class_nm = $_POST['cls_nm'];
    $statuses = $_POST['status']; // Array with student_id as key
    
    try {
        $pdo->beginTransaction();
        
        foreach($statuses as $student_id => $status){
            // Check if already exists for this date
            $check = $pdo->prepare("SELECT id FROM attendance WHERE student_id = ? AND attendance_date = ?");
            $check->execute([$student_id, $att_date]);
            
            if($check->rowCount() > 0){
                // Update
                $update = $pdo->prepare("UPDATE attendance SET status = ?, timestamp_marked = CURRENT_TIMESTAMP, marked_by_admin_id = ? WHERE student_id = ? AND attendance_date = ?");
                $update->execute([$status, $_SESSION['id'], $student_id, $att_date]);
            } else {
                // Insert
                $insert = $pdo->prepare("INSERT INTO attendance (student_id, school_id, class_name, attendance_date, status, marked_by_admin_id) VALUES (?, ?, ?, ?, ?, ?)");
                $insert->execute([$student_id, $school_id, $class_nm, $att_date, $status, $_SESSION['id']]);
            }
        }
        
        $pdo->commit();
        $message = "Attendance marked successfully!";
        $message_type = "success";
    } catch(Exception $e) {
        $pdo->rollBack();
        $message = "Error: " . $e->getMessage();
        $message_type = "danger";
    }
}

$schools = $pdo->query("SELECT id, school_name FROM schools ORDER BY school_name ASC")->fetchAll();
$students = [];

if($selected_school && $selected_class){
    $stmt = $pdo->prepare("SELECT s.id, s.student_id, s.full_name, a.status as current_status 
                          FROM students s 
                          LEFT JOIN attendance a ON s.id = a.student_id AND a.attendance_date = ? 
                          WHERE s.school_id = ? AND s.class_name = ? AND s.status = 'active'
                          ORDER BY s.full_name ASC");
    $stmt->execute([$selected_date, $selected_school, $selected_class]);
    $students = $stmt->fetchAll();
}

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold text-navy mb-0">Daily Attendance</h3>
        <p class="text-secondary small mb-0">Record and monitor student presence</p>
    </div>
</div>

<?php if($message): ?>
<div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Select School</label>
                <select name="school_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">Choose School...</option>
                    <?php foreach($schools as $school): ?>
                    <option value="<?php echo $school['id']; ?>" <?php echo $selected_school == $school['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($school['school_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Class / Grade</label>
                <select name="class_name" class="form-select" required onchange="this.form.submit()">
                    <option value="">Choose Class...</option>
                    <?php 
                    $classes = ['1st Std', '2nd Std', '3rd Std', '4th Std', '5th Std', '6th Std', '7th Std', '8th Std', '9th Std', '10th Std'];
                    foreach($classes as $class): ?>
                    <option value="<?php echo $class; ?>" <?php echo $selected_class == $class ? 'selected' : ''; ?>><?php echo $class; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Date</label>
                <input type="date" name="date" class="form-control" value="<?php echo $selected_date; ?>" onchange="this.form.submit()">
            </div>
        </form>
    </div>
</div>

<?php if($selected_school && $selected_class): ?>
<div class="card border-0 shadow-sm">
    <form method="POST">
        <input type="hidden" name="sch_id" value="<?php echo $selected_school; ?>">
        <input type="hidden" name="cls_nm" value="<?php echo $selected_class; ?>">
        <input type="hidden" name="att_date" value="<?php echo $selected_date; ?>">
        
        <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-navy m-0">Student List - <?php echo $selected_class; ?></h5>
            <div class="attendance-actions">
                <button type="button" class="btn btn-sm btn-outline-success me-2" onclick="markAll('Present')">Mark All Present</button>
                <button type="submit" name="mark_attendance" class="btn btn-navy px-4">Save Attendance</button>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" width="150">ID</th>
                            <th>Student Name</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent</th>
                            <th class="text-center pe-4">Late</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($students)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No students found in this class.</td></tr>
                        <?php endif; ?>
                        
                        <?php foreach($students as $student): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-navy"><?php echo $student['student_id']; ?></td>
                            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                            <td class="text-center">
                                <?php if($student['current_status']): ?>
                                    <span class="badge rounded-pill <?php echo $student['current_status'] == 'Present' ? 'badge-present' : ($student['current_status'] == 'Absent' ? 'badge-absent' : 'badge-late'); ?>">
                                        <?php echo $student['current_status']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted rounded-pill">Not Marked</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="form-check d-inline-block">
                                    <input class="form-check-input att-radio" type="radio" name="status[<?php echo $student['id']; ?>]" value="Present" <?php echo $student['current_status'] == 'Present' ? 'checked' : ''; ?> required>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check d-inline-block">
                                    <input class="form-check-input att-radio" type="radio" name="status[<?php echo $student['id']; ?>]" value="Absent" <?php echo $student['current_status'] == 'Absent' ? 'checked' : ''; ?>>
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <div class="form-check d-inline-block">
                                    <input class="form-check-input att-radio" type="radio" name="status[<?php echo $student['id']; ?>]" value="Late" <?php echo $student['current_status'] == 'Late' ? 'checked' : ''; ?>>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white p-4 border-0 text-end">
            <button type="submit" name="mark_attendance" class="btn btn-navy px-5 py-2">Submit Attendance Record</button>
        </div>
    </form>
</div>
<?php else: ?>
<div class="text-center py-5 bg-white shadow-sm rounded-4 mt-4">
    <i class="fas fa-filter fa-4x text-light mb-3"></i>
    <h5 class="text-secondary">Please select a school and class to record attendance</h5>
</div>
<?php endif; ?>

<script>
function markAll(status) {
    const radios = document.querySelectorAll('.att-radio[value="' + status + '"]');
    radios.forEach(radio => radio.checked = true);
}
</script>

<?php include "includes/footer.php"; ?>
