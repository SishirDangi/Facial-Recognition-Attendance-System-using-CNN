<?php
session_start();
include 'config.php';

// Redirect to login if the student is not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Check if attendance was already taken today
$sql = "SELECT * FROM StudentAttendanceLog WHERE student_id = ? AND attendance_date = CURDATE()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<p>Attendance has already been taken today.</p>";
    exit();
}

// Call Python script for face recognition
$output = shell_exec("python3 takeattendance.py $student_id");

// If the Python script returns success, log the attendance in the database
if (trim($output) === "success") {
    $sql = "INSERT INTO StudentAttendanceLog (student_id, check_in_time, attendance_date) VALUES (?, NOW(), CURDATE())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    
    echo "<p>Attendance recorded successfully!</p>";
} else {
    echo "<p>Face not recognized. Please try again.</p>";
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance</title>
</head>
<body>
    <p id="attendance-status">Loading...</p>
    <script>
        document.getElementById('attendance-status').innerText = '<?php echo $output === "success" ? "Attendance successful!" : "Face not recognized"; ?>';
    </script>
</body>
</html>
