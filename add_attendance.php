<?php
session_start();
include("db.php");
require('fpdf.php'); // make sure fpdf.php is in the same folder

// Only admin or teacher can access
if(!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "teacher")){
    header("Location: index.php");
    exit;
}

$message = ""; 
$attendance_saved = false;
$attendance_exists = false; // ✅ new flag

// Save Attendance
if(isset($_POST['submit_attendance'])){
    $date = $_POST['date'];
    $grade = $_POST['grade'];

    // ✅ Check if attendance already exists for this grade & date
    $check = mysqli_query($conn, "
        SELECT a.id 
        FROM attendance a 
        JOIN students s ON a.student_id = s.id 
        WHERE s.grade='$grade' AND a.date='$date'
        LIMIT 1
    ");

    if(mysqli_num_rows($check) > 0){
        $attendance_exists = true;
        $message = "⚠️ Attendance for Grade $grade on $date has already been submitted!";
    } else {
        // Load student names for mapping
        $students_res = mysqli_query($conn,"SELECT id, name FROM students WHERE grade='$grade'");
        $students_name = [];
        while($s = mysqli_fetch_assoc($students_res)){
            $students_name[$s['id']] = $s['name'];
        }

        foreach($_POST['status'] as $student_id => $status){
            $student_name = mysqli_real_escape_string($conn, $students_name[$student_id]);
            mysqli_query($conn,"INSERT INTO attendance(student_id, student_name, date, status, teacher_name)
                VALUES('$student_id', '$student_name', '$date', '$status', '{$_SESSION['user']}')");
        }
        $attendance_saved = true;
        $message = "✅ Attendance saved successfully for Grade $grade on $date!";
    }
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
$existing_attendance = []; // ✅ to store existing statuses

if(isset($_GET['grade'])){
    $selected_grade = $_GET['grade'];
    if(isset($_GET['date'])){
        $selected_date = $_GET['date'];
    }

    // ✅ Check existing attendance
    $check_existing = mysqli_query($conn, "
        SELECT a.student_id, a.status 
        FROM attendance a 
        JOIN students s ON a.student_id = s.id 
        WHERE s.grade='$selected_grade' AND a.date='$selected_date'
    ");
    if(mysqli_num_rows($check_existing) > 0){
        $attendance_exists = true;
        while($r = mysqli_fetch_assoc($check_existing)){
            $existing_attendance[$r['student_id']] = $r['status'];
        }
    }

    // Load students
    $students = mysqli_query($conn, "SELECT * FROM students WHERE grade='$selected_grade'");
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
.alert{ background:#ffdddd; color:#d00; padding:10px; border-left:5px solid #d00; margin-bottom:15px; }
.success{ background:#ddffdd; color:#090; padding:10px; border-left:5px solid #090; margin-bottom:15px; }
.status-present{ color:green; font-weight:bold; }
.status-absent{ color:red; font-weight:bold; }
</style>
</head>

<body>

<?php if(!empty($message)): ?>
<script>
    alert("<?= addslashes($message) ?>");
</script>
<?php endif; ?>

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
<form method="POST">
    <input type="hidden" name="date" value="<?= $selected_date ?>">
    <input type="hidden" name="grade" value="<?= $selected_grade ?>">

    <?php if($attendance_exists): ?>
        <div class="alert">⚠️ Attendance for Grade <?= $selected_grade ?> on <?= $selected_date ?> already submitted.</div>
    <?php endif; ?>

    <table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Course</th>
        <th>Status</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($students)){ 
        $id = $row['id'];
        $existing_status = $existing_attendance[$id] ?? '';
    ?>
    <tr>
        <td><?= $id ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['course'] ?></td>
        <td>
            <?php if($attendance_exists): ?>
                <?php if($existing_status == 'Present'): ?>
                    <span class="status-present">✅ Present</span>
                <?php elseif($existing_status == 'Absent'): ?>
                    <span class="status-absent">❌ Absent</span>
                <?php else: ?>
                    <span>-</span>
                <?php endif; ?>
            <?php else: ?>
                <label><input type="radio" name="status[<?= $id ?>]" value="Present" required> ✅ Present</label>
                <label><input type="radio" name="status[<?= $id ?>]" value="Absent"> ❌ Absent</label>
            <?php endif; ?>
        </td>
    </tr>
    <?php } ?>
    </table>

    <br>
    <button type="submit" name="submit_attendance" <?= $attendance_exists ? "disabled" : "" ?>>✅ Save Attendance</button>
</form>
<?php endif; ?>

<?php if($attendance_saved || $attendance_exists): ?>
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
