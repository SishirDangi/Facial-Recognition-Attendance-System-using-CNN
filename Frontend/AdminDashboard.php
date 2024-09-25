<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeroHajiri Dashboard</title>
    <link rel="stylesheet" href="AdminDashboard.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <h2>MeroHajiri</h2>
            </div>
            <ul class="menu">
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Attendance Sheet</a></li>
                <li><a href="#">Attendance Report</a></li>
                <li><a href="#">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <header>
                <div class="date-time">
                    <!-- Add current date and time here using JavaScript -->
                    <span>Show current date and time</span>
                </div>
                <div class="user">
                    <span>Hi, username</span>
                </div>
            </header>
            <section class="dashboard">
                <h1>Admin Dashboard</h1>
                <p>Showing data of all students</p>
                <div class="stats">
                    <div class="card">
                        <h2>Present | Today</h2>
                        <h3>115</h3>
                        <p>75% increase</p>
                    </div>
                    <div class="card">
                        <h2>Absent | Today</h2>
                        <h3>15</h3>
                        <p>25% decrease</p>
                    </div>
                    <div class="card">
                        <h2>Attendance | This Month</h2>
                        <h3>27</h3>
                        <p>37% increase</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
