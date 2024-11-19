FROM pytorch/pytorch:latest

WORKDIR /app

COPY requirements.txt .
RUN apt-get update && apt-get install -y \
    libgl1-mesa-glx \
    libglib2.0-0 libsm6 libxrender1 libxext6 \
    && pip install --no-cache-dir -r requirements.txt

COPY . .

# Expose the service port
EXPOSE 8080

# Run the Gunicorn server
CMD ["gunicorn", "micro:app"]