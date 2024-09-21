<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];
    $profile = $_POST['profile']; // Student or Admin

    if ($profile == 'student') {
        // Query for students
        $sql = "SELECT * FROM students WHERE student_id = '$user_id'";
    } else if ($profile == 'admin') {
        // Query for admins
        $sql = "SELECT * FROM admins WHERE admin_id = '$user_id'";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Successful login
            echo "Login successful";

            // Redirect to the appropriate dashboard
            if ($profile == 'student') {
                header("Location: StudentDashboard.html");
            } else if ($profile == 'admin') {
                header("Location: AdminDashboard.html");
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No account found with this ID.";
    }

    $conn->close();
}
?>
