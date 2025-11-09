<?php
session_start();
include("db.php");
require('fpdf.php'); // Make sure fpdf.php exists in your folder

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher'){
    header("Location: login.php");
    exit;
}

// Generate PDF function
function generate_report($conn, $type, $grade, $date_or_month){
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);

    if($type == 'daily'){
        $pdf->Cell(0,10,"Daily Attendance Report - Grade $grade ($date_or_month)",0,1,'C');
        $query = "SELECT s.id, s.name, s.course, a.status
                  FROM students s
                  LEFT JOIN attendance a ON s.id = a.student_id AND a.date='$date_or_month'
                  WHERE s.grade='$grade'";
    } elseif($type == 'monthly'){
        $pdf->Cell(0,10,"Monthly Attendance Report - Grade $grade ($date_or_month)",0,1,'C');
        $query = "SELECT s.id, s.name, s.course,
                         COUNT(CASE WHEN a.status='Present' THEN 1 END) AS present_days,
                         COUNT(CASE WHEN a.status='Absent' THEN 1 END) AS absent_days
                  FROM students s
                  LEFT JOIN attendance a ON s.id = a.student_id AND a.date LIKE '$date_or_month%'
                  WHERE s.grade='$grade'
                  GROUP BY s.id";
    } else {
        // Marks Report
        $pdf->Cell(0,10,"Marks Report - Grade $grade",0,1,'C');
        $query = "SELECT id, name, course, mark FROM students WHERE grade='$grade'";
    }

    $res = mysqli_query($conn, $query);

    // Table headers
    $pdf->SetFont('Arial','B',12);
    if($type == 'daily'){
        $pdf->Cell(20,10,'ID',1);
        $pdf->Cell(60,10,'Name',1);
        $pdf->Cell(50,10,'Course',1);
        $pdf->Cell(30,10,'Status',1);
    } elseif($type == 'monthly'){
        $pdf->Cell(20,10,'ID',1);
        $pdf->Cell(60,10,'Name',1);
        $pdf->Cell(30,10,'Present',1);
        $pdf->Cell(30,10,'Absent',1);
        $pdf->Cell(40,10,'Attendance %',1);
    } else {
        $pdf->Cell(20,10,'ID',1);
        $pdf->Cell(60,10,'Name',1);
        $pdf->Cell(50,10,'Course',1);
        $pdf->Cell(30,10,'Marks',1);
    }
    $pdf->Ln();

    $pdf->SetFont('Arial','',12);
    while($row = mysqli_fetch_assoc($res)){
        if($type == 'daily'){
            $pdf->Cell(20,10,$row['id'],1);
            $pdf->Cell(60,10,$row['name'],1);
            $pdf->Cell(50,10,$row['course'],1);
            $pdf->Cell(30,10,$row['status'] ?? 'Absent',1);
        } elseif($type == 'monthly'){
            $total = $row['present_days'] + $row['absent_days'];
            $percent = $total > 0 ? round(($row['present_days'] / $total) * 100, 2) . '%' : '0%';
            $pdf->Cell(20,10,$row['id'],1);
            $pdf->Cell(60,10,$row['name'],1);
            $pdf->Cell(30,10,$row['present_days'],1);
            $pdf->Cell(30,10,$row['absent_days'],1);
            $pdf->Cell(40,10,$percent,1);
        } else {
            $pdf->Cell(20,10,$row['id'],1);
            $pdf->Cell(60,10,$row['name'],1);
            $pdf->Cell(50,10,$row['course'],1);
            $pdf->Cell(30,10,$row['mark'],1);
        }
        $pdf->Ln();
    }

    $filename = "Report_{$grade}_{$type}_{$date_or_month}.pdf";
    $pdf->Output('D', $filename);
    exit;
}

// Handle report generation
if(isset($_POST['generate_daily'])){
    generate_report($conn, 'daily', $_POST['grade'], $_POST['date']);
}
if(isset($_POST['generate_monthly'])){
    generate_report($conn, 'monthly', $_POST['grade'], $_POST['month']);
}
if(isset($_POST['generate_marks'])){
    generate_report($conn, 'marks', $_POST['grade'], date('Y'));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📄 View Reports</title>
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
        max-width: 800px;
        margin: 60px auto;
        background-color: white;
        border-radius: 10px;
        padding: 40px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    form {
        margin-top: 20px;
    }

    label {
        font-weight: bold;
        display: block;
        margin: 10px 0 5px;
    }

    select, input[type=date], input[type=month] {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        margin-bottom: 15px;
    }

    button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #0056b3;
    }

    .logout {
        background-color: #dc3545;
    }

    .logout:hover {
        background-color: #a71d2a;
    }
</style>
</head>
<body>

<div class="navbar">
    <h1>📄 Teacher Reports</h1>
    <a href="teacher_dashboard.php">🏠 Back</a>
</div>

<div class="container">
    <h2>Generate Attendance or Marks Reports</h2>

    <form method="POST">
        <label>Select Grade:</label>
        <select name="grade" required>
            <option value="">-- Select --</option>
            <?php
            $grades = mysqli_query($conn, "SELECT DISTINCT grade FROM students ORDER BY grade ASC");
            while($g = mysqli_fetch_assoc($grades)){
                echo "<option value='{$g['grade']}'>Grade {$g['grade']}</option>";
            }
            ?>
        </select>

        <label>Select Date (Daily Report):</label>
        <input type="date" name="date" value="<?= date('Y-m-d') ?>">

        <label>Select Month (Monthly Report):</label>
        <input type="month" name="month" value="<?= date('Y-m') ?>">

        <div style="margin-top:20px;">
            <button type="submit" name="generate_daily">📅 Daily Attendance Report</button>
            <button type="submit" name="generate_monthly">🗓️ Monthly Attendance Report</button>
            <button type="submit" name="generate_marks">📊 Marks Report</button>
        </div>
    </form>
</div>

</body>
</html>
