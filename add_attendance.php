<?php
session_start();
include("db.php");
require('fpdf.php'); // make sure fpdf.php is in the same folder

// Only admin or teacher can access
if(!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "teacher")){
    header("Location: index.php");
    exit;
}

$attendance_saved = false; // flag to show report buttons

// Save Attendance
if(isset($_POST['submit_attendance'])){
    $date = $_POST['date'];
    $grade = $_POST['grade'];
    foreach($_POST['status'] as $student_id => $status){
        mysqli_query($conn,"INSERT INTO attendance(student_id, date, status, teacher_name)
            VALUES('$student_id', '$date', '$status', '{$_SESSION['user']}')");
    }
    $attendance_saved = true;
}

// Generate PDF Report function
function generate_pdf($conn, $grade, $type, $date_or_month){
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);

    if($type == 'daily'){
        $pdf->Cell(0,10,"Daily Attendance Report - Grade $grade ($date_or_month)",0,1,'C');
        $query = "SELECT s.id, s.name, s.course, a.status
                  FROM students s
                  LEFT JOIN attendance a ON s.id = a.student_id AND a.date='$date_or_month'
                  WHERE s.grade='$grade'";
    } else {
        $pdf->Cell(0,10,"Monthly Attendance Report - Grade $grade ($date_or_month)",0,1,'C');
        $query = "SELECT s.id, s.name, s.course, 
                         SUM(CASE WHEN a.status='Present' THEN 1 ELSE 0 END) as present_count,
                         SUM(CASE WHEN a.status='Absent' THEN 1 ELSE 0 END) as absent_count
                  FROM students s
                  LEFT JOIN attendance a ON s.id = a.student_id AND a.date LIKE '$date_or_month%'
                  WHERE s.grade='$grade'
                  GROUP BY s.id";
    }

    $res = mysqli_query($conn, $query);
    if(!$res){
        die("SQL Error: ".mysqli_error($conn)."<br>Query: ".$query);
    }

    // Table header
    $pdf->SetFont('Arial','B',12);
    if($type=='daily'){
        $pdf->Cell(20,10,'ID',1);
        $pdf->Cell(50,10,'Name',1);
        $pdf->Cell(50,10,'Course',1);
        $pdf->Cell(30,10,'Status',1);
    } else {
        $pdf->Cell(20,10,'ID',1);
        $pdf->Cell(50,10,'Name',1);
        $pdf->Cell(30,10,'Present',1);
        $pdf->Cell(30,10,'Absent',1);
    }
    $pdf->Ln();

    // Table data
    $pdf->SetFont('Arial','',12);
    while($row = mysqli_fetch_assoc($res)){
        if($type=='daily'){
            $pdf->Cell(20,10,$row['id'],1);
            $pdf->Cell(50,10,$row['name'],1);
            $pdf->Cell(50,10,$row['course'],1);
            $pdf->Cell(30,10,$row['status'] ?? 'Absent',1);
        } else {
            $pdf->Cell(20,10,$row['id'],1);
            $pdf->Cell(50,10,$row['name'],1);
            $pdf->Cell(30,10,$row['present_count'],1);
            $pdf->Cell(30,10,$row['absent_count'],1);
        }
        $pdf->Ln();
    }

    $filename = "Attendance_Report_{$grade}_{$type}_{$date_or_month}.pdf";
    $pdf->Output('D', $filename);
    exit;
}

// Handle report button clicks
if(isset($_POST['daily_report'])){
    generate_pdf($conn, $_POST['grade'], 'daily', $_POST['date']);
}

if(isset($_POST['monthly_report'])){
    generate_pdf($conn, $_POST['grade'], 'monthly', $_POST['month']);
}

// Step 1: If grade selected — show students
$students = [];
$selected_grade = '';
$selected_date = date('Y-m-d');
if(isset($_GET['grade'])){
    $selected_grade = $_GET['grade'];
    $students = mysqli_query($conn, "SELECT * FROM students WHERE grade='$selected_grade'");
    if(isset($_GET['date'])){
        $selected_date = $_GET['date'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>📌 Mark Attendance</title>
<style>
body{ font-family:Arial; background:#f3f3f3; padding:20px; }
.container{ background:white; width:80%; margin:auto; padding:20px; border-radius:10px; }
table{ width:100%; border-collapse:collapse; margin-top:20px; }
th,td{ border:1px solid #ccc; padding:10px; text-align:center; }
th{ background:#007bff; color:white; }
button, select, input{ padding:8px; border-radius:5px; margin-right:5px; }
</style>
</head>

<body>
<div class="container">

<h2>📌 Mark Attendance</h2>

<!-- Step 1: Select Grade and Date -->
<form method="GET">
    <label>Select Grade:</label>
    <select name="grade" required>
        <option value="">-- Select --</option>
        <?php
        $grade_sql = mysqli_query($conn,"SELECT DISTINCT grade FROM students ORDER BY grade ASC");
        while($g = mysqli_fetch_assoc($grade_sql)){
            echo "<option value='{$g['grade']}' ".($selected_grade==$g['grade']?"selected":"").">Grade {$g['grade']}</option>";
        }
        ?>
    </select>

    <label>Select Date:</label>
    <input type="date" name="date" value="<?= $selected_date ?>" required>

    <button type="submit">Load Students</button>
</form>

<?php if($selected_grade && $students->num_rows>0): ?>

<!-- Step 2: Show Student List -->
<form method="POST">
    <input type="hidden" name="date" value="<?= $selected_date ?>">
    <input type="hidden" name="grade" value="<?= $selected_grade ?>">

    <table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Course</th>
        <th>Status</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($students)){ ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['course'] ?></td>
        <td>
            <label><input type="radio" name="status[<?= $row['id'] ?>]" value="Present" required> ✅ Present</label>
            <label><input type="radio" name="status[<?= $row['id'] ?>]" value="Absent"> ❌ Absent</label>
        </td>
    </tr>
    <?php } ?>
    </table>

    <br>
    <button type="submit" name="submit_attendance">✅ Save Attendance</button>
</form>

<?php endif; ?>

<?php if($attendance_saved): ?>
<!-- Step 3: Show report buttons -->
<form method="POST" style="margin-top:20px;">
    <input type="hidden" name="grade" value="<?= $selected_grade ?>">
    <input type="hidden" name="date" value="<?= $selected_date ?>">
    <label>Select Month:</label>
    <input type="month" name="month" value="<?= date('Y-m') ?>">
    <br><br>
    <button type="submit" name="daily_report">📄 Download Daily Report</button>
    <button type="submit" name="monthly_report">📄 Download Monthly Report</button>
</form>
<?php endif; ?>

</div>
</body>
</html>
