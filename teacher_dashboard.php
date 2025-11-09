<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher'){
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Teacher Dashboard</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f3f3f3;
        margin: 0;
        padding: 0;
    }

    .navbar {
        background-color: #007bff;
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .navbar h1 {
        font-size: 22px;
        margin: 0;
    }

    .navbar a {
        color: white;
        text-decoration: none;
        background-color: #0056b3;
        padding: 8px 14px;
        border-radius: 5px;
        transition: 0.3s;
    }

    .navbar a:hover {
        background-color: #003d80;
    }

    .container {
        max-width: 900px;
        margin: 60px auto;
        background-color: white;
        border-radius: 10px;
        padding: 40px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        text-align: center;
    }

    h2 {
        color: #333;
        font-size: 26px;
        margin-bottom: 40px;
    }

    .menu {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .menu a {
        text-decoration: none;
        display: inline-block;
        padding: 15px 25px;
        font-size: 18px;
        background-color: #007bff;
        color: white;
        border-radius: 8px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.15);
        transition: 0.3s;
    }

    .menu a:hover {
        background-color: #0056b3;
        transform: translateY(-3px);
    }

    .logout {
        background-color: #dc3545 !important;
    }

    .logout:hover {
        background-color: #a71d2a !important;
    }
</style>
</head>
<body>

<div class="navbar">
    <h1>Teacher Dashboard</h1>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="container">
    <h2>👩‍🏫 Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Teacher'); ?>!</h2>


    <div class="menu">
        <a href="add_attendance.php">📌 Mark Attendance</a>
        <a href="addmark.php">📝 Add Marks</a>
        <a href="view_reports.php">📄 View Reports</a>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>
</div>

</body>
</html>
