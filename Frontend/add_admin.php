<?php
include 'config.php';
// Admin details
$first_name = "Ram";
$last_name = "Khadka";
$admin_id = "2118166";
$phone = "9841185321";
$email = "ram@gmail.com";
$password = "ram2242";
// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
// SQL query to insert admin details
$sql = "INSERT INTO admins (first_name, last_name, admin_id, phone, email, password) 
        VALUES ('$first_name', '$last_name', '$admin_id', '$phone', '$email', '$hashed_password')";
if ($conn->query($sql) === TRUE) {
    echo "New admin added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
?>
