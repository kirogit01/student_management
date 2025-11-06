<?php
// Database configuration
$host = "localhost";
$user = "root";
$pass = "";
$db   = "student_management";

// Create connection
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}

// Optional: Set charset to UTF-8 (prevents encoding issues)
mysqli_set_charset($conn, "utf8");

// ✅ Connection established successfully
// echo "✅ Database connected successfully!";
?>
