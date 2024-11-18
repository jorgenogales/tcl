import io
import json
import logging
import os

import torch
from PIL import Image
from ultralytics import YOLO
from torchvision import transforms

import gunicorn.app.base
from flask import Flask, request, jsonify
from google.cloud import storage

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')   

logger = logging.getLogger(__name__)   


# --- Model Download ---

model_name = "yolov8n"
model_path = f"{model_name}.pt"

if not os.path.exists(model_path):
    logger.info(f"Downloading {model_name} model...")
    YOLO(f"{model_name}").load(model_path)
    logger.info(f"Model downloaded to {model_path}")

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
    transforms.ToTensor(),
])

# Initialize Google Cloud Storage client
storage_client = storage.Client()
bucket_name = "tklis"


def predict(image_bytes):
    """
    Performs object detection on an image.

    Args:
        image_bytes (bytes): The image data as bytes.

    Returns:
        dict: A dictionary containing the prediction results.
    """
    try:
        # Load the image using Pillow
        image = Image.open(io.BytesIO(image_bytes))

        # Preprocess the image
        input_tensor = preprocess(image)

        # Perform inference
        results = model(input_tensor)

        # Process the results
        detections = []
        for result in results:
            for r in result.boxes.data.tolist():
                x1, y1, x2, y2, confidence, class_id = r
                detections.append({
                    "bbox": [x1, y1, x2, y2],
                    "confidence": confidence,
                    "class_id": int(class_id)
                })

        return {"detections": detections}

    except Exception as e:
        logger.error(f"Error during prediction: {e}")
        return {"error": str(e)}


app = Flask(__name__)  # Create a Flask app


@app.route('/predict', methods=['POST'])
def predict_route():
    """
    API endpoint for object detection.
    """
    
    image_path = '/imagen.jpg'

    try:
        # Download the image from GCS
        bucket = storage_client.bucket(bucket_name)
        blob = bucket.blob(image_path)
        image_bytes = blob.download_as_bytes()

        prediction = predict(image_bytes)
        return jsonify(prediction)

    except Exception as e:
        logger.error(f"Error processing request: {e}")
        return jsonify({'error': str(e)}), 500


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
        return self.application


if __name__ == '__main__':

    options = {
        'bind': '%s:%s' % ('0.0.0.0', '8080'),
        'workers':
 2,
    }
    StandaloneApplication(app, options).run()