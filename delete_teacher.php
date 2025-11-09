<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: index.php");
    exit();
}


$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM teachers WHERE id='$id'");
header("Location: teachers.php");
exit;
?>
