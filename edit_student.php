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
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $grade = mysqli_real_escape_string($conn, $_POST['grade']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);

    mysqli_query($conn, "UPDATE teachers SET 
        username='$username', 
        name='$name', 
        email='$email', 
        phone_number='$phone_number', 
        grade='$grade', 
        subject='$subject' 
        WHERE id='$id'");
    header("Location: teachers.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Teacher</title>
<style>
/* Your existing styles */
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
