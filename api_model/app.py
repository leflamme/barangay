import pandas as pd
from flask import Flask, request, jsonify
import sys

app = Flask(__name__)

# --- ADD A VERSION ---
APP_VERSION = "v2.0_hardcoded_logic"
# --- END VERSION ---

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # --- PRINT THE VERSION ---
        print(f"--- NEW REQUEST (VERSION {APP_VERSION}) ---", file=sys.stderr)
        # --- END PRINT ---
        
        data = request.get_json()
        print(f"Data received: {data}", file=sys.stderr)

        category = data.get('rainfall_category', 'light').lower() 
        amount_mm = data.get('rainfall_amount_mm', 0)

        prediction_status = 'normal' # Default
        
        if category == 'heavy':
            prediction_status = 'evacuate'
        elif category == 'moderate':
            prediction_status = 'warn'
        elif category == 'light' and amount_mm > 5:
            prediction_status = 'warn'
        else:
            prediction_status = 'normal'
        
        print(f"Final Prediction: {prediction_status}", file=sys.stderr)
        print("--- END REQUEST (SUCCESS) ---", file=sys.stderr)
        return jsonify({'prediction': prediction_status})

    except Exception as e:
        print(f"!!! AN ERROR OCCURRED: {str(e)}", file=sys.stderr)
        print("--- END REQUEST (ERROR) ---", file=sys.stderr)
        return jsonify({'error': str(e)})

if __name__ == '__main__':
    print(f"--- Starting Flask App (VERSION {APP_VERSION}) ---", file=sys.stderr)
    app.run(host='0.0.0.0', port=8080)