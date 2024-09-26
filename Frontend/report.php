<?php
session_start();
include 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id']; 
echo "Current Session Student ID: " . $student_id . "<br>";

$sql = "SELECT 
            s.first_name, 
            s.last_name, 
            s.batch, 
            s.course, 
            a.check_in_time, 
            a.check_out_time, 
            a.attendance_date
        FROM 
            StudentAttendanceLog a
        JOIN 
            students s ON a.student_id = s.id  
        WHERE 
            s.student_id = ?"; 

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $student_id);  
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Name: " . $row['first_name'] . " " . $row['last_name'] . "<br>";
            echo "Batch: " . $row['batch'] . "<br>";
            echo "Course: " . $row['course'] . "<br>";
            echo "Check-in Time: " . $row['check_in_time'] . "<br>";
            echo "Check-out Time: " . ($row['check_out_time'] ? $row['check_out_time'] : "Not checked out") . "<br>";
            echo "Attendance Date: " . $row['attendance_date'] . "<br><br>";
        }
    } else {
        echo "No attendance records found.";
    }

    $stmt->close();
} else {
    echo "Database query error.";
}

$conn->close();
?>
