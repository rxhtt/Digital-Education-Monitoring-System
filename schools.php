<?php
require_once "includes/auth.php";
require_once "config/db.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT * FROM schools WHERE 1=1";
$params = [];

if(!empty($search)){
    $query .= " AND (school_name LIKE :search OR school_code LIKE :search OR district LIKE :search)";
    $params[':search'] = "%$search%";
}

$query .= " ORDER BY school_name ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$schools = $stmt->fetchAll();

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold text-navy mb-0">School Management</h3>
        <p class="text-secondary small mb-0">Monitor and manage registered government schools</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <button class="btn btn-navy px-4" data-bs-toggle="modal" data-bs-target="#addSchoolModal">
            <i class="fas fa-plus me-2"></i> Add New School
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" class="row g-3">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, code or district..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-navy w-100">Search Schools</button>
            </div>
        </form>
    </div>
</div>

<!-- Schools Grid -->
<div class="row g-4 animate-fade-in">
    <?php if(empty($schools)): ?>
    <div class="col-12 text-center py-5">
        <i class="fas fa-school fa-4x text-light mb-3"></i>
        <h5 class="text-muted">No schools found matching your search.</h5>
    </div>
    <?php endif; ?>
    
    <?php foreach($schools as $school): ?>
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-3">
                    <span class="badge bg-navy-light"><?php echo $school['school_code']; ?></span>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3"><?php echo ucfirst($school['status']); ?></span>
                </div>
                <h5 class="fw-bold text-navy mb-1"><?php echo htmlspecialchars($school['school_name']); ?></h5>
                <p class="text-muted small mb-4"><i class="fas fa-map-marker-alt me-1"></i> <?php echo $school['district']; ?>, <?php echo $school['block_or_taluk']; ?></p>
                
                <div class="bg-light p-3 rounded-3 mb-4">
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted d-block uppercase fw-bold x-small">Headmaster</small>
                            <span class="small fw-semibold"><?php echo htmlspecialchars($school['head_name']); ?></span>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block uppercase fw-bold x-small">Contact</small>
                            <span class="small fw-semibold"><?php echo htmlspecialchars($school['contact_number']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <a href="students.php?school=<?php echo $school['id']; ?>" class="btn btn-sm btn-link text-navy text-decoration-none fw-bold px-0">
                        View Students <i class="fas fa-chevron-right ms-1 x-small"></i>
                    </a>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary border-0"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Simple Add School Modal -->
<div class="modal fade" id="addSchoolModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-navy text-white p-4">
                <h5 class="modal-title fw-bold">Register New School</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addSchoolForm">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">School Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">School Code</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">District</label>
                            <input type="text" name="district" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Taluk / Block</label>
                            <input type="text" name="block" class="form-control" required>
                        </div>
                        <div class="col-12 text-end mt-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-navy px-4">Register School</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
