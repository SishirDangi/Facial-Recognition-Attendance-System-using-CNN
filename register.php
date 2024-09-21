<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $batch = $_POST['batch'];
    $course = $_POST['course'];
    $student_id = $_POST['student_id'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into database
    $sql = "INSERT INTO students (first_name, last_name, batch, course, student_id, phone, email, password) 
            VALUES ('$first_name', '$last_name', '$batch', '$course', '$student_id', '$phone', '$email', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        echo "Account created successfully";
        header("Location: login.html");  // Redirect to login page
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
