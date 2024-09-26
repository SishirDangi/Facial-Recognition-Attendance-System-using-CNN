<?php
session_start();
include 'config.php';

$student_id = $_SESSION['student_id'];
$action = $_POST['action'];

if ($action === 'checkin') {
    $sql = "INSERT INTO StudentAttendanceLog (student_id, check_in_time, attendance_date) 
            VALUES ((SELECT id FROM students WHERE student_id = ?), NOW(), CURDATE())";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $student_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Check-in successful']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to check in']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
}

if ($action === 'checkout') {
    $sql = "UPDATE StudentAttendanceLog 
            SET check_out_time = NOW() 
            WHERE student_id = (SELECT id FROM students WHERE student_id = ?) 
            AND attendance_date = CURDATE() 
            AND check_out_time IS NULL";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $student_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Check-out successful']);
            } else {
                echo json_encode(['success' => false, 'error' => 'No check-in found for today']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to check out']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
}
$conn->close();
?>
