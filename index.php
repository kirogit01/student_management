<?php
session_start();
include("db.php");

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // encrypt password same as DB
    $role = $_POST['role'];

    // Validate role
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

            // ✅ Save session
            $_SESSION['role'] = $role;
            $_SESSION['user'] = $user['username']; // FIXED
           
            // ✅ Redirect based on role
            if ($role == "admin") {
                header("Location: dashboard.php");
                exit;
            } else {
                header("Location: teacher_dashboard.php");
                exit;
            }
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
<title>Login - Admin & Teacher</title>
<style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
    body{background:#f0f2f5;display:flex;justify-content:center;align-items:center;height:100vh;}
    .login-box{background:white;padding:40px;width:400px;border-radius:12px;box-shadow:0 8px 18px rgba(0,0,0,0.1);text-align:center;}
    .login-box h2{margin-bottom:25px;}
    .login-box input,select{width:100%;padding:12px;margin:10px 0;border:1px solid #ccc;border-radius:8px;}
    .login-box button{width:100%;padding:12px;background:#007bff;color:white;border:none;border-radius:8px;font-size:16px;cursor:pointer;}
    .login-box button:hover{background:#0056b3;}
    .error{margin-top:15px;color:#ff4d4f;font-weight:bold;}
</style>
</head>
<body>

<div class="login-box">
    <h2>Login</h2>
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
