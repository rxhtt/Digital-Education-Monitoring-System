<?php
session_start();

// Check if user is already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: dashboard.php");
    exit;
}

require_once "config/db.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password_hash, full_name, role FROM admins WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = trim($_POST["username"]);
            
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password_hash"];
                        $full_name = $row["full_name"];
                        $role = $row["role"];
                        
                        if(password_verify($password, $hashed_password)){
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["full_name"] = $full_name;
                            $_SESSION["role"] = $role;
                            
                            header("location: dashboard.php");
                        } else {
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid username or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Digital Education Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-container d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card login-card">
                    <div class="row g-0">
                        <div class="col-md-6 d-none d-md-flex login-brand-panel">
                            <div class="mb-4">
                                <i class="fas fa-shield-alt fa-4x text-accent mb-3"></i>
                                <h2 class="fw-bold mb-2">DEMS Portal</h2>
                                <p class="lead opacity-75">Digital Education Monitoring System for Government Schools</p>
                            </div>
                            <ul class="list-unstyled small opacity-75">
                                <li class="mb-2"><i class="fas fa-check-circle me-2 text-accent"></i> Centralized School Monitoring</li>
                                <li class="mb-2"><i class="fas fa-check-circle me-2 text-accent"></i> Attendance & Academic Tracking</li>
                                <li class="mb-2"><i class="fas fa-check-circle me-2 text-accent"></i> Real-time Authority Reporting</li>
                            </ul>
                        </div>
                        <div class="col-md-6 bg-white p-5">
                            <div class="text-center mb-4 md-mt-0">
                                <h3 class="fw-bold text-navy">Admin Login</h3>
                                <p class="text-muted small">Sign in to access the monitoring dashboard</p>
                            </div>

                            <?php 
                            if(!empty($login_err)){
                                echo '<div class="alert alert-danger py-2 small">' . $login_err . '</div>';
                            }        
                            ?>

                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-navy">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                        <input type="text" name="username" class="form-control bg-light border-start-0 <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" placeholder="Enter username">
                                        <div class="invalid-feedback"><?php echo $username_err; ?></div>
                                    </div>
                                </div>    
                                <div class="mb-4">
                                    <label class="form-label small fw-semibold text-navy">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                        <input type="password" name="password" class="form-control bg-light border-start-0 <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="Enter password">
                                        <div class="invalid-feedback"><?php echo $password_err; ?></div>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-navy py-2 fw-bold">Login to Portal</button>
                                </div>
                            </form>

                            <div class="mt-5 pt-4 text-center border-top">
                                <p class="x-small text-muted mb-0">Project Implementation by</p>
                                <h6 class="fw-bold text-navy mb-0">Anisha K</h6>
                                <p class="x-small text-muted">BCA 6th Sem, GFGC</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
