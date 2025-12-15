<?php
session_start();
include("db.php");
require('fpdf.php');

/* ================= ACCESS CONTROL ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: index.php");
    exit();
}

/* ================= SEARCH ================= */
$search_query = "";
if(isset($_GET['search']) && !empty(trim($_GET['search']))){
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $teachers = mysqli_query($conn, "SELECT * FROM teachers
        WHERE username LIKE '%$search_query%'
        OR name LIKE '%$search_query%'
        OR grade LIKE '%$search_query%'
        OR subject LIKE '%$search_query%'
        OR phone_number LIKE '%$search_query%'
        ORDER BY id DESC");
} else {
    $teachers = mysqli_query($conn, "SELECT * FROM teachers ORDER BY id DESC");
}

/* ================= PDF FUNCTION ================= */
function generate_teachers_pdf($conn){
    $pdf = new FPDF('L','mm','A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'Teachers Details Report',0,1,'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial','B',10);
    $headers = ['ID','Username','Name','Email','Phone','Grade','Subject'];
    $widths = [15,35,40,70,35,25,40];

    foreach($headers as $i => $h){
        $pdf->Cell($widths[$i],8,$h,1);
    }
    $pdf->Ln();

    $pdf->SetFont('Arial','',9);
    $res = mysqli_query($conn, "SELECT * FROM teachers ORDER BY id DESC");
    while($row = mysqli_fetch_assoc($res)){
        $pdf->Cell(15,8,$row['id'],1);
        $pdf->Cell(35,8,$row['username'],1);
        $pdf->Cell(40,8,$row['name'],1);
        $pdf->Cell(70,8,$row['email'],1);
        $pdf->Cell(35,8,$row['phone_number'],1);
        $pdf->Cell(25,8,$row['grade'],1);
        $pdf->Cell(40,8,$row['subject'],1);
        $pdf->Ln();
    }

    $pdf->Output('D','Teachers_Details.pdf');
    exit;
}

/* ================= HANDLE PDF REQUEST ================= */
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['teachers_pdf'])){
        generate_teachers_pdf($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>👩‍🏫 Teachers Details</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f5f6fa;color:#333;line-height:1.6;}
.container{max-width:1300px;margin:40px auto;padding:20px;}

/* HEADER */
header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 2px 6px rgba(0,0,0,0.08);
    border:1px solid #e0e0e0;
}
header h2{color:#4A90E2;font-size:26px;font-weight:600;}

.btn{
    background:#4A90E2;
    color:white;
    padding:10px 16px;
    text-decoration:none;
    border-radius:6px;
    transition:0.25s;
}
.btn:hover{background:#3277c7;}

.pdf-btn{
    background:#36b9cc;
    color:white;
    padding:10px 16px;
    border:none;
    border-radius:6px;
    font-weight:500;
    cursor:pointer;
}
.pdf-btn:hover{background:#258faf;}

.search-form{display:flex;gap:10px;}
.search-form input{
    padding:8px 12px;
    border-radius:6px;
    border:1px solid #ccc;
    min-width:260px;
}
.search-form button{
    background:#4A90E2;
    color:white;
    border:none;
    padding:8px 14px;
    border-radius:6px;
}
.search-form button:hover{background:#3277c7;}

a.back{
    display:inline-block;
    margin:15px 0;
    color:#4A90E2;
    text-decoration:none;
    font-weight:500;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 2px 8px rgba(0,0,0,0.06);
}
th,td{padding:14px;text-align:center;font-size:14px;}
th{background:#4A90E2;color:white;}
tr:nth-child(even){background:#fafafa;}
tr:hover{background:#eef4ff;}

.edit{background:#f0d67c;color:#000;padding:7px 12px;border-radius:6px;}
.delete{background:#e07a7a;color:white;padding:7px 12px;border-radius:6px;}
</style>
</head>

<body>
<div class="container">

<header>
    <h2>👩‍🏫 Teachers Details</h2>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="add_teacher.php" class="btn">➕ Add New Teacher</a>

        <form method="POST">
            <button type="submit" name="teachers_pdf" class="pdf-btn">📄 Download PDF</button>
        </form>

        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search Username, Name, Grade, Subject, Phone" value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit">Search</button>
        </form>
    </div>
</header>

<a href="dashboard.php" class="back">⬅️ Back to Admin Dashboard</a>

<table>
<tr>
<th>ID</th>
<th>Username</th>
<th>Name</th>
<th>Email</th>
<th>Phone Number</th>
<th>Grade</th>
<th>Subject</th>
<th>Action</th>
</tr>

<?php if(mysqli_num_rows($teachers) > 0): ?>
<?php while($t = mysqli_fetch_assoc($teachers)): ?>
<tr>
<td><?= $t['id'] ?></td>
<td><?= htmlspecialchars($t['username']) ?></td>
<td><?= htmlspecialchars($t['name']) ?></td>
<td><?= htmlspecialchars($t['email']) ?></td>
<td><?= htmlspecialchars($t['phone_number']) ?></td>
<td><?= htmlspecialchars($t['grade']) ?></td>
<td><?= htmlspecialchars($t['subject']) ?></td>
<td>
<a href="edit_teacher.php?id=<?= $t['id'] ?>" class="edit">Edit</a>
<a href="delete_teacher.php?id=<?= $t['id'] ?>" class="delete" onclick="return confirm('Delete this teacher?');">Delete</a>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="8">❌ No teachers found.</td></tr>
<?php endif; ?>
</table>

</div>
</body>
</html>
