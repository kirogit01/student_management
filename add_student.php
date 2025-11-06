<?php
session_start();
include("db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: index.php");
    exit();
}


if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $grade = (int)$_POST['grade'];

    $sql = "INSERT INTO students (name, email, phone, course, gender, address, grade) 
            VALUES ('$name', '$email', '$phone', '$course', '$gender', '$address', '$grade')";

    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "❌ Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>➕ Add Student</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family:'Poppins',sans-serif; background:#f8fafc; margin:0; padding:0; }
.container { max-width:600px; margin:50px auto; background:white; padding:30px; border-radius:12px; box-shadow:0 3px 15px rgba(0,0,0,0.1); }
h2 { color:#007bff; text-align:center; margin-bottom:25px; }
form label { display:block; margin-top:15px; font-weight:500; }
form input, form select, form textarea { width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:6px; }
form textarea { resize:none; height:80px; }
button { margin-top:20px; background:#28a745; color:white; border:none; padding:10px 15px; border-radius:6px; cursor:pointer; font-weight:500; }
button:hover { background:#218838; }
a.back { display:inline-block; margin-top:15px; color:#007bff; text-decoration:none; font-weight:500; }
a.back:hover { color:#0056b3; }
</style>
</head>
<body>

<div class="container">
<h2>➕ Add Student</h2>

<form method="POST">
<label>Full Name</label>
<input type="text" name="name" placeholder="Enter full name" required>

<label>Email</label>
<input type="email" name="email" placeholder="Enter email" required>

<label>Phone</label>
<input type="text" name="phone" placeholder="Enter phone number" required>

<label>Course</label>
<input type="text" name="course" placeholder="Enter course" required>

<label>Gender</label>
<select name="gender" required>
<option value="">Select Gender</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
</select>

<label>Address</label>
<textarea name="address" placeholder="Enter address" required></textarea>

<label>Grade</label>
<input type="number" name="grade" placeholder="1 - 13" min="1" max="13" required>

<button type="submit" name="submit">Add Student</button>
</form>
<a href="dashboard.php" class="back">⬅️ Back to Dashboard</a>
</div>

</body>
</html>
