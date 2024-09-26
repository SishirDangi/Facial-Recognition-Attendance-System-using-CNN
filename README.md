# Facial Recognition Attendance System

This project is a **Facial Recognition Attendance System** built using **TensorFlow** and **OpenCV**. It uses a Convolutional Neural Network (CNN) to recognize faces and can be integrated with real-time attendance tracking systems.

## Features

- Real-time face detection and recognition using a webcam.
- Customizable CNN model trained using **TensorFlow/Keras**.
- **OpenCV** for real-time image processing.
- Data augmentation techniques for better model generalization.
- Confidence threshold to reduce false positives.
- Supports training on multiple users with image datasets.

## Project Structure

```bash
facial-recognition-attendance-system/
│
├── README.md               # Project overview and instructions
├── requirements.txt        # Required packages (TensorFlow, OpenCV, etc.)
├── data/                   # Dataset folder
│   ├── person_1/
│   ├── person_2/
│   └── ... (images of each user)
├── models/                 # Saved trained models
│   └── face_recognition_model.h5
├── src/                    # Source code
│   ├── dataset_generator.py
│   ├── model_training.py
│   ├── face_detection.py
│   └── real_time_recognition.py
└── notebooks/              # (Optional) Jupyter notebooks for experiments
    └── data_exploration.ipynb
