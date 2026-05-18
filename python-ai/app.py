from flask import Flask, jsonify, request
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

@app.route('/')
def index():
    return jsonify({'message': 'AI Service running'})

@app.route('/recommend', methods=['POST'])
def recommend():
    data = request.json
    skin_type = data.get('skin_type', '')
    recommendations = []
    return jsonify({'recommendations': recommendations})

if __name__ == '__main__':
    app.run(port=5000, debug=True)