from flask import Flask, render_template, request, jsonify, Response
import cv2
import os
import numpy as np
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Conv2D, MaxPooling2D, Flatten, Dense, Dropout, BatchNormalization
from tensorflow.keras.optimizers import Adam
from tensorflow.keras.utils import to_categorical
from sklearn.model_selection import train_test_split
import pickle
from threading import Thread

app = Flask(__name__)

DATASET_PATH = "dataset"
MODEL_PATH = "models/FacedataModel.h5"
LABEL_MAP_PATH = "models/label_map.pkl"

os.makedirs(DATASET_PATH, exist_ok=True)
os.makedirs("models", exist_ok=True)
training_progress = {"progress": 0}

def create_cnn_model(input_shape, num_classes):
    model = Sequential()
    model.add(Conv2D(32, (3, 3), activation='relu', input_shape=input_shape))
    model.add(BatchNormalization())
    model.add(MaxPooling2D(pool_size=(2, 2)))
    model.add(Conv2D(64, (3, 3), activation='relu'))
    model.add(BatchNormalization())
    model.add(MaxPooling2D(pool_size=(2, 2)))
    model.add(Conv2D(128, (3, 3), activation='relu'))
    model.add(BatchNormalization())
    model.add(MaxPooling2D(pool_size=(2, 2)))
    model.add(Flatten())
    model.add(Dense(256, activation='relu'))
    model.add(Dropout(0.5))
    model.add(Dense(num_classes, activation='softmax'))
    return model

@app.route('/')
def index():
    return render_template('AddFaceDataset.html')

@app.route('/capture', methods=['POST'])
def capture_images():
    student_id = request.form['student_id']
    full_name = request.form['full_name']
    user_folder = os.path.join(DATASET_PATH, f"{student_id}_{full_name.replace(' ', '_')}")
    os.makedirs(user_folder, exist_ok=True)
    cap = cv2.VideoCapture(0, cv2.CAP_DSHOW) 

    if not cap.isOpened():
        return jsonify({"status": "error", "message": "Unable to access webcam."})

    face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')

    count = 1
    max_images = 500

    while count <= max_images:
        ret, frame = cap.read()
        if not ret:
            break

        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=5)

        for (x, y, w, h) in faces:
            face = gray[y:y + h, x:x + w]
            face = cv2.resize(face, (128, 128))
            filename = f"{student_id}_{full_name.replace(' ', '_')}_{count}.jpg"
            cv2.imwrite(os.path.join(user_folder, filename), face)
            count += 1

            text = f"{full_name} (ID: {student_id}) - {count}/{max_images}"
            cv2.putText(frame, text, (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 0.7, (0, 255, 0), 2)
            cv2.rectangle(frame, (x, y), (x + w, y + h), (255, 0, 0), 2)

        cv2.imshow("Capturing Images", frame)
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()

    if count > 1:
        return jsonify({"status": "success", "message": f"Captured {count - 1} images for {full_name}."})
    else:
        return jsonify({"status": "error", "message": "No images captured."})

def train_cnn_model():
    global training_progress
    training_progress["progress"] = 0

    images = []
    labels = []
    label_map = {}
    class_index = 0

    for user_folder in os.listdir(DATASET_PATH):
        folder_path = os.path.join(DATASET_PATH, user_folder)
        if os.path.isdir(folder_path):
            label_map[user_folder] = class_index
            class_index += 1

            for image_name in os.listdir(folder_path):
                image_path = os.path.join(folder_path, image_name)
                image = cv2.imread(image_path)
                if image is not None:
                    image = cv2.resize(image, (64, 64))
                    images.append(image)
                    labels.append(label_map[user_folder])

    if len(images) == 0:
        return False

    X = np.array(images).astype('float32') / 255.0
    y = to_categorical(np.array(labels))

    X_train, X_val, y_train, y_val = train_test_split(X, y, test_size=0.2, random_state=42)

    model = create_cnn_model((64, 64, 3), len(label_map))
    model.compile(optimizer=Adam(learning_rate=0.001), loss='categorical_crossentropy', metrics=['accuracy'])

    epochs = 30
    for epoch in range(epochs):
        model.fit(X_train, y_train, validation_data=(X_val, y_val), epochs=1, batch_size=32, verbose=1)
        training_progress["progress"] = int(((epoch + 1) / epochs) * 100)

    model.save(MODEL_PATH)
    with open(LABEL_MAP_PATH, 'wb') as f:
        pickle.dump(label_map, f)

    training_progress["progress"] = 100
    return True

@app.route('/train', methods=['POST'])
def train():
    thread = Thread(target=train_cnn_model)
    thread.start()
    return jsonify({"status": "success", "message": "Training started."})

@app.route('/progress', methods=['GET'])
def get_progress():
    return jsonify(training_progress)

if __name__ == "__main__":
    app.run(debug=True)
