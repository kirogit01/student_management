<?php
include("db.php");

if(isset($_GET['grade']) && isset($_GET['type'])){
    $grade = mysqli_real_escape_string($conn, $_GET['grade']);
    $type = $_GET['type'];
    $date_or_month = mysqli_real_escape_string($conn, $_GET['date']);

    if($type == 'daily'){
        $check = mysqli_query($conn, "
            SELECT a.id FROM attendance a
            JOIN students s ON a.student_id = s.id
            WHERE s.grade='$grade' AND a.date='$date_or_month'
            LIMIT 1
        ");
    } else {
        $check = mysqli_query($conn, "
            SELECT a.id FROM attendance a
            JOIN students s ON a.student_id = s.id
            WHERE s.grade='$grade' AND a.date LIKE '$date_or_month%'
            LIMIT 1
        ");
    }

    echo (mysqli_num_rows($check) > 0) ? "exists" : "not_exists";
}
?>
