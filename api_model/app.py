import pandas as pd
import joblib
from flask import Flask, request, jsonify
import sys
import os

app = Flask(__name__)

# Load the trained model at startup
# Ensure model.pkl is in the same directory as app.py
try:
    model = joblib.load('model.pkl')
    print("--- Model Loaded Successfully ---", file=sys.stderr)
except Exception as e:
    print(f"!!! CRITICAL: Could not load model.pkl: {e}", file=sys.stderr)
    model = None

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()
        
        # 1. Get raw data from PHP
        # PHP sends: 'heavy', 'moderate', 'light'
        raw_category = data.get('rainfall_category', 'light')
        amount_mm = float(data.get('rainfall_amount_mm', 0))
        # PHP sends: 'rare', 'moderate', 'frequent' (lowercase)
        raw_history = data.get('flood_history', 'rare')

        # 2. Map PHP data to Model Training Categories
        # The model was trained on: 'Red', 'Orange', 'Yellow'
        category_map = {
            'heavy': 'Red',
            'moderate': 'Orange',
            'light': 'Yellow',
            'normal': 'Yellow' 
        }
        
        # The model was trained on: 'Frequent', 'Moderate', 'Rare' (Title Case)
        history_map = {
            'frequent': 'Frequent',
            'moderate': 'Moderate', # Note: 'moderate' exists in both category and history!
            'rare': 'Rare'
        }

        # Apply mapping
        model_category = category_map.get(raw_category.lower(), 'Yellow')
        model_history = history_map.get(raw_history.lower(), 'Rare')

        # 3. Create DataFrame (Required by scikit-learn pipelines)
        # Must match the exact column names used during training
        input_df = pd.DataFrame([{
            'rainfall_category': model_category,
            'rainfall_amount_mm': amount_mm,
            'flood_history': model_history
        }])

        # 4. Predict using the AI
        if model:
            # Model returns ['Yes'] or ['No']
            prediction_raw = model.predict(input_df)[0]
        else:
            prediction_raw = 'No' # Fallback if model failed to load

        # 5. Convert AI Output to System Status
        # System expects: 'evacuate', 'warn', 'normal'
        final_status = 'normal'

        if prediction_raw == 'Yes':
            final_status = 'evacuate'
        else:
            # If AI says "No Flood", we still check for warning levels
            # to keep residents alert.
            if model_category == 'Orange':
                final_status = 'warn'
            else:
                final_status = 'normal'

        return jsonify({
            'prediction': final_status,
            'ai_raw_response': prediction_raw, # Helpful for debugging
            'input_interpreted': {
                'category': model_category,
                'history': model_history
            }
        })

    except Exception as e:
        print(f"!!! ERROR: {str(e)}", file=sys.stderr)
        return jsonify({'error': str(e)})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8080)