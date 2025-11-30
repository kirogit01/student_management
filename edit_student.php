<?php
session_start();
include("db.php");

// Check admin login
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: index.php");
    exit();
}

// Get student ID
$id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch student details
$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE id='$id'"));

// Update student data
if (isset($_POST['submit'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone']);
    $grade = mysqli_real_escape_string($conn, $_POST['grade']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Update query (fixed)
    $query = "UPDATE students SET 
                name='$name',
                email='$email',
                phone='$phone_number',
                grade='$grade',
                address='$address'
              WHERE id='$id'";

    mysqli_query($conn, $query);

    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Student</title>
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
<h2>Edit Student Details</h2>

<form method="POST">

<label>Name:</label>
<input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>

<label>Email:</label>
<input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>

<label>Phone Number:</label>
<input type="text" name="phone" value="<?= htmlspecialchars($student['phone']) ?>" required>

<label>Grade:</label>
<input type="text" name="grade" value="<?= htmlspecialchars($student['grade']) ?>" required>

<label>Address:</label>
<input type="text" name="address" value="<?= htmlspecialchars($student['address']) ?>" required>

<button type="submit" name="submit">Update Student</button>
</form>

<a href="dashboard.php" class="back">⬅️ Back to Admin</a>

</div>
</body>
</html>
