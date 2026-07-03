from flask import Flask, jsonify, request
from flask_cors import CORS
import mysql.connector
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

app = Flask(__name__)
CORS(app)

# TỪ ĐIỂN AI: Dạy cho AI biết mỗi loại da cần những từ khóa gì (Đã thêm oil-free)
skin_keywords = {
    "da_dau": "kiềm dầu, mụn, lỗ chân lông to, bã nhờn, làm sạch sâu, salicylic acid, nha đam, oil-free, không chứa dầu",
    "da_kho": "cấp ẩm, dưỡng ẩm, axit hyaluronic, bong tróc, phục hồi, ceramides, HA, cấp nước",
    "da_nhay_cam": "dịu nhẹ, không cồn, không hương liệu, mẩn đỏ, phục hồi da, hoa cúc, an toàn",
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
        ma_nguoi_dung = data.get('ma_nguoi_dung', None)

        # 1. Kết nối Database lấy tất cả sản phẩm đang bán
        conn = get_db_connection()
        query = "SELECT ma_san_pham, ten_san_pham, mo_ta, loai_da_phu_hop, ma_danh_muc, so_luong_ton FROM san_pham WHERE trang_thai = 'dang_ban'"
        df = pd.read_sql(query, conn)
        
        # 1.1 Lấy lịch sử mua hàng của user (nếu có)
        purchased_categories = []
        if ma_nguoi_dung:
            hist_query = f"""
                SELECT DISTINCT sp.ma_danh_muc 
                FROM chi_tiet_don_hang ct 
                JOIN don_hang dh ON ct.ma_don_hang = dh.ma_don_hang 
                JOIN san_pham sp ON ct.ma_san_pham = sp.ma_san_pham
                WHERE dh.ma_nguoi_dung = {ma_nguoi_dung}
            """
            hist_df = pd.read_sql(hist_query, conn)
            purchased_categories = hist_df['ma_danh_muc'].tolist()
            
        conn.close()

        if df.empty:
            return jsonify({"success": False, "data": []})

        # 2. XỬ LÝ AI (NLP - Phân tích văn bản)
        user_profile = skin_keywords.get(loai_da_user, "")
        
        # Điền chuỗi rỗng vào các sản phẩm không có mô tả để AI không báo lỗi
        df['mo_ta'] = df['mo_ta'].fillna("")
        
        # Gộp từ khóa của user và mô tả của tất cả sản phẩm
        documents = [user_profile] + df['mo_ta'].tolist()

        # Thuật toán TF-IDF biến chữ viết thành vector số học (Cải tiến: bắt theo cụm 1-2 từ như "kiềm dầu", "cấp ẩm")
        tfidf = TfidfVectorizer(ngram_range=(1, 2))
        tfidf_matrix = tfidf.fit_transform(documents)

        # Tính độ tương đồng giữa User (vị trí 0) và các Sản phẩm
        cosine_sim = cosine_similarity(tfidf_matrix[0:1], tfidf_matrix[1:]).flatten()

        # Gán điểm số AI vừa chấm được
        df['ai_score'] = cosine_sim

        # 3. Rule-based recommendation: Cộng thêm điểm ưu tiên nếu sản phẩm thiết kế ĐÚNG cho loại da đó
        df.loc[df['loai_da_phu_hop'] == loai_da_user, 'ai_score'] += 0.5
        df.loc[df['loai_da_phu_hop'] == 'tat_ca', 'ai_score'] += 0.2

        # 4. Học từ hành vi người dùng: Cộng điểm cho các sản phẩm cùng danh mục mà user từng mua
        if purchased_categories:
            df.loc[df['ma_danh_muc'].isin(purchased_categories), 'ai_score'] += 0.3

        # 5. Cross-selling theo lộ trình Skincare (Sữa rửa mặt(5) -> Toner(6) -> Serum(7) -> Kem dưỡng(8) -> Chống nắng(4))
        routine_flow = {5: 6, 6: 7, 7: 8, 8: 4}
        if purchased_categories:
            for cat in purchased_categories:
                if cat in routine_flow:
                    next_step = routine_flow[cat]
                    df.loc[df['ma_danh_muc'] == next_step, 'ai_score'] += 0.4
                    
        # 6. Gợi ý kích cầu (Sắp hết hàng): Ưu tiên hiển thị sản phẩm còn ít để khách mua kẻo hết
        df.loc[(df['so_luong_ton'] > 0) & (df['so_luong_ton'] <= 20), 'ai_score'] += 0.25

        # Sắp xếp và lấy 5 sản phẩm có điểm AI cao nhất
        top_products = df.sort_values(by='ai_score', ascending=False).head(5)
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