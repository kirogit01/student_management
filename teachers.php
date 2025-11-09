<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: index.php");
    exit();
}

// Handle search
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>👩‍🏫 Teachers Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* ===== General Reset ===== */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f4f6f9;color:#333;line-height:1.6;}

/* ===== Container ===== */
.container{max-width:1200px;margin:40px auto;padding:20px;}

/* ===== Header ===== */
header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;}
header h2{color:#4e73df;font-weight:600;font-size:28px;}
header a.btn{background:#1cc88a;color:white;padding:10px 20px;text-decoration:none;border-radius:6px;font-size:14px;transition:0.3s;}
header a.btn:hover{background:#17a673;}

/* ===== Search Form ===== */
.search-form{display:flex;gap:10px;}
.search-form input[type=text]{padding:8px 12px;border:1px solid #ccc;border-radius:6px;min-width:250px;}
.search-form button{background:#4e73df;color:white;border:none;padding:8px 15px;border-radius:6px;cursor:pointer;transition:0.3s;}
.search-form button:hover{background:#2e59d9;}

/* ===== Back Button ===== */
a.back{display:inline-block;margin-bottom:15px;color:#4e73df;text-decoration:none;font-weight:500;transition:0.3s;}
a.back:hover{color:#224abe;}

/* ===== Table Styles ===== */
table{width:100%;border-collapse:collapse;background:white;box-shadow:0 4px 12px rgba(0,0,0,0.05);border-radius:10px;overflow:hidden;}
th, td{padding:15px;text-align:center;}
th{background:#4e73df;color:white;font-weight:500;text-transform:uppercase;}
tr:nth-child(even){background:#f8f9fc;}
tr:hover{background:#e2e6ea;transition:0.3s;}
td a.edit{background:#f6c23e;color:black;padding:6px 12px;border-radius:5px;transition:0.3s;}
td a.edit:hover{background:#dda20a;color:white;}
td a.delete{background:#e74a3b;color:white;padding:6px 12px;border-radius:5px;transition:0.3s;}
td a.delete:hover{background:#c12e2a;color:white;}
td a.view{background:#36b9cc;color:white;padding:6px 12px;border-radius:5px;transition:0.3s;}
td a.view:hover{background:#258faf;color:white;}

/* ===== Responsive ===== */
@media screen and (max-width: 768px){
    table, thead, tbody, th, td, tr{display:block;}
    th{position:absolute;top:-9999px;left:-9999px;}
    tr{margin-bottom:15px;}
    td{position:relative;padding-left:50%;text-align:left;}
    td:before{position:absolute;top:12px;left:12px;width:45%;white-space:nowrap;font-weight:600;}
    td:nth-of-type(1):before{content:"ID";}
    td:nth-of-type(2):before{content:"Username";}
    td:nth-of-type(3):before{content:"Name";}
    td:nth-of-type(4):before{content:"Email";}
    td:nth-of-type(5):before{content:"Phone Number";}
    td:nth-of-type(6):before{content:"Grade";}
    td:nth-of-type(7):before{content:"Subject";}
    td:nth-of-type(8):before{content:"Action";}
}
</style>
</head>
<body>
<div class="container">
<header>
    <h2>👩‍🏫 Teachers Dashboard</h2>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="add_teacher.php" class="btn">➕ Add New Teacher</a>
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
