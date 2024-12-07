import csv
from flask import Flask, render_template, request, redirect, url_for, flash, session, send_file, Response
import mysql.connector
from werkzeug.security import generate_password_hash, check_password_hash
from config import get_db_connection
from datetime import date
import os

app = Flask(__name__)
app.secret_key = 'sss43' 

# Home Route
@app.route('/')
def index():
    return render_template('Login/index.html')

# Login Route
@app.route('/login', methods=['GET', 'POST'])
def login():
    error = None
    if request.method == 'POST':
        user_id = request.form['user_id'].strip()
        password = request.form['password']
        profile = request.form['profile']

        conn = get_db_connection()
        if not conn:
            return "Database connection failed", 500

        cursor = conn.cursor(dictionary=True)
        table = 'students' if profile == 'student' else 'admins'
        query = f"SELECT * FROM {table} WHERE student_id = %s" if profile == 'student' else f"SELECT * FROM {table} WHERE admin_id = %s"

        cursor.execute(query, (user_id,))
        account = cursor.fetchone()

        if account:
            if check_password_hash(account['password'], password):
                if profile == 'student':
                    session['student_id'] = account['student_id']
                    session['student_name'] = account['first_name']
                    session['profile'] = 'student'
                    return redirect(url_for('student_dashboard'))
                elif profile == 'admin':
                    session['admin_id'] = account['admin_id']
                    session['admin_name'] = account['first_name']
                    session['profile'] = 'admin'
                    return redirect(url_for('admin_dashboard'))
            else:
                error = "Invalid password. Please try again."
        else:
            error = "No account found with the given ID. Please check your ID and try again."

        cursor.close()
        conn.close()

    return render_template('Login/login.html', error=error)

