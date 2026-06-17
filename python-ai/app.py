from flask import Flask, jsonify, request
from flask_cors import CORS
import mysql.connector
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

app = Flask(__name__)
CORS(app)

# TỪ ĐIỂN AI: Dạy cho AI biết mỗi loại da cần những từ khóa gì
skin_keywords = {
    "da_dau": "kiềm dầu, mụn, lỗ chân lông to, bã nhờn, làm sạch sâu, salicylic acid, nha đam",
    "da_kho": "cấp ẩm, dưỡng ẩm, axit hyaluronic, bong tróc, phục hồi, ceramides, HA",
    "da_nhay_cam": "dịu nhẹ, không cồn, không hương liệu, mẩn đỏ, phục hồi da, hoa cúc",
    "da_hon_hop": "cân bằng ẩm, vùng chữ T, kiềm dầu, cấp nước, niacinamide",
    "da_thuong": "duy trì độ ẩm, bảo vệ da, mịn màng, tươi sáng, vitamin C"
}

def get_db_connection():
    # 🚨 QUAN TRỌNG: SỬA TÊN DATABASE Ở DÒNG NÀY 🚨
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="", # Mặc định XAMPP không có pass
        database="db_my_pham" 
    )

@app.route('/')
def index():
    return jsonify({'message': 'AI Service running'})

# Đổi thành /api/recommend cho khớp với file Node.js đang gọi
@app.route('/api/recommend', methods=['POST'])
def recommend():
    try:
        data = request.json
        # Lấy loại da từ Node.js gửi sang, không có thì mặc định là da thường
        loai_da_user = data.get('loai_da', 'da_thuong')

        # 1. Kết nối Database lấy tất cả sản phẩm đang bán
        conn = get_db_connection()
        query = "SELECT ma_san_pham, ten_san_pham, mo_ta, loai_da_phu_hop FROM san_pham WHERE trang_thai = 'dang_ban'"
        df = pd.read_sql(query, conn)
        conn.close()

        if df.empty:
            return jsonify({"success": False, "data": []})

        # 2. XỬ LÝ AI (NLP - Phân tích văn bản)
        user_profile = skin_keywords.get(loai_da_user, "")
        
        # Điền chuỗi rỗng vào các sản phẩm không có mô tả để AI không báo lỗi
        df['mo_ta'] = df['mo_ta'].fillna("")
        
        # Gộp từ khóa của user và mô tả của tất cả sản phẩm
        documents = [user_profile] + df['mo_ta'].tolist()

        # Thuật toán TF-IDF biến chữ viết thành vector số học
        tfidf = TfidfVectorizer()
        tfidf_matrix = tfidf.fit_transform(documents)

        # Tính độ tương đồng giữa User (vị trí 0) và các Sản phẩm
        cosine_sim = cosine_similarity(tfidf_matrix[0:1], tfidf_matrix[1:]).flatten()

        # Gán điểm số AI vừa chấm được
        df['ai_score'] = cosine_sim

        # 3. Cộng thêm điểm ưu tiên nếu sản phẩm đó thiết kế ĐÚNG cho loại da đó
        df.loc[df['loai_da_phu_hop'] == loai_da_user, 'ai_score'] += 0.5
        df.loc[df['loai_da_phu_hop'] == 'tat_ca', 'ai_score'] += 0.2

        # Sắp xếp và lấy 4 sản phẩm có điểm AI cao nhất
        top_products = df.sort_values(by='ai_score', ascending=False).head(4)
        recommended_ids = top_products['ma_san_pham'].tolist()

        return jsonify({
            "success": True, 
            "loai_da_phan_tich": loai_da_user, 
            "data": recommended_ids
        })

    except Exception as e:
        print("Lỗi AI Server:", e)
        return jsonify({"success": False, "message": str(e)}), 500

if __name__ == '__main__':
    app.run(port=5000, debug=True)