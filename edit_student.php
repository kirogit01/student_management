<?php
session_start();
include("db.php");

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = (int)$_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM students WHERE id=$id");
if (!$result || mysqli_num_rows($result) == 0) {
    echo "❌ Student not found!";
    exit();
}

$student = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $grade = (int)$_POST['grade'];

    $sql = "UPDATE students SET 
                name='$name',
                email='$email',
                phone='$phone',
                course='$course',
                gender='$gender',
                address='$address',
                grade='$grade'
            WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "❌ Error updating student: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>✏️ Edit Student</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family:'Poppins',sans-serif; background:#f8fafc; margin:0; padding:0; }
.container { max-width:600px; margin:50px auto; background:white; padding:30px; border-radius:12px; box-shadow:0 3px 15px rgba(0,0,0,0.1); }
h2 { color:#007bff; text-align:center; margin-bottom:25px; }
form label { display:block; margin-top:15px; font-weight:500; }
form input, form select, form textarea { width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:6px; }
form textarea { resize:none; height:80px; }
button { margin-top:20px; background:#ffc107; color:black; border:none; padding:10px 15px; border-radius:6px; cursor:pointer; font-weight:500; }
button:hover { background:#e0a800; }
a.back { display:inline-block; margin-top:15px; color:#007bff; text-decoration:none; font-weight:500; }
a.back:hover { color:#0056b3; }
</style>
</head>
<body>

<div class="container">
<h2>✏️ Edit Student</h2>

<form method="POST">
<label>Full Name</label>
<input type="text" name="name" value="<?= htmlspecialchars($student['name']); ?>" required>

<label>Email</label>
<input type="email" name="email" value="<?= htmlspecialchars($student['email']); ?>" required>

<label>Phone</label>
<input type="text" name="phone" value="<?= htmlspecialchars($student['phone']); ?>" required>

<label>Course</label>
<input type="text" name="course" value="<?= htmlspecialchars($student['course']); ?>" required>

<label>Gender</label>
<select name="gender" required>
<option value="Male" <?= $student['gender']=='Male'?'selected':'' ?>>Male</option>
<option value="Female" <?= $student['gender']=='Female'?'selected':'' ?>>Female</option>
</select>

<label>Address</label>
<textarea name="address" required><?= htmlspecialchars($student['address']); ?></textarea>

<label>Grade</label>
<input type="number" name="grade" min="1" max="13" value="<?= $student['grade']; ?>" required>

<button type="submit" name="update">Update Student</button>
</form>
<a href="dashboard.php" class="back">⬅️ Back to Dashboard</a>
</div>

</body>
</html>
