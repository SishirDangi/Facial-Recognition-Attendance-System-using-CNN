<?php
session_start(); // Start the session
include 'config.php'; // Include the database configuration

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];
    $profile = $_POST['profile']; // Student or Admin

    // Prepare query based on user profile
    if ($profile == 'student') {
        $sql = "SELECT * FROM students WHERE student_id = ?";
    } else if ($profile == 'admin') {
        $sql = "SELECT * FROM admins WHERE admin_id = ?";
    }

    // Prepare statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Successful login
            $_SESSION['student_id'] = $row['student_id']; // Use the correct column name for student_id
            $_SESSION['profile'] = $profile;

            // Redirect to the appropriate dashboard
            if ($profile == 'student') {
                header("Location: Student/StudentDashboard.php");
            } else if ($profile == 'admin') {
                header("Location: Admin/AdminDashboard.php");
            }
            exit(); // End script after redirection
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No account found with this ID.";
    }

    // Close the connection
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
            <p>Please Enter Your ID and Password</p> 
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
</body>
</html>
