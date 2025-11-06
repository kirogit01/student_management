<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher'){
    header("Location: login.php");
    exit;
}
echo "<h2>👩‍🏫 Welcome Teacher " . $_SESSION['name'] . "</h2>";
?>
<a href='addmark.php'>Add Marks</a> | <a href='logout.php'>Logout</a>
