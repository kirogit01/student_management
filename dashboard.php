<?php
session_start();
include("db.php");

// ✅ Correct admin session
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📊 Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* Same CSS as before */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f8fafc;color:#333;line-height:1.6;}
.container{max-width:1300px;margin:40px auto;padding:20px;}
header{display:flex;justify-content:space-between;align-items:center;background:linear-gradient(90deg,#007bff,#00c6ff);color:white;padding:15px 25px;border-radius:12px;box-shadow:0 3px 10px rgba(0,0,0,0.1);}
header h2{font-weight:600;}
.action-bar{margin:20px 0;display:flex;flex-wrap:wrap;gap:10px;}
.action-bar a{background:#007bff;color:white;padding:8px 14px;text-decoration:none;border-radius:6px;font-size:14px;font-weight:500;transition:0.3s;}
.action-bar a:hover{background:#0056b3;}
.action-bar a.teachers{background:#6f42c1;}
.action-bar a.teachers:hover{background:#5a3499;}
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
  <a href="teachers.php" class="teachers">👩‍🏫 Teachers Details</a>
  <a href="students_marks.php">📝 Students Marks</a>
  <a href="export.php">⬇️ Export to Excel</a>
  <a href="logout.php">🚪 Logout</a>
  <form method="GET" class="search-form">
      <input type="text" name="search" placeholder="🔍 Search by Name, Grade or Course" value="<?= htmlspecialchars($search_query) ?>">
      <button type="submit">Search</button>
  </form>
</div>

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
new Chart(document.getElementById('courseChart'), {
  type:'doughnut',
  data:{
    labels:<?= $course_labels_json ?>,
    datasets:[{data:<?= $course_counts_json ?>,backgroundColor:['#007bff','#28a745','#ffc107','#dc3545','#17a2b8','#6f42c1','#ff66b2','#20c997']}]
  },
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
</script>
</body>
</html>
