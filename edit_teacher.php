<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin'])){
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$teacher = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM teachers WHERE id='$id'"));

if(isset($_POST['submit'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $grade = mysqli_real_escape_string($conn, $_POST['grade']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);

    mysqli_query($conn, "UPDATE teachers SET username='$username', name='$name', email='$email', grade='$grade', subject='$subject' WHERE id='$id'");
    header("Location: teachers.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Teacher</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;background:#f8fafc;color:#333;margin:0;padding:0;}
.container{max-width:600px;margin:50px auto;background:white;padding:30px;border-radius:12px;box-shadow:0 3px 15px rgba(0,0,0,0.1);}
h2{color:#6f42c1;text-align:center;margin-bottom:25px;}
form label{display:block;margin-top:15px;font-weight:500;}
form input{width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:6px;}
form button{margin-top:20px;background:#ffc107;color:black;border:none;padding:10px 15px;border-radius:6px;cursor:pointer;font-weight:500;}
form button:hover{background:#e0a800;}
a.back{display:inline-block;margin-top:15px;color:#007bff;text-decoration:none;font-weight:500;}
a.back:hover{color:#0056b3;}
</style>
</head>
<body>

<div class="container">
<h2>Edit Teacher Details</h2>

<form method="POST">
<label>Username:</label>
<input type="text" name="username" value="<?= htmlspecialchars($teacher['username']) ?>" required>

<label>Name:</label>
<input type="text" name="name" value="<?= htmlspecialchars($teacher['name']) ?>" required>

<label>Email:</label>
<input type="email" name="email" value="<?= htmlspecialchars($teacher['email']) ?>" required>

<label>Grade:</label>
<input type="text" name="grade" value="<?= htmlspecialchars($teacher['grade']) ?>" required>

<label>Subject:</label>
<input type="text" name="subject" value="<?= htmlspecialchars($teacher['subject']) ?>" required>

<button type="submit" name="submit">Update Teacher</button>
</form>
<a href="teachers.php" class="back">⬅️ Back to Teachers</a>
</div>

</body>
</html>
