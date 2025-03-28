import joblib
import re  # For text preprocessing
from flask import Flask, request, jsonify
from flask_cors import CORS  # Make sure this is installed

app = Flask(__name__)
# Configure CORS specifically for your frontend
CORS(app, resources={
    r"/predict": {
        "origins": "http://localhost:8000",  # Your web server origin
        "methods": ["POST", "OPTIONS"],
        "allow_headers": ["Content-Type"]
    }
})

# Rest of your existing code...

# Load your model and vectorizer
model = joblib.load('logreg_task_classifier.pkl')
tfidf = joblib.load('tfidf_vectorizer.pkl')

def preprocess_text(text):
    """Custom text preprocessing matching your training pipeline"""
    # Convert to lowercase
    text = text.lower()
    # Remove special characters and numbers
    text = re.sub(r'[^a-zA-Z\s]', '', text)
    # Remove extra whitespace
    text = re.sub(r'\s+', ' ', text).strip()
    return text

@app.route('/predict', methods=['POST'])
def predict():
    # Get data from POST request
    data = request.json
    
    # Validate input
    if not data or 'task' not in data:
        return jsonify({'error': 'No task provided'}), 400
    
    try:
        # Preprocess and predict
        cleaned_task = preprocess_text(data['task'])
        task_tfidf = tfidf.transform([cleaned_task])
        prediction = model.predict(task_tfidf)[0]
        
        return jsonify({
            'category': prediction,
            'original_task': data['task']
        })
    
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)



