<?php
session_start();

include 'config.php';

if (!isset($_SESSION['student_id'])) {
    // Redirect to login if the student_id is not set
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="Dashboard.css">
</head>
<body>

<header>
    <div class="logo-section">
        <img src="photo/MeroHajiri_logo.png" alt="MeroHajiri Logo" class="logo">
        <h1>MeroHajiri</h1>
    </div>

    <div class="user-section">
        Hi, <span id="first_name">Loading...</span>
    </div>
</header>

<div class="sidebar">
    <div id="datetime">Loading date and time...</div>
    <a href="StudentDashboard.php">Dashboard</a>
    <a href="#">Attendance Sheet</a>
    <a href="report.php">Attendance Report</a>
    <a href="takeattendance.php">Take Attendance</a> 
    <a href="#" onclick="openLogoutDialog()">Logout</a>

<div id="logoutDialog" style="display: none;">
  <div class="dialog-box">
    <p>Are you sure you want to logout?</p>
    <button onclick="confirmLogout()">Yes</button>
    <button onclick="closeLogoutDialog()">No</button>
  </div>
</div>

<script>
function openLogoutDialog() {
    document.getElementById('logoutDialog').style.display = 'flex'; 
}

function closeLogoutDialog() {
    document.getElementById('logoutDialog').style.display = 'none'; 
}

function confirmLogout() {
    window.location.href = "index.php"; 
}
</script>

<style>

#logoutDialog {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); 
  display: flex;
  justify-content: center;
  align-items: center;
}

.dialog-box {
  background-color: white;
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
}

.dialog-box button {
  margin: 10px;
  padding: 10px 20px;
  cursor: pointer;
}
</style>
</div>

<div class="main-content">
    <p style="padding-left: 2em; font-size: 24px;">Student Dashboard</p>

    <div class="dashboard">
        <div class="card">
            <h3>Present</h3>
            <p>115</p>
            <p>77% increase</p>
        </div>
        <div class="card">
            <h3>Absent</h3>
            <p>15</p>
            <p>25% decrease</p>
        </div>
        <div class="card">
            <h3>Attendance (This Month)</h3>
            <p>27</p>
            <p>57% increase</p>
        </div>
    </div>
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

        fetch('get_user_info.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (typeof data === 'string') {
                    document.getElementById('first_name').innerText = data;
                } else {
                    document.getElementById('first_name').innerText = "Unknown User";
                }
            })
            .catch(error => {
                console.error('Error fetching student name:', error);
                document.getElementById('first_name').innerText = "Error fetching name";
            });
    });
</script>

</body>
</html>
