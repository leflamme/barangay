import joblib
import pandas as pd
from flask import Flask, request, jsonify
import sys # Import sys

app = Flask(__name__)

# Load your trained model (which is a Pipeline)
model = joblib.load('model.pkl')

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # --- NEW REQUEST ---
        print("--- NEW REQUEST ---", file=sys.stderr)
        data = request.get_json()
        print(f"Data after get_json(): {data}", file=sys.stderr)

        # 1. Create a DataFrame from the input
        # We pass the raw categorical and numerical data
        input_data = pd.DataFrame({
            'rainfall_category': [data['rainfall_category']],
            'rainfall_amount_mm': [data['rainfall_amount_mm']],
            'flood_history': [data['flood_history']]
        })
        print("Successfully created raw input DataFrame", file=sys.stderr)

        # 2. Make a prediction
        # We pass the raw DataFrame directly to the model.
        # The model.pkl (which is a Pipeline) will handle 
        # the one-hot encoding internally.
        prediction = model.predict(input_data)
        
        print(f"Prediction: {prediction[0]}", file=sys.stderr)
        
        # Return the prediction as JSON
        print("--- END REQUEST (SUCCESS) ---", file=sys.stderr)
        return jsonify({'prediction': prediction[0]})

    except Exception as e:
        print(f"!!! AN ERROR OCCURRED: {str(e)}", file=sys.stderr)
        print("--- END REQUEST (ERROR) ---", file=sys.stderr)
        # Return the error in the same format as before
        return jsonify({'error': str(e)})

if __name__ == '__main__':
    # Run the app on port 8080, accessible from other services
    app.run(host='0.0.0.0', port=8080)