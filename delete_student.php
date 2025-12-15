<?php
include("db.php");

if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Ensure it's an integer

    // Optional: delete related attendance first (if FK exists)
    mysqli_query($conn, "DELETE FROM attendance WHERE student_id=$id");

    // Delete student
    $result = mysqli_query($conn, "DELETE FROM students WHERE id=$id");

    if (!$result) {
        die("Error deleting student: " . mysqli_error($conn));
    }
}

header("Location: dashboard.php");
exit();
?>
