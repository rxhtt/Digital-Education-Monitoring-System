<?php
require_once "includes/auth.php";
require_once "config/db.php";

$query = "SELECT t.*, sch.school_name FROM teachers t LEFT JOIN schools sch ON t.school_id = sch.id ORDER BY t.full_name ASC";
$teachers = $pdo->query($query)->fetchAll();

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold text-navy mb-0">Teacher Directory</h3>
        <p class="text-secondary small mb-0">Manage teaching staff and school assignments</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <button class="btn btn-navy px-4">
            <i class="fas fa-plus me-2"></i> Add New Teacher
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Teacher ID</th>
                        <th>Full Name</th>
                        <th>Subject / Specialty</th>
                        <th>Assigned School</th>
                        <th>Contact</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($teachers)): ?>
                    <tr><td colspan="6" class="text-center py-5 text-muted">No teacher records found.</td></tr>
                    <?php endif; ?>
                    
                    <?php foreach($teachers as $teacher): ?>
                    <tr>
                        <td class="ps-4"><span class="badge bg-navy-light text-white"><?php echo $teacher['teacher_id']; ?></span></td>
                        <td>
                            <div class="fw-bold text-navy"><?php echo htmlspecialchars($teacher['full_name']); ?></div>
                            <small class="text-muted"><?php echo $teacher['designation']; ?></small>
                        </td>
                        <td><?php echo $teacher['subject_name']; ?></td>
                        <td><small class="fw-semibold"><?php echo htmlspecialchars($teacher['school_name'] ?? 'Not Assigned'); ?></small></td>
                        <td>
                            <div class="small"><i class="fas fa-phone-alt me-1 x-small"></i> <?php echo $teacher['phone']; ?></div>
                            <div class="small"><i class="far fa-envelope me-1 x-small"></i> <?php echo $teacher['email']; ?></div>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-navy"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
