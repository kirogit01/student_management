<?php
session_start();
include("db.php");

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); 
    $role = $_POST['role'];

    if ($role == "admin") {
        $sql = "SELECT * FROM admins WHERE username='$username' AND password='$password'";
    } elseif ($role == "teacher") {
        $sql = "SELECT * FROM teachers WHERE username='$username' AND password='$password'";
    } else {
        $error = "❌ Invalid role selected!";
    }

    if (isset($sql)) {
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['role'] = $role;
            $_SESSION['user'] = $user['username'];
            header($role == "admin" ? "Location: dashboard.php" : "Location: teacher_dashboard.php");
            exit;
        } else {
            $error = "❌ Wrong Username or Password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Student Management System</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
html,body{height:100%;}

/* Full-page background image */
body {
    background: url('images.png') no-repeat center center fixed;
    background-size: 1000px 800px;
    display:flex;
    justify-content:center;
    align-items:center;
    flex-direction:column;
}

/* Optional dark overlay for better contrast */
.overlay {
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background: rgba(0,0,0,0.4);
    z-index:0;
}

.container {
    position: relative;
    z-index:1;
    background: rgba(255,255,255,0.95);
    padding:40px;
    border-radius:15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    text-align:center;
    max-width:400px;
    width:90%;
}

.header h1{
    font-size:28px;
    color:#007bff;
    margin-bottom:8px;
}

.header h2{
    font-size:20px;
    color:#555;
    margin-bottom:20px;
}

.container input, .container select{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:1px solid #ccc;
    border-radius:8px;
}

.container button{
    width:100%;
    padding:12px;
    background:#007bff;
    color:white;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
    transition: 0.3s;
}

.container button:hover{
    background:#0056b3;
}

.error{
    margin-top:15px;
    color:#ff4d4f;
    font-weight:bold;
}
</style>
</head>
<body>

<div class="overlay"></div>

<div class="container">
    <div class="header">
        <h1>Welcome to our Students Management System</h1>
        <h2>BT/PD/Munaitivu Sakthi M.V</h2>
    </div>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin">Admin</option>
            <option value="teacher">Teacher</option>
        </select>
        <button type="submit" name="login">Login</button>
    </form>

    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
</div>

</body>
</html>
