<?php
session_start(); 
include 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = htmlspecialchars(trim($_POST['user_id']));
    $password = trim($_POST['password']);
    $profile = htmlspecialchars(trim($_POST['profile']));
    if ($profile == 'student') {
        $sql = "SELECT * FROM students WHERE student_id = ?";
    } else if ($profile == 'admin') {
        $sql = "SELECT * FROM admins WHERE admin_id = ?";
    } else {
        die("Invalid profile selected.");
    }
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            if ($profile == 'student') {
                $_SESSION['student_id'] = $row['student_id'];
                $_SESSION['student_name'] = $row['first_name']; // Store student name in session
                $_SESSION['profile'] = 'student';
                header("Location: StudentDashboard.php");
                exit();
            } else if ($profile == 'admin') {
                $_SESSION['admin_id'] = $row['admin_id'];
                $_SESSION['admin_name'] = $row['first_name']; // Store admin name in session
                $_SESSION['profile'] = 'admin';
                header("Location: AdminDashboard.php");
                exit();
            }
        } else {
            echo "<script>alert('Invalid password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('No account found with the given ID. Please check your ID and try again.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MeroHajiri Login</title>
    <link rel="stylesheet" href="login.css"> 
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <header>
        <div class="logo-section">
            <img src="photo/MeroHajiri_logo.png" alt="MeroHajiri Logo" class="logo">
            <h1>MeroHajiri</h1>
        </div>
    </header>

    <div class="container">
        <div class="login-container">
            <h2 class="login">Login</h2>
            <p>Please enter your ID and password to access the system.</p>
            <form action="login.php" method="POST">
                <input type="text" name="user_id" placeholder="ID" required>
                <input type="password" name="password" placeholder="Password" required>
                
                <p class="profile_l">Please select your profile</p>
                
                <div class="profile-selection">
                    <label>
                        <input type="radio" name="profile" value="student" required> Student
                    </label>
                    <label>
                        <input type="radio" name="profile" value="admin" required> Admin
                    </label>
                </div>
                <button type="submit">LOGIN</button>
            </form>
        </div>
    </div>
    
    <footer>
        <p>&copy; MeroHajiri 2024. All rights reserved.</p>
    </footer>
    <script>
        function showAlert(message) {
            alert(message); 
        }
    </script>
</body>
</html>
