<?php
include("db.php");

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Delete student
    mysqli_query($conn, "DELETE FROM students WHERE id='$id'");
}

header("Location: dashboard.php");
exit();
?>
