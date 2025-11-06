<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin'])){
    header("Location: index.php");
    exit;
}

if(isset($_POST['submit'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $grade = mysqli_real_escape_string($conn, $_POST['grade']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $password = md5($_POST['password']);

    mysqli_query($conn, "INSERT INTO teachers (username,name,email,grade,subject,password) 
        VALUES ('$username','$name','$email','$grade','$subject','$password')");
    header("Location: teachers.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>➕ Add New Teacher</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;background:#f8fafc;color:#333;margin:0;padding:0;}
.container{max-width:600px;margin:50px auto;background:white;padding:30px;border-radius:12px;box-shadow:0 3px 15px rgba(0,0,0,0.1);}
h2{color:#6f42c1;text-align:center;margin-bottom:25px;}
form label{display:block;margin-top:15px;font-weight:500;}
form input{width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:6px;}
form button{margin-top:20px;background:#28a745;color:white;border:none;padding:10px 15px;border-radius:6px;cursor:pointer;font-weight:500;}
form button:hover{background:#218838;}
a.back{display:inline-block;margin-top:15px;color:#007bff;text-decoration:none;font-weight:500;}
a.back:hover{color:#0056b3;}
</style>
</head>
<body>

<div class="container">
<h2>➕ Add New Teacher</h2>

<form method="POST">
<label>Username:</label>
<input type="text" name="username" required>

<label>Name:</label>
<input type="text" name="name" required>

<label>Email:</label>
<input type="email" name="email" required>

<label>Grade:</label>
<input type="text" name="grade" required>

<label>Subject:</label>
<input type="text" name="subject" required>

<label>Password:</label>
<input type="password" name="password" required>

<button type="submit" name="submit">Add Teacher</button>
</form>
<a href="teachers.php" class="back">⬅️ Back to Teachers</a>
</div>

</body>
</html>
