
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
        font-family: 'Poppins', sans-serif;
        background: #f4f5f7;
        margin: 0;
        padding: 0;
        color: #333;
    }

    /* NAVBAR */
    .navbar {
        background: #2c3e50;
        color: white;
        padding: 18px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .navbar h1 {
        font-size: 24px;
        margin: 0;
        font-weight: 600;
    }

    .navbar a {
        color: white;
        text-decoration: none;
        padding: 10px 18px;
        border-radius: 6px;
        background: #2980b9;
        font-weight: 500;
    }

    /* MAIN CONTAINER */
    .container {
        max-width: 900px;
        margin: 60px auto;
        background: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0px 6px 15px rgba(0,0,0,0.05);
        text-align: center;
    }

    h2 {
        color: #2c3e50;
        font-size: 26px;
        font-weight: 600;
        margin-bottom: 35px;
    }

    .menu {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 20px;
    }

    .menu a {
        text-decoration: none;
        width: 220px;
        padding: 18px;
        font-size: 18px;
        background: #ecf0f1;
        color: #2c3e50;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0px 3px 8px rgba(0,0,0,0.05);
        border-left: 5px solid #2980b9;
    }

    .menu a.logout {
        background: #e74c3c;
        color: white;
        border-left: 5px solid #c0392b;
    }

    /* RESPONSIVE */
    @media(max-width: 600px){
        .menu a {
            width: 100%;
        }
    }
</style>

</head>
<body>

<div class="navbar">
    <h1>Teacher Dashboard</h1>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="container">
    <h2>👩‍🏫 Welcome, 
        <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : "Teacher"; ?>!
    </h2>

    <div class="menu">
        <a href="add_attendance.php">📌 Mark Attendance</a>
        <a href="addmark.php">📝 Add Marks</a>
        <a href="view_reports.php">📄 View Reports</a>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>
</div>

</body>
</html>