# Register Route
@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        first_name = request.form['first_name']
        last_name = request.form['last_name']
        batch = request.form['batch']
        course = request.form['course']
        student_id = request.form['student_id']
        phone = request.form['phone']
        email = request.form['email']
        password = request.form['password']
        confirm_password = request.form['confirm_password']

        if password != confirm_password:
            flash("Passwords do not match.", "error")
            return redirect(url_for('register'))

        hashed_password = generate_password_hash(password)

        conn = get_db_connection()
        if not conn:
            flash("Database connection failed.", "error")
            return redirect(url_for('register'))

        cursor = conn.cursor()

        try:
            cursor.execute("SELECT * FROM students WHERE student_id = %s OR email = %s", (student_id, email))
            existing_account = cursor.fetchone()

            if existing_account:
                flash("Student ID or Email already exists.", "error")
                return redirect(url_for('register'))

            query = """
            INSERT INTO students (first_name, last_name, batch, course, student_id, phone, email, password)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(query, (first_name, last_name, batch, course, student_id, phone, email, hashed_password))
            conn.commit()
            flash("Account created successfully!", "success")

        except mysql.connector.Error as err:
            flash(f"An error occurred: {err}", "error")
        finally:
            cursor.close()
            conn.close()

        return redirect(url_for('login'))

    return render_template('Login/register.html')

# Dashboard Route
@app.route('/student_dashboard')
def student_dashboard():
    if 'student_id' not in session:
        return redirect(url_for('login'))

    conn = get_db_connection()
    if not conn:
        return "Database connection failed", 500

    cursor = conn.cursor(dictionary=True)

    try:
        student_id = session['student_id']
        cursor.execute("SELECT first_name FROM students WHERE student_id = %s", (student_id,))
        student = cursor.fetchone()

        if not student:
            return "Student not found", 404

        today = date.today()
        today_str = today.strftime('%Y-%m-%d')

        query = """
            SELECT 
                COUNT(DISTINCT DATE(attendance_time)) AS present_days
            FROM studentattendance
            WHERE student_id = %s;
        """
        cursor.execute(query, (student_id,))
        result = cursor.fetchone()

        present_days = result['present_days'] if result else 0

        return render_template(
            'Dashboard/StudentDashboard.html',
            first_name=student['first_name'],
            present_days=present_days
        )
    finally:
        cursor.close()
        conn.close()

# AttendanceSheet Route
@app.route('/attendance', methods=['GET', 'POST'])
def student_attendance_sheet():
    if 'student_id' not in session:
        return redirect(url_for('login'))

    student_id = session['student_id']

    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    cursor.execute('SELECT * FROM studentattendance WHERE student_id = %s', (student_id,))
    attendance_data = cursor.fetchall()

    cursor.close()
    conn.close()

    if request.method == 'POST' and 'download_csv' in request.form:
        directory = 'static/studentReport/'
        if not os.path.exists(directory):
            os.makedirs(directory)  

        file_path = os.path.join(directory, f'attendance_{student_id}.csv')

        with open(file_path, 'w', newline='') as csvfile:
            csvwriter = csv.writer(csvfile)
            csvwriter.writerow(['Serial No.', 'Full Name', 'Batch', 'Course', 'Date', 'Time'])
            for i, record in enumerate(attendance_data, start=1):
                date = record['attendance_time'].strftime('%Y-%m-%d') if record['attendance_time'] else 'N/A'
                time = record['attendance_time'].strftime('%H:%M:%S') if record['attendance_time'] else 'N/A'
                csvwriter.writerow([i, record['full_name'], record['batch'], record['course'], date, time])

        flash('Attendance sheet has been downloaded successfully!', 'success')
        return redirect(url_for('student_attendance_sheet'))

    return render_template('StudentAttendanceSheet.html', attendance_records=attendance_data)

# Admin Route
@app.route('/admin_dashboard')
def admin_dashboard():
    if 'admin_id' not in session:
        return redirect(url_for('login'))

    admin_name = session.get('admin_name', 'Unknown User')
    return render_template('Dashboard/AdminDashboard.html', admin_name=admin_name)

@app.route('/admin_attendance')
def student_attendance_sheet_all():
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    
    cursor.execute('SELECT * FROM studentattendance')
    attendance_data = cursor.fetchall() 
    
    cursor.close()
    conn.close()
    
    return render_template('StudentAttendanceSheetAll.html', attendance_records=attendance_data)

# Mydetails Route
@app.route('/my_details')
def my_details():
    if 'student_id' not in session:
        return redirect(url_for('login'))

    conn = get_db_connection()
    if not conn:
        return "Database connection failed", 500

    cursor = conn.cursor(dictionary=True)

    try:
        student_id = session['student_id']
        cursor.execute(""" 
            SELECT student_id, first_name, last_name, batch, course, phone, email
            FROM students WHERE student_id = %s
        """, (student_id,))
        student = cursor.fetchone()

        if not student:
            return "Student not found", 404

        return render_template('Dashboard/My_Details.html', student=student)
    finally:
        cursor.close()
        conn.close()

@app.route('/report', methods=['GET'])
def generate_report():
    
    course = request.args.get('course')
    batch = request.args.get('batch')
    report_date = request.args.get('date')

    if not course or not batch:
        return render_template('student_report.html', records=None, error="Course and batch are required.")

    query = """
        SELECT student_id, full_name, course, batch, attendance_time
        FROM studentattendance
        WHERE course = %s AND batch = %s
    """
    params = [course, batch]

    if report_date:
        query += " AND DATE(attendance_time) = %s"
        params.append(report_date)

    connection = get_db_connection()
    records = []
    error = None
    if connection:
        try:
            cursor = connection.cursor(dictionary=True)
            cursor.execute(query, params)
            records = cursor.fetchall()
        except Exception as e:
            error = f"Error fetching data: {e}"
        finally:
            cursor.close()
            connection.close()
    else:
        error = "Database connection failed."

    if records:
        if report_date:
            filename_date = report_date
        else:
            filename_date = date.today().strftime('%Y-%m-%d')

        file_path = f'static/reports/student_attendance_report_{filename_date}.csv'

        os.makedirs(os.path.dirname(file_path), exist_ok=True)

        with open(file_path, mode='w', newline='', encoding='utf-8') as file:
            writer = csv.DictWriter(file, fieldnames=['student_id', 'full_name', 'course', 'batch', 'attendance_time'])
            writer.writeheader()
            writer.writerows(records)

        return render_template('student_report.html', records=records, error=error, download_link=file_path)
    else:
        return render_template('student_report.html', records=None, error="No records found for the selected details.")

# Managestudents Route
@app.route('/manage_students', methods=['GET'])
def manage_students():
    if 'admin_id' not in session:
        return redirect(url_for('login'))

    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    delete_id = request.args.get('delete_id')
    if delete_id:
        try:
            cursor.execute("DELETE FROM students WHERE student_id = %s", (delete_id,))
            conn.commit()
            flash('Student deleted successfully!', 'success')
        except Exception as e:
            flash(f'Error deleting student: {str(e)}', 'danger')

        cursor.close()
        conn.close()
        return redirect(url_for('manage_students'))

    search_query = request.args.get('search_query', '').strip()
    if search_query:
        query = """
            SELECT * FROM students
            WHERE first_name LIKE %s OR last_name LIKE %s OR batch LIKE %s OR course LIKE %s
        """
        search_param = f"%{search_query}%"
        cursor.execute(query, (search_param, search_param, search_param, search_param))
    else:
        cursor.execute("SELECT * FROM students")

    students = cursor.fetchall()

    cursor.close()
    conn.close()

    return render_template('ManageStudents.html', students=students, search_query=search_query)

# Logout
@app.route('/logout')
def logout():
    session.clear()
    return redirect(url_for('index'))

if __name__ == '__main__':
    app.run(debug=True)
