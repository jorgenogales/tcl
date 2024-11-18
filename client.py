import requests
import json

def call_php_micro():
    try:
        response = requests.get('http://0.0.0.0:8080')
        response.raise_for_status()  # Raise an exception for bad status codes (4xx or 5xx)
        return response.json()
    except requests.exceptions.RequestException as e:
        print(f"Error calling microservice: {e}")
        return None

    except json.JSONDecodeError as e:
        print(f"Error decoding JSON response: {e}")
        return None


if __name__ == "__main__":
    
    response_data = call_php_micro()

    if response_data:
        print("Microservice response:")
        print(json.dumps(response_data, indent=4)) # Print nicely formatted JSON
    else:
        print("Failed to call the microservice.")