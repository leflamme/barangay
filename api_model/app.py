import joblib
import pandas as pd
from flask import Flask, request, jsonify

app = Flask(__name__)

# Load your trained model
model = joblib.load('model.pkl')

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # Get data from the POST request
        data = request.get_json()

        # --- IMPORTANT ---
        # The model was trained on features:
        # ["rainfall_category", "rainfall_amount_mm", "flood_history"]
        #
        # We must create a DataFrame with these exact columns.
        # We also need to one-hot encode the categorical features
        # exactly like you did in your Jupyter Notebook.
        
        # 1. Create a DataFrame from the input
        # We will build it manually to be safe.
        input_data = pd.DataFrame({
            'rainfall_category': [data['rainfall_category']],
            'rainfall_amount_mm': [data['rainfall_amount_mm']],
            'flood_history': [data['flood_history']]
        })
        
        # 2. Pre-process the data (One-Hot Encoding)
        # This MUST match your training notebook.
        processed_data = pd.get_dummies(input_data, columns=['rainfall_category', 'flood_history'])
        
        # 3. Ensure all columns from training are present
        # This is a common failure point. We create a list of all
        # columns the model expects, based on your training.
        
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

        # 4. Make a prediction
        prediction = model.predict(processed_data)
        
        # Return the prediction as JSON
        return jsonify({'prediction': prediction[0]})

    except Exception as e:
        return jsonify({'error': str(e)})

if __name__ == '__main__':
    # Run the app on port 8080, accessible from other services
    app.run(host='0.0.0.0', port=8080)