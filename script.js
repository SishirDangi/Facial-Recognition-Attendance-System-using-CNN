function login() {
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    if (username === 'admin' && password === 'admin123') {
        document.getElementById('login-page').classList.add('hidden');
        document.getElementById('dashboard').classList.remove('hidden');
        document.getElementById('user-name').textContent = username;
    } else {
        alert('Invalid credentials, please try again.');
    }
}

function logout() {
    document.getElementById('login-page').classList.remove('hidden');
    document.getElementById('dashboard').classList.add('hidden');
}

// Example to add an attendance log entry
var attendanceLog = document.getElementById('attendance-log');
function addAttendanceLog(name) {
    var logItem = document.createElement('li');
    logItem.textContent = name + " - Present";
    attendanceLog.appendChild(logItem);
}

// Simulate attendance marking
setTimeout(function() {
    addAttendanceLog("John Doe");
}, 3000);
