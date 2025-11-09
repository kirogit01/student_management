<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$teacher = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM teachers WHERE id='$id'"));

if(isset($_POST['submit'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $grade = mysqli_real_escape_string($conn, $_POST['grade']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);

    mysqli_query($conn, "UPDATE teachers SET username='$username', name='$name', email='$email', phone_number='$phone_number', grade='$grade', subject='$subject' WHERE id='$id'");
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
body{font-family:'Poppins',sans-serif;background:#f4f6f9;color:#333;margin:0;padding:0;}
.container{max-width:600px;margin:50px auto;background:white;padding:30px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.1);}
h2{color:#4e73df;text-align:center;margin-bottom:25px;font-weight:600;}
form label{display:block;margin-top:15px;font-weight:500;}
form input{width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:6px;transition:0.3s;}
form input:focus{border-color:#4e73df;outline:none;box-shadow:0 0 5px rgba(78,115,223,0.3);}
form button{margin-top:20px;background:#f6c23e;color:black;border:none;padding:12px 20px;border-radius:6px;cursor:pointer;font-weight:500;font-size:16px;transition:0.3s;}
form button:hover{background:#dda20a;color:white;}
a.back{display:inline-block;margin-top:15px;color:#4e73df;text-decoration:none;font-weight:500;transition:0.3s;}
a.back:hover{color:#224abe;}
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

<label>Phone Number:</label>
<input type="text" name="phone_number" value="<?= htmlspecialchars($teacher['phone_number']) ?>" required>

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
