<?php
// Database configuration file (config.php)
$servername = "localhost";
$username = "root";
$password = "";  // Change this to your MySQL password
$dbname = "MeroHajiriStudentData";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
