<?php
require_once "includes/auth.php";
require_once "config/db.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$school_filter = isset($_GET['school']) ? $_GET['school'] : '';

// Build Query
$query = "SELECT s.*, sch.school_name FROM students s LEFT JOIN schools sch ON s.school_id = sch.id WHERE 1=1";
$params = [];

if(!empty($search)){
    $query .= " AND (s.full_name LIKE :search OR s.student_id LIKE :search)";
    $params[':search'] = "%$search%";
}

if(!empty($school_filter)){
    $query .= " AND s.school_id = :school";
    $params[':school'] = $school_filter;
}

$query .= " ORDER BY s.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();

$schools = $pdo->query("SELECT id, school_name FROM schools ORDER BY school_name ASC")->fetchAll();

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold text-navy mb-0">Student Management</h3>
        <p class="text-secondary mb-0">View and manage student records</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="add_student.php" class="btn btn-navy px-4">
            <i class="fas fa-plus me-2"></i> Add New Student
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name or student ID..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-4">
                <select name="school" class="form-select">
                    <option value="">All Schools</option>
                    <?php foreach($schools as $school): ?>
                    <option value="<?php echo $school['id']; ?>" <?php echo $school_filter == $school['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($school['school_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-navy w-100">Filter Records</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Student ID</th>
                        <th>Full Name</th>
                        <th>Class & Section</th>
                        <th>School</th>
                        <th>Father's Name</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($students)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-user-slash fa-3x text-light mb-3"></i>
                            <p class="text-muted">No student records found matching your criteria.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach($students as $student): ?>
                    <tr class="animate-fade-in">
                        <td class="ps-4 fw-bold text-navy small"><?php echo $student['student_id']; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-light rounded-circle me-3 p-2 text-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-navy-light"></i>
                                </div>
                                <span class="fw-semibold"><?php echo htmlspecialchars($student['full_name']); ?></span>
                            </div>
                        </td>
                        <td><?php echo $student['class_name'] . ' - ' . $student['section']; ?></td>
                        <td><small class="text-muted"><?php echo htmlspecialchars($student['school_name'] ?? 'N/A'); ?></small></td>
                        <td><?php echo htmlspecialchars($student['father_name']); ?></td>
                        <td>
                            <?php 
                            $badge_class = 'bg-success';
                            if($student['status'] == 'inactive') $badge_class = 'bg-danger';
                            if($student['status'] == 'transferred') $badge_class = 'bg-warning text-dark';
                            ?>
                            <span class="badge <?php echo $badge_class; ?> rounded-pill small"><?php echo ucfirst($student['status']); ?></span>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="btn-group">
                                <a href="student_details.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-navy" title="View Detailed Dossier">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="if(confirm('Are you sure you want to delete this student?')) window.location.href='delete_student.php?id=<?php echo $student['id']; ?>'">
                                    <i class="fas fa-trash"></i>
                                </button>
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
