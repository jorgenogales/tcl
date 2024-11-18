# gunicorn.conf.py

bind = "0.0.0.0:8080"  # Bind to all interfaces on port 8080
workers = 1  # Adjust the number of workers as needed
#worker_class = "gevent" # "uvicorn.workers.UvicornWorker"  # Use the gevent worker class
timeout = 120  # Set the worker timeout to 120 seconds
preload_app = False  # Preload the Flask application

# Logging configuration
loglevel = "debug"  # Set the logging level to debug
errorlog = "-"  # Log errors to stderr
accesslog = "-"  # Log access logs to stdout