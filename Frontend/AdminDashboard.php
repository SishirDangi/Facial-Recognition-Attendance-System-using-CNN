<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';
$adminName = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : "Unknown User";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="Dashboard.css"> <!-- Ensure this CSS file is available -->
</head>
<body>
    <header>
        <div class="logo-section">
            <img src="photo/MeroHajiri_logo.png" alt="MeroHajiri Logo" class="logo">
            <h1>MeroHajiri</h1>
        </div>
        <div class="user-section">
            Hi, <span id="first_name"><?php echo $adminName; ?></span> <!-- Display admin name here -->
        </div>
    </header>

    <div class="sidebar">
        <div id="datetime">Loading date and time...</div>
        <a href="AdminDashboard.php">Dashboard</a>
        <a href="#">Attendance Sheet</a>
        <a href="report">Attendance Report</a>
        <a href="#">Mangae Student</a>
        <a href="#" onclick="openLogoutDialog()">Logout</a>
    </div>

    <div class="main-content">
        <h2>&nbsp;&nbsp;&nbsp;Welcome to the Admin Dashboard</h2>
        <!-- Admin specific content goes here -->
    </div>

    <script>
        function updateDateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            };
            document.getElementById('datetime').innerHTML = now.toLocaleDateString('en-US', options);
        }

        document.addEventListener("DOMContentLoaded", function () {
            setInterval(updateDateTime, 1000);
        });
    </script>

</body>
</html>
