<?php
// Start session
session_start();

// Include the database configuration file
include 'config.php';  // Make sure this file contains the correct DB connection

// Fetch the student ID from the session
$student_id = $_SESSION['student_id'];

// Check if student_id exists in session
if (!isset($student_id)) {
    echo json_encode("No student ID found in session");
    exit();
}

// Fetch the student's first name from the database
$sql = "SELECT first_name FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);

// Check if the statement was prepared successfully
if (!$stmt) {
    echo json_encode("Failed to prepare statement: " . $conn->error);
    exit();
}

// Bind the student ID to the query
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row['first_name']);
} else {
    echo json_encode("Student not found");
}

// Close the database connection
$stmt->close();
$conn->close();
?>
