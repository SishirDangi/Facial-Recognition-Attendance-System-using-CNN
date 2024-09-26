<?php
session_start();
include 'config.php';
$student_id = $_SESSION['student_id'];
if (!isset($student_id)) {
    echo json_encode("No student ID found in session");
    exit();
}

$sql = "SELECT first_name FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode("Failed to prepare statement: " . $conn->error);
    exit();
}

$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row['first_name']);
} else {
    echo json_encode("Student not found");
}
$stmt->close();
$conn->close();
?>
