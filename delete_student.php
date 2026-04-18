<?php
require_once "includes/auth.php";
require_once "config/db.php";

if(isset($_GET['id'])){
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("location: students.php");
exit;
?>
