from flask import Flask, request, jsonify
import cv2
import numpy as np
import tensorflow as tf
from datetime import datetime
import mysql.connector
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # Enable CORS if your frontend is on a different port

# Load the pre-trained Haar Cascade face detector
face_cascade = cv2.CascadeClassifier('haarcascade_frontalface_default.xml')

# Load your trained CNN model (assuming it's in TensorFlow)
model = tf.keras.models.load_model('face_recognition_model.h5')

# Connect to MySQL database
conn = mysql.connector.connect(
    host='localhost',
    user='root',
    password='yourpassword',
    database='MeroHajiriStudentData'
)
cursor = conn.cursor()

# Helper function to recognize the face
def recognize_face(image):
    # Preprocess the image (resize, normalize) to fit the CNN model's input
    face = cv2.resize(image, (128, 128))  # Adjust based on your model's input size
    face = face / 255.0  # Normalize pixel values
    face = np.expand_dims(face, axis=0)  # Add batch dimension

    prediction = model.predict(face)
    if prediction[0][0] > 0.5:  # Adjust threshold based on model's accuracy
        return "Student Recognized"  # Return some unique student identifier
    else:
        return None

@app.route('/takeattendance', methods=['POST'])
def take_attendance():
    file = request.files['image'].read()  # Receive image from frontend
    npimg = np.frombuffer(file, np.uint8)
    image = cv2.imdecode(npimg, cv2.IMREAD_COLOR)

    # Detect faces in the image
    faces = face_cascade.detectMultiScale(image, 1.3, 5)
    if len(faces) == 0:
        return jsonify({"success": False, "message": "No face detected"})

    # Recognize face
    for (x, y, w, h) in faces:
        face_image = image[y:y+h, x:x+w]
        student_id = recognize_face(face_image)

        if student_id:
            # Check if student already checked in today
            today = datetime.now().date()
            query = ("SELECT * FROM StudentAttendanceLog WHERE student_id = %s AND attendance_date = %s")
            cursor.execute(query, (student_id, today))
            record = cursor.fetchone()

            if record:
                return jsonify({"success": False, "message": "Attendance already marked for today."})

            # Insert attendance record
            insert_query = ("INSERT INTO StudentAttendanceLog (student_id, check_in_time, attendance_date) "
                            "VALUES (%s, NOW(), %s)")
            cursor.execute(insert_query, (student_id, today))
            conn.commit()

            return jsonify({"success": True, "message": "Attendance marked successfully."})

    return jsonify({"success": False, "message": "Face not recognized"})

if __name__ == '__main__':
    app.run(debug=True)

# test code only
