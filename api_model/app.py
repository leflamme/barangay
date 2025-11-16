import pandas as pd
from flask import Flask, request, jsonify
import sys

app = Flask(__name__)

# We are NOT loading the model. Its logic is flawed.
# model = joblib.load('model.pkl')

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()
        print(f"--- NEW REQUEST ---", file=sys.stderr)
        print(f"Data received: {data}", file=sys.stderr)

        # Get the inputs from the JSON
        category = data.get('rainfall_category', 'light')
        amount_mm = data.get('rainfall_amount_mm', 0)

        # --- THIS IS THE "AI" LOGIC ---
        # We are defining the correct output ourselves.
        
        prediction_status = 'normal' # Default
        
        if category == 'heavy':
            prediction_status = 'evacuate'
        elif category == 'moderate':
            prediction_status = 'warn'
        elif category == 'light' and amount_mm > 0:
            # This handles the "Yellow Alert"
            prediction_status = 'warn'
        else:
            # This handles "Normal" (light and 0mm)
            prediction_status = 'normal'
        
        # --- END OF LOGIC ---

        print(f"Final Prediction: {prediction_status}", file=sys.stderr)
        
        # Return the prediction as JSON
        print("--- END REQUEST (SUCCESS) ---", file=sys.stderr)
        return jsonify({'prediction': prediction_status})

    except Exception as e:
        print(f"!!! AN ERROR OCCURRED: {str(e)}", file=sys.stderr)
        print("--- END REQUEST (ERROR) ---", file=sys.stderr)
        return jsonify({'error': str(e)})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8080)