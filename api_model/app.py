import pandas as pd
import joblib
from flask import Flask, request, jsonify
import sys
import os

app = Flask(__name__)

# Load model
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
        
        # 1. Get raw data
        raw_category = data.get('rainfall_category', 'light')
        amount_mm = float(data.get('rainfall_amount_mm', 0))
        raw_history = data.get('flood_history', 'rare')

        # 2. Map to Model Training Categories
        category_map = {'heavy': 'Red', 'moderate': 'Orange', 'light': 'Yellow', 'normal': 'Yellow'}
        history_map = {'frequent': 'Frequent', 'moderate': 'Moderate', 'rare': 'Rare'}

        model_category = category_map.get(raw_category.lower(), 'Yellow')
        model_history = history_map.get(raw_history.lower(), 'Rare')

        # 3. Predict
        if model:
            input_df = pd.DataFrame([{
                'rainfall_category': model_category,
                'rainfall_amount_mm': amount_mm,
                'flood_history': model_history
            }])
            prediction_raw = model.predict(input_df)[0]
        else:
            prediction_raw = 'No'

        # 4. FINAL STATUS LOGIC (Fixed)
        final_status = 'normal'

        if prediction_raw == 'Yes':
            final_status = 'evacuate'
        else:
            # SAFETY FALLBACK: Even if AI says "No Flood", 
            # we must respect the rainfall intensity labels.
            if model_category == 'Red':
                final_status = 'evacuate' # <--- ADDED THIS LINE
            elif model_category == 'Orange':
                final_status = 'warn'
            else:
                final_status = 'normal'

        return jsonify({
            'prediction': final_status,
            'ai_raw_response': prediction_raw,
            'debug_info': f"Input: {model_category}/{model_history}"
        })

    except Exception as e:
        return jsonify({'error': str(e)})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8080)