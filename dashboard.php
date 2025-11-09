<?php
session_start();
include("db.php");
require('fpdf.php');

// ✅ Only admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: index.php");
    exit;
}

// --- Handle student search ---
$search_query = "";
if(isset($_GET['search']) && !empty(trim($_GET['search']))){
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $students = mysqli_query($conn, "SELECT * FROM students 
        WHERE name LIKE '%$search_query%' 
        OR grade LIKE '%$search_query%' 
        OR course LIKE '%$search_query%' 
        ORDER BY id DESC");
} else {
    $students = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
}

// --- Course Statistics ---
$course_query = mysqli_query($conn, "SELECT course, COUNT(*) AS count FROM students GROUP BY course");
$course_labels = $course_counts = [];
while($row = mysqli_fetch_assoc($course_query)){
    $course_labels[] = $row['course'];
    $course_counts[] = $row['count'];
}
$course_labels_json = json_encode($course_labels);
$course_counts_json = json_encode($course_counts);

// --- Gender Statistics ---
$gender_query = mysqli_query($conn, "SELECT gender, COUNT(*) AS count FROM students GROUP BY gender");
$gender_labels = $gender_counts = [];
while($g = mysqli_fetch_assoc($gender_query)){
    $gender_labels[] = $g['gender'];
    $gender_counts[] = $g['count'];
}
$gender_labels_json = json_encode($gender_labels);
$gender_counts_json = json_encode($gender_counts);

// --- Grade Statistics ---
$grade_query = mysqli_query($conn, "SELECT grade, COUNT(*) AS count FROM students GROUP BY grade");
$grade_labels = $grade_counts = [];
while($gr = mysqli_fetch_assoc($grade_query)){
    $grade_labels[] = $gr['grade'];
    $grade_counts[] = $gr['count'];
}
$grade_labels_json = json_encode($grade_labels);
$grade_counts_json = json_encode($grade_counts);

// --- PDF Generation function ---
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
    } else { // monthly
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
    if(!$res){ die("SQL Error: ".mysqli_error($conn)); }

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

// --- Handle PDF Download ---
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['daily_report']) && !empty($_POST['grade']) && !empty($_POST['date'])){
        generate_pdf($conn, $_POST['grade'], 'daily', $_POST['date']);
    }
    if(isset($_POST['monthly_report']) && !empty($_POST['grade']) && !empty($_POST['month'])){
        generate_pdf($conn, $_POST['grade'], 'monthly', $_POST['month']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📊 Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f8fafc;color:#333;line-height:1.6;}
.container{max-width:1300px;margin:40px auto;padding:20px;}
header{display:flex;justify-content:space-between;align-items:center;background:linear-gradient(90deg,#007bff,#00c6ff);color:white;padding:15px 25px;border-radius:12px;box-shadow:0 3px 10px rgba(0,0,0,0.1);}
header h2{font-weight:600;}
.action-bar{margin:20px 0;display:flex;flex-wrap:wrap;gap:10px;}
.action-bar a{background:#007bff;color:white;padding:8px 14px;text-decoration:none;border-radius:6px;font-size:14px;font-weight:500;transition:0.3s;}
.action-bar a:hover{background:#0056b3;}
.search-form{margin-left:auto;display:flex;gap:10px;}
.search-form input[type=text]{padding:8px 10px;border:1px solid #ccc;border-radius:6px;min-width:250px;}
.search-form button{background:#28a745;color:white;border:none;padding:8px 14px;border-radius:6px;cursor:pointer;transition:0.3s;}
.search-form button:hover{background:#218838;}
table{width:100%;border-collapse:collapse;background:white;box-shadow:0 2px 8px rgba(0,0,0,0.05);border-radius:10px;overflow:hidden;margin-top:15px;}
th,td{padding:12px;text-align:center;}
th{background:#007bff;color:white;}
tr:nth-child(even){background:#f8f9fa;}
tr:hover{background:#e9f3ff;}
.charts-wrapper{display:flex;flex-wrap:wrap;gap:20px;margin-top:40px;}
.chart-container{flex:1 1 300px;min-width:300px;background:white;padding:20px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.08);}
.chart-container h3{text-align:center;color:#007bff;margin-bottom:10px;}
canvas{max-width:100%;}
@media(max-width:768px){header{flex-direction:column;gap:10px;text-align:center;}.action-bar{flex-direction:column;align-items:flex-start;}.search-form{margin-left:0;}}
</style>
</head>
<body>
<div class="container">

<header>
  <h2>🎓 Admin Dashboard</h2>
  <div>Welcome, <strong><?= htmlspecialchars($_SESSION['user']); ?></strong> 👋</div>
</header>

<div class="action-bar">
  <a href="add_student.php">➕ Add Student</a>
  <a href="teachers.php">👩‍🏫 Teachers Details</a>
  <a href="students_marks.php">📝 Students Marks</a>
  <a href="export.php">⬇️ Export to Excel</a>
  <a href="logout.php">🚪 Logout</a>
  <form method="GET" class="search-form">
      <input type="text" name="search" placeholder="🔍 Search by Name, Grade or Course" value="<?= htmlspecialchars($search_query) ?>">
      <button type="submit">Search</button>
  </form>
</div>

<!-- Attendance Reports -->
<h3 style="color:#007bff;margin-top:30px;">📄 Attendance Reports</h3>
<form method="POST" id="reportForm" style="margin-bottom:20px;">
    <label>Grade:</label>
    <select name="grade" id="gradeSelect" required>
        <option value="">--Select Grade--</option>
        <?php
        $grades = mysqli_query($conn, "SELECT DISTINCT grade FROM students ORDER BY grade ASC");
        while($g = mysqli_fetch_assoc($grades)){
            echo "<option value='{$g['grade']}'>{$g['grade']}</option>";
        }
        ?>
    </select>

    <label>Date (Daily):</label>
    <input type="date" id="dailyDate" name="date" value="<?= date('Y-m-d') ?>">

    <label>Month (Monthly):</label>
    <input type="month" id="monthlyMonth" name="month" value="<?= date('Y-m') ?>">

    <button type="submit" name="daily_report" id="dailyBtn" disabled>📄 Download Daily PDF</button>
    <button type="submit" name="monthly_report" id="monthlyBtn" disabled>📄 Download Monthly PDF</button>

    <p id="attendanceMsg" style="color:#dc3545;font-weight:500;margin-top:10px;"></p>
</form>

<!-- Students Table -->
<h3 style="color:#007bff;margin-bottom:10px;">👩‍🎓 All Students</h3>
<table>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Course</th><th>Gender</th><th>Address</th><th>Grade</th><th>Action</th>
</tr>
<?php if(mysqli_num_rows($students) > 0): ?>
<?php while($s = mysqli_fetch_assoc($students)): ?>
<tr>
<td><?= $s['id'] ?></td>
<td><?= htmlspecialchars($s['name']) ?></td>
<td><?= htmlspecialchars($s['email']) ?></td>
<td><?= htmlspecialchars($s['phone']) ?></td>
<td><?= htmlspecialchars($s['course']) ?></td>
<td><?= htmlspecialchars($s['gender']) ?></td>
<td><?= htmlspecialchars($s['address']) ?></td>
<td><?= $s['grade'] ?></td>
<td>
<a href="edit_student.php?id=<?= $s['id'] ?>" style="background:#ffc107;color:black;padding:6px 10px;border-radius:5px;text-decoration:none;">Edit</a>
<a href="delete_student.php?id=<?= $s['id'] ?>" style="background:#dc3545;color:white;padding:6px 10px;border-radius:5px;text-decoration:none;" onclick="return confirm('Delete this student?');">Delete</a>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="9">❌ No students found.</td></tr>
<?php endif; ?>
</table>

<!-- Charts Section -->
<div class="charts-wrapper">
<div class="chart-container">
<h3>🎯 Students Per Course</h3>
<canvas id="courseChart"></canvas>
</div>
<div class="chart-container">
<h3>⚧ Gender Distribution</h3>
<canvas id="genderChart"></canvas>
</div>
<div class="chart-container">
<h3>📊 Students Per Grade</h3>
<canvas id="gradeChart"></canvas>
</div>
</div>

</div>

<script>
// --- Chart.js setup ---
new Chart(document.getElementById('courseChart'), {
  type:'doughnut',
  data:{labels:<?= $course_labels_json ?>,datasets:[{data:<?= $course_counts_json ?>,backgroundColor:['#007bff','#28a745','#ffc107','#dc3545','#17a2b8','#6f42c1','#ff66b2','#20c997']}]},
  options:{plugins:{legend:{position:'bottom'}}}
});
new Chart(document.getElementById('genderChart'), {
  type:'pie',
  data:{labels:<?= $gender_labels_json ?>,datasets:[{data:<?= $gender_counts_json ?>,backgroundColor:['#007bff','#ff6384']}]},
  options:{plugins:{legend:{position:'bottom'}}}
});
new Chart(document.getElementById('gradeChart'), {
  type:'bar',
  data:{labels:<?= $grade_labels_json ?>,datasets:[{label:'Number of Students',data:<?= $grade_counts_json ?>,backgroundColor:'#17a2b8'}]},
  options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});

// --- Attendance check via AJAX ---
async function checkAttendance(type) {
  const grade = document.getElementById('gradeSelect').value;
  const date = type === 'daily'
    ? document.getElementById('dailyDate').value
    : document.getElementById('monthlyMonth').value;

  const dailyBtn = document.getElementById('dailyBtn');
  const monthlyBtn = document.getElementById('monthlyBtn');
  const msg = document.getElementById('attendanceMsg');

  if (!grade) {
    msg.textContent = '⚠️ Please select a grade.';
    dailyBtn.disabled = true;
    monthlyBtn.disabled = true;
    return;
  }

  const res = await fetch(`check_attendance.php?grade=${grade}&type=${type}&date=${date}`);
  const text = await res.text();

  if (text.trim() === 'exists') {
    msg.textContent = '✅ Attendance available. You can download the report.';
    if (type === 'daily') dailyBtn.disabled = false;
    else monthlyBtn.disabled = false;
  } else {
    msg.textContent = '⚠️ Attendance not yet submitted by teacher.';
    if (type === 'daily') dailyBtn.disabled = true;
    else monthlyBtn.disabled = true;
  }
}

document.getElementById('gradeSelect').addEventListener('change', () => {
  checkAttendance('daily');
  checkAttendance('monthly');
});
document.getElementById('dailyDate').addEventListener('change', () => checkAttendance('daily'));
document.getElementById('monthlyMonth').addEventListener('change', () => checkAttendance('monthly'));
</script>

</body>
</html>
