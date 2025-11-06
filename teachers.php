<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: index.php");
    exit();
}


// --- Handle teacher search ---
$search_query = "";
if(isset($_GET['search']) && !empty(trim($_GET['search']))){
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $teachers = mysqli_query($conn, "SELECT * FROM teachers
        WHERE username LIKE '%$search_query%'
        OR name LIKE '%$search_query%'
        OR grade LIKE '%$search_query%'
        OR subject LIKE '%$search_query%'
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
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f8fafc;color:#333;line-height:1.6;padding:20px;}
header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
header h2{color:#6f42c1;}
header a.btn{background:#28a745;color:white;padding:8px 14px;text-decoration:none;border-radius:6px;font-size:14px;}
header a.btn:hover{background:#218838;}
.search-form{margin-left:auto;display:flex;gap:10px;}
.search-form input[type=text]{padding:6px 10px;border:1px solid #ccc;border-radius:6px;min-width:200px;}
.search-form button{background:#007bff;color:white;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;}
.search-form button:hover{background:#0056b3;}
table{width:100%;border-collapse:collapse;background:white;box-shadow:0 2px 8px rgba(0,0,0,0.05);border-radius:10px;overflow:hidden;margin-top:15px;}
th,td{padding:12px;text-align:center;}
th{background:#6f42c1;color:white;}
tr:nth-child(even){background:#f8f9fa;}
tr:hover{background:#e9f3ff;}
a{text-decoration:none;padding:6px 10px;border-radius:5px;}
a.edit{background:#ffc107;color:black;}
a.delete{background:#dc3545;color:white;}
a.back{background:#007bff;color:white;}
</style>
</head>
<body>

<header>
    <h2>👩‍🏫 Teachers Details</h2>
    <div style="display:flex;gap:10px;">
        <a href="add_teacher.php" class="btn">➕ Add New Teacher</a>
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search Username, Name, Grade, Subject" value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit">Search</button>
        </form>
    </div>
</header>

<a href="dashboard.php" class="back">⬅️ Back to Dashboard</a>

<table>
<tr>
<th>ID</th><th>Username</th><th>Name</th><th>Email</th><th>Grade</th><th>Subject</th><th>Action</th>
</tr>
<?php if(mysqli_num_rows($teachers) > 0): ?>
<?php while($t = mysqli_fetch_assoc($teachers)): ?>
<tr>
<td><?= $t['id'] ?></td>
<td><?= htmlspecialchars($t['username']) ?></td>
<td><?= htmlspecialchars($t['name']) ?></td>
<td><?= htmlspecialchars($t['email']) ?></td>
<td><?= htmlspecialchars($t['grade']) ?></td>
<td><?= htmlspecialchars($t['subject']) ?></td>
<td>
<a href="edit_teacher.php?id=<?= $t['id'] ?>" class="edit">Edit</a>
<a href="delete_teacher.php?id=<?= $t['id'] ?>" class="delete" onclick="return confirm('Delete this teacher?');">Delete</a>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="7">❌ No teachers found.</td></tr>
<?php endif; ?>
</table>

</body>
</html>
