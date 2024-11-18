import io
import json
import logging
import os
import sys
import time

import torch
from PIL import Image
from flask import Flask, jsonify, request
from google.cloud import storage
from torchvision import transforms
from ultralytics import YOLO
import gunicorn.app.base

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s', stream=sys.stderr)
logger = logging.getLogger(__name__)


# --- Model Download ---

model_name = "yolov8n"
model_path = f"{model_name}.pt"

if not os.path.exists(model_path):
    start_time = time.time()
    logger.info(f"Downloading {model_name} model...")
    YOLO(f"{model_name}").load(model_path)
    logger.info(f"Model downloaded to {model_path}")
    end_time = time.time()
    download_time = end_time - start_time
    logger.info(f"Model download time: {download_time:.2f} seconds")

# Load the YOLOv8 model
try:
    model = YOLO(model_path)
    logger.info(f"Model loaded from {model_path}")
except Exception as e:
    logger.error(f"Error loading model: {e}")
    raise

# --- End of Model Download ---

# Preprocessing transform for the model
preprocess = transforms.Compose([
    transforms.Resize(640),              # Resize the image to 640x640.  Preserves aspect ratio and pads.
    transforms.ToTensor(),              # Convert the image to a PyTorch tensor (C, H, W)
    transforms.Lambda(lambda t: t.unsqueeze(0))
])


# Google Cloud Storage client initialization
def get_storage_client():
    """
    Initializes and returns a Google Cloud Storage client.
    """
    try:
        return storage.Client()
    except Exception as e:
        logger.error(f"Error initializing GCS client: {e}")
        raise

bucket_name = "tklis"  # Replace with your GCS bucket name

# Initialize GCS client in worker
storage_client = None  # Define storage_client globally

def initialize_worker():
    """
    Initializes resources in the worker process.
    """
    global storage_client
    storage_client = get_storage_client()
    logger.info("GCS client initialized in worker.")

# --- Prediction function ---

def predict(image_bytes):
    """
    Performs object detection on an image.
    """
    try:
        logger.info("Starting prediction")
        image = Image.open(io.BytesIO(image_bytes))
        input_tensor = preprocess(image)
        logger.info("Performing inference")
        results = model(input_tensor)

        detections = []
        for result in results:
            for r in result.boxes.data.tolist():
                x1, y1, x2, y2, confidence, class_id = r
                detections.append({
                    "bbox": [x1, y1, x2, y2],
                    "confidence": confidence,
                    "class_id": int(class_id)
                })

        logger.info("Prediction finished")
        return {"detections": detections}

    except Exception as e:
        logger.error(f"Error during prediction: {e}")
        return {"error": str(e)}

# --- Flask app ---

app = Flask(__name__)  # Create a Flask app

@app.route('/predict', methods=['POST', 'GET'])
def predict_route():
    """
    API endpoint for object detection.
    """
    global storage_client
    storage_client = get_storage_client()
    image_path = 'imagen.jpg'  # Replace with your image path
    logger.info(f"Image path: {image_path}")

    try:
        logger.info("Downloading image from GCS")
        bucket = storage_client.bucket(bucket_name)
        blob = bucket.blob(image_path)
        image_bytes = blob.download_as_bytes()
        logger.info("Image downloaded")

        prediction = predict(image_bytes)
        logger.info("Prediction received")
        return jsonify(prediction)

    except Exception as e:
        logger.exception(f"Exception caught: {e}")
        return jsonify({'error': str(e)}), 500

# --- Gunicorn application ---

class StandaloneApplication(gunicorn.app.base.BaseApplication):
    """
    A standalone application class for Gunicorn.
    """

    def __init__(self, app, options=None):
        self.options = options or {}
        self.application = app
        super().__init__()

    def load_config(self):
        config = {key: value for key, value in self.options.items()
                  if key in self.cfg.settings and value is not None}
        for key, value in config.items():
            self.cfg.set(key.lower(), value)

    def load(self):
        initialize_worker()  # Call initialize_worker here
        return self.application  # Return the Flask app instance

# --- Main block ---

if __name__ == '__main__':
    options = {
        'bind': '%s:%s' % ('0.0.0.0', '8080'),
        'workers': 2,
        'worker_class': 'uvicorn.workers.UvicornWorker',
    }
    StandaloneApplication(app).run()