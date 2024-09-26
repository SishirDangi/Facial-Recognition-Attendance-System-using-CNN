<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facial Recognition Attendance System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo-section">
            <img src="photo/MeroHajiri_logo.png" alt="MeroHajiri Logo" class="logo"> 
        </div>
    </header>

    <section class="main-content">
        <div class="title-section">
            <h2>Facial Recognition</h2>
            <h3><span class="highlight-blue">Based Attendance</span> <span class="highlight-red">System Using CNN</span></h3>
            <p class="about-text">MeroHajiri is a platform for student attendance system where students can do their attendance using their face.</p>
        </div>

        <div class="account-section">
            <form class="create-account-form" action="register.php" method="POST"> 
                <h3>CREATE ACCOUNT FOR STUDENT</h3>
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="text" name="batch" placeholder="Batch" required>
              
                <div class="course">
                    <select id="course" name="course" required>
                        <option value="" disabled selected>Select your course</option>
                        <option value="Bsc.CSIT">Bsc.CSIT</option>
                        <option value="BCA">BCA</option>
                        <option value="BBA">BBA</option>
                        <option value="BBS">BBS</option>
                        <option value="BIM">BIM</option>
                        <option value="BIT">BIT</option>
                    </select>
                </div>
                
                <input type="text" name="student_id" placeholder="Student ID" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">CREATE ACCOUNT</button>
            </form>
            <br>
            <p>Already registered?</p>
            <div class="login-btn">
                <button type="button" class="login-button" onclick="window.location.href='login.php';">Login</button>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; MeroHajiri 2024. All rights reserved.</p>
    </footer>
</body>
</html>
