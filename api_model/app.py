import joblib
import pandas as pd
from flask import Flask, request, jsonify
import sys # Import sys

app = Flask(__name__)

# Load your trained model
model = joblib.load('model.pkl')

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # --- NEW DEBUGGING CODE ---
        # We print to stderr so it shows up in Railway logs
        print("--- NEW REQUEST ---", file=sys.stderr)
        
        # 1. Get raw data as bytes
        raw_data = request.data
        print(f"Raw data received: {raw_data}", file=sys.stderr)

        # 2. Get data as JSON
        data = request.get_json()
        print(f"Data after get_json(): {data}", file=sys.stderr)
        print(f"Type of data: {type(data)}", file=sys.stderr)
        # --- END DEBUGGING CODE ---

        # 1. Create a DataFrame from the input
        # We will use the more robust method
        input_data = pd.DataFrame({
            'rainfall_category': [data['rainfall_category']],
            'rainfall_amount_mm': [data['rainfall_amount_mm']],
            'flood_history': [data['flood_history']]
        })
        print("Successfully created DataFrame", file=sys.stderr)

        # 2. Pre-process the data (One-Hot Encoding)
        processed_data = pd.get_dummies(input_data, columns=['rainfall_category', 'flood_history'])
        print("Successfully one-hot encoded", file=sys.stderr)
        
        # 3. Ensure all columns from training are present
        # --- YOU MUST UPDATE THIS LIST ---
        # Get these column names from your Jupyter Notebook
        # after you one-hot encoded. It will look something like this:
        expected_cols = [
            'rainfall_amount_mm',
            'rainfall_category_heavy',
            'rainfall_category_light',
            'rainfall_category_moderate',
            'flood_history_frequent',
            'flood_history_moderate',
            'flood_history_rare'
        ]
        
        # Add any missing columns with a value of 0
        for col in expected_cols:
            if col not in processed_data.columns:
                processed_data[col] = 0
                
        # Reorder columns to match training
        processed_data = processed_data[expected_cols]
        print("Successfully aligned columns", file=sys.stderr)

        # 4. Make a prediction
        prediction = model.predict(processed_data)
        print(f"Prediction: {prediction[0]}", file=sys.stderr)
        
        # Return the prediction as JSON
        return jsonify({'prediction': prediction[0]})

    except Exception as e:
        # --- NEW DEBUGGING FOR ERROR ---
        print(f"!!! AN ERROR OCCURRED: {str(e)}", file=sys.stderr)
        print("--- END REQUEST ---", file=sys.stderr)
        # --- END DEBUGGING ---
        return jsonify({'error': str(e)})

if __name__ == '__main__':
    # Run the app on port 8080, accessible from other services
    app.run(host='0.0.0.0', port=8080)