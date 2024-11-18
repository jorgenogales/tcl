FROM pytorch/pytorch:latest

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

# Expose the service port
EXPOSE 8080

# Run the Gunicorn server
CMD ["gunicorn", "micro:app"]