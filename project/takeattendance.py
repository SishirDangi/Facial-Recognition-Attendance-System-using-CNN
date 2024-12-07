from flask import Flask, render_template, Response, jsonify
import cv2
import mysql.connector
import numpy as np
from tensorflow.keras.models import load_model
from datetime import datetime
from config import get_db_connection

app = Flask(__name__)

db = get_db_connection()
if not db:
    exit(1)

try:
    model = load_model("models/FacedataModel.h5")
    print("Model loaded successfully")
except Exception as e:
    print(f"Error loading model: {e}")
    exit(1)

try:
    cursor = db.cursor(dictionary=True)
    cursor.execute("SELECT student_id FROM students")
    students = cursor.fetchall()
    
    label_map = {index: student['student_id'] for index, student in enumerate(students)}
    reverse_label_map = {v: k for k, v in label_map.items()}
    print("Label map generated successfully")
except mysql.connector.Error as err:
    print(f"Database error while generating label map: {err}")
    exit(1)

face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + "haarcascade_frontalface_default.xml")

camera = cv2.VideoCapture(0)  # Access the default camera
current_frame = None
attendance_complete = False

@app.route('/')
def index():
    return render_template('takeattendance.html')

@app.route('/video_feed')
def video_feed():
    global camera, attendance_complete
    if attendance_complete:
        attendance_complete = False  # Reset attendance status
    if not camera.isOpened():
        camera.open(0)  # Reopen the camera if it was closed
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

def preprocess_face(face):
    """Preprocess the face image for the model."""
    try:
        face_resized = cv2.resize(face, (64, 64))
        face_normalized = face_resized.astype("float32") / 255.0
        face_rgb = np.stack([face_normalized] * 3, axis=-1)  # Convert to 3 channels
        face_reshaped = np.expand_dims(face_rgb, axis=0)
        return face_reshaped
    except Exception as e:
        print(f"Error preprocessing face: {e}")
        return None

def generate_frames():
    global current_frame, camera, attendance_complete
    while True:
        if not camera.isOpened():
            camera.open(0)  # Ensure the camera is open

        success, frame = camera.read()
        if not success:
            break

        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=5, minSize=(64, 64))

        for (x, y, w, h) in faces:
            face_gray = gray[y:y + h, x:x + w]
            face_input = preprocess_face(face_gray)
            if face_input is None:
                continue

            predictions = model.predict(face_input)
            class_index = np.argmax(predictions)
            confidence = np.max(predictions)

            if confidence >= 0.8: 
                user_id = label_map.get(class_index, "Unknown")
                full_name = "Unknown"
                if user_id != "Unknown":
                    cursor = db.cursor(dictionary=True)
                    cursor.execute("""
                        SELECT CONCAT(first_name, ' ', last_name) AS full_name
                        FROM students WHERE student_id = %s
                    """, (user_id,))
                    student = cursor.fetchone()
                    cursor.close()

                    if student:
                        full_name = student['full_name']

                label = f"{user_id} - {full_name} ({confidence:.2f})"
            else:
                label = "Unknown (Low Confidence)"

            cv2.rectangle(frame, (x, y), (x + w, y + h), (255, 0, 0), 2)
            cv2.putText(frame, label, (x, y - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)

        current_frame = frame
        _, buffer = cv2.imencode('.jpg', frame)
        frame = buffer.tobytes()
        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')

@app.route('/take_attendance', methods=['POST'])
def take_attendance():
    global current_frame, camera, attendance_complete
    if current_frame is None:
        return jsonify({"error": "No frame available"}), 400

    gray = cv2.cvtColor(current_frame, cv2.COLOR_BGR2GRAY)
    faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=5, minSize=(64, 64))
    attendance_list = []

    for (x, y, w, h) in faces:
        face_gray = gray[y:y + h, x:x + w]
        face_input = preprocess_face(face_gray)
        if face_input is None:
            continue

        predictions = model.predict(face_input)
        class_index = np.argmax(predictions)
        confidence = np.max(predictions)

        if confidence >= 0.8:  # Adjust threshold if needed
            user_id = label_map.get(class_index, "Unknown")
            if record_attendance(user_id):
                attendance_list.append(user_id)

    camera.release()
    attendance_complete = True

    if attendance_list:
        return jsonify({"status": "success", "message": "Attendance recorded successfully!"})
    else:
        return jsonify({"status": "error", "message": "No faces recognized"})

def record_attendance(user_id):
    """Record attendance in the 'studentattendance' table whenever a face is recognized."""
    current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    cursor = db.cursor(dictionary=True)
    
    try:
        cursor.execute("""
            SELECT CONCAT(first_name, ' ', last_name) AS full_name, course, batch 
            FROM students WHERE student_id = %s
        """, (user_id,))
        student = cursor.fetchone()

        if student:
            cursor.execute("""
                INSERT INTO studentattendance (student_id, full_name, course, batch, attendance_time)
                VALUES (%s, %s, %s, %s, %s)
            """, (user_id, student['full_name'], student['course'], student['batch'], current_time))
            db.commit()
            return True
        else:
            return False
    except mysql.connector.Error as err:
        print(f"Database error: {err}")
        return False
    finally:
        cursor.close()

if __name__ == "__main__":
    app.run(debug=True)
