<?php
require_once "includes/auth.php";
require_once "config/db.php";

$message = "";
$message_type = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])){
    $student_id = trim($_POST['student_id']);
    $full_name = trim($_POST['full_name']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $class_name = $_POST['class_name'];
    $section = trim($_POST['section']);
    $school_id = $_POST['school_id'];
    $father = trim($_POST['father_name']);
    $mother = trim($_POST['mother_name']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $admission_date = $_POST['admission_date'];
    
    try {
        $sql = "INSERT INTO students (student_id, full_name, gender, dob, class_name, section, school_id, father_name, mother_name, guardian_contact, address, admission_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$student_id, $full_name, $gender, $dob, $class_name, $section, $school_id, $father, $mother, $contact, $address, $admission_date]);
        
        $message = "Student added successfully!";
        $message_type = "success";
    } catch(PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "danger";
    }
}

$schools = $pdo->query("SELECT id, school_name FROM schools ORDER BY school_name ASC")->fetchAll();

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold text-navy mb-0">Add New Student</h3>
        <p class="text-secondary small mb-0">Register a new student in the monitoring system</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="students.php" class="btn btn-outline-navy">
            <i class="fas fa-arrow-left me-2"></i> Back to List
        </a>
    </div>
</div>

<?php if($message): ?>
<div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm animate-fade-in">
    <div class="card-body p-5">
        <form method="POST">
            <div class="row g-4">
                <div class="col-12">
                    <h5 class="fw-bold text-navy border-bottom pb-2 mb-3">Personal Information</h5>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Admission Number / ID</label>
                    <input type="text" name="student_id" class="form-control" required placeholder="e.g. STU1001">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold small">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required placeholder="Enter student's full name">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Gender</label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Admission Date</label>
                    <input type="date" name="admission_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="col-12 mt-5">
                    <h5 class="fw-bold text-navy border-bottom pb-2 mb-3">Academic Details</h5>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">School Assignment</label>
                    <select name="school_id" class="form-select" required>
                        <option value="">Choose School...</option>
                        <?php foreach($schools as $school): ?>
                        <option value="<?php echo $school['id']; ?>"><?php echo htmlspecialchars($school['school_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Class</label>
                    <select name="class_name" class="form-select" required>
                        <option value="">Select Class</option>
                        <?php 
                        $classes = ['1st Std', '2nd Std', '3rd Std', '4th Std', '5th Std', '6th Std', '7th Std', '8th Std', '9th Std', '10th Std'];
                        foreach($classes as $c) echo "<option value='$c'>$c</option>";
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Section</label>
                    <input type="text" name="section" class="form-control" placeholder="e.g. A">
                </div>

                <div class="col-12 mt-5">
                    <h5 class="fw-bold text-navy border-bottom pb-2 mb-3">Parent & Contact Information</h5>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Father's Full Name</label>
                    <input type="text" name="father_name" class="form-control" placeholder="Enter father's name">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Mother's Full Name</label>
                    <input type="text" name="mother_name" class="form-control" placeholder="Enter mother's name">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Contact Number</label>
                    <input type="text" name="contact" class="form-control" placeholder="10-digit mobile number">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold small">Permanent Address</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Full residential address"></textarea>
                </div>

                <div class="col-12 text-end mt-4">
                    <button type="reset" class="btn btn-light px-4 me-2">Clear Form</button>
                    <button type="submit" name="add_student" class="btn btn-navy px-5 py-2">Save Student Record</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>
