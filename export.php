<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: index.php");
    exit();
}


include("db.php");

// Set headers to force download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=students_list.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Fetch all students
$query = "SELECT * FROM students ORDER BY id ASC";
$result = mysqli_query($conn, $query);

// Column headers
echo "ID\tName\tEmail\tPhone\tCourse\n";

// Output data rows
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['id'] . "\t" . $row['name'] . "\t" . $row['email'] . "\t" . $row['phone'] . "\t" . $row['course'] . "\n";
}
exit;
?>
