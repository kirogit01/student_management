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
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

body {
    height: 100vh;
    background: url('school.png') no-repeat center center/cover;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

/* Dark overlay */
body::before {
    content: "";
    position: absolute;
    height: 100%;
    width: 100%;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(3px);
}

/* Modern glass card */
.container {
    position: relative;
    z-index: 10;
    width: 380px;
    padding: 40px;
    border-radius: 18px;
    background: rgba(255,255,255,0.17);
    box-shadow: 0 8px 25px rgba(0,0,0,0.4);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.25);
    animation: fadeIn 0.7s ease-in-out;
}

/* Animations */
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}

h1 {
    text-align: center;
    color: #fff;
    font-size: 28px;
    margin-bottom: 5px;
}

h2 {
    text-align: center;
    color: #dcdcdc;
    font-size: 18px;
    margin-bottom: 25px;
}

input, select {
    width: 100%;
    padding: 12px;
    margin: 12px 0;
    border: none;
    border-radius: 10px;
    outline: none;
    font-size: 15px;
}

/* Input focus */
input:focus, select:focus {
    border: 2px solid #4da3ff;
    background: rgba(255,255,255,0.8);
}

/* Login button */
button {
    width: 100%;
    padding: 14px;
    margin-top: 10px;
    border: none;
    background: #007bff;
    border-radius: 10px;
    color: white;
    font-size: 17px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #0056b3;
}

/* Error message */
.error {
    margin-top: 15px;
    color: #ff4d4f;
    text-align: center;
    font-weight: bold;
}
</style>
</head>

<body>

<div class="container">
    <h1>Student Management System</h1>
    <h2>Login to your account</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="teacher">Teacher</option>
        </select>

        <button name="login">Login</button>
    </form>

    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

</div>

</body>
</html>
