<?php
require_once "includes/auth.php";
require_once "config/db.php";

if(!isset($_GET['id'])){
    header("location: students.php");
    exit;
}

$id = $_GET['id'];
$message = "";
$message_type = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $full_name = trim($_POST['full_name']);
    $class_name = $_POST['class_name'];
    $status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE students SET full_name = ?, class_name = ?, status = ? WHERE id = ?");
        $stmt->execute([$full_name, $class_name, $status, $id]);
        $message = "Student record updated!";
        $message_type = "success";
    } catch(Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "danger";
    }
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold text-navy mb-0">Edit Student Record</h3>
        <p class="text-secondary small mb-0">Update information for <?php echo htmlspecialchars($student['full_name']); ?></p>
    </div>
</div>

<div class="card border-0 shadow-sm col-md-8">
    <div class="card-body p-5">
        <?php if($message) echo "<div class='alert alert-$message_type'>$message</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Class</label>
                <select name="class_name" class="form-select">
                    <?php 
                    $classes = ['1st Std', '2nd Std', '3rd Std', '4th Std', '5th Std', '6th Std', '7th Std', '8th Std', '9th Std', '10th Std'];
                    foreach($classes as $c): ?>
                    <option value="<?php echo $c; ?>" <?php echo $student['class_name'] == $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="active" <?php echo $student['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $student['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="transferred" <?php echo $student['status'] == 'transferred' ? 'selected' : ''; ?>>Transferred</option>
                </select>
            </div>
            <div class="text-end">
                <a href="student_details.php?id=<?php echo $id; ?>" class="btn btn-light me-2">Cancel</a>
                <button type="submit" class="btn btn-navy px-4">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>
