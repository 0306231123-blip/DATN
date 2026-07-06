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
        
        # 1.1 Lấy lịch sử mua hàng của user (lấy cả danh mục và id sản phẩm, chỉ lấy đơn hàng thành công)
        purchased_products = []
        purchased_categories = []
        if ma_nguoi_dung:
            hist_query = f"""
                SELECT DISTINCT sp.ma_san_pham, sp.ma_danh_muc 
                FROM chi_tiet_don_hang ct 
                JOIN don_hang dh ON ct.ma_don_hang = dh.ma_don_hang 
                JOIN san_pham sp ON ct.ma_san_pham = sp.ma_san_pham
                WHERE dh.ma_nguoi_dung = {ma_nguoi_dung}
                AND dh.trang_thai_don IN ('giao_thanh_cong', 'hoan_thanh')
            """
            hist_df = pd.read_sql(hist_query, conn)
            if not hist_df.empty:
                purchased_products = hist_df['ma_san_pham'].tolist()
                purchased_categories = hist_df['ma_danh_muc'].tolist()
            
        conn.close()

        if df.empty:
            return jsonify({"success": False, "data": {"low_stock": [], "next_step": [], "skin_type": []}})

        # ==========================================
        # LIST 1: GỢI Ý MUA LẠI KẺO HẾT (LOW STOCK)
        # ==========================================
        # Tìm sản phẩm ĐÃ MUA THÀNH CÔNG và có tồn kho <= 20
        low_stock_ids = df[(df['ma_san_pham'].isin(purchased_products)) & (df['so_luong_ton'] > 0) & (df['so_luong_ton'] <= 20)]['ma_san_pham'].tolist()

        # ==========================================
        # LIST 2: GỢI Ý BƯỚC TIẾP THEO (CROSS-SELLING)
        # ==========================================
        # Ánh xạ bước tiếp theo dựa trên danh mục đã mua
        # 4: Chống nắng, 5: Sữa rửa mặt, 6: Toner, 7: Serum, 8: Kem dưỡng
        routine_flow = {5: 6, 6: 7, 7: 8, 8: 4, 4: 5} 
        next_step_categories = []
        for cat in purchased_categories:
            if cat in routine_flow:
                next_step_categories.append(routine_flow[cat])
        
        next_step_ids = []
        if next_step_categories:
            next_step_ids = df[df['ma_danh_muc'].isin(next_step_categories)].head(4)['ma_san_pham'].tolist()

        # ==========================================
        # LIST 3: GỢI Ý THEO LOẠI DA (NLP)
        # ==========================================
        user_profile = skin_keywords.get(loai_da_user, "")
        df['mo_ta'] = df['mo_ta'].fillna("")
        documents = [user_profile] + df['mo_ta'].tolist()

        tfidf = TfidfVectorizer(ngram_range=(1, 2))
        tfidf_matrix = tfidf.fit_transform(documents)
        cosine_sim = cosine_similarity(tfidf_matrix[0:1], tfidf_matrix[1:]).flatten()
        df['ai_score'] = cosine_sim

        # Ưu tiên sản phẩm đúng loại da
        df.loc[df['loai_da_phu_hop'] == loai_da_user, 'ai_score'] += 0.5
        df.loc[df['loai_da_phu_hop'] == 'tat_ca', 'ai_score'] += 0.2

        # Loại trừ các sản phẩm đã có ở 2 list trên để tránh trùng lặp
        exclude_ids = set(low_stock_ids + next_step_ids)
        skin_type_ids = df[~df['ma_san_pham'].isin(exclude_ids)].sort_values(by='ai_score', ascending=False).head(4)['ma_san_pham'].tolist()

        return jsonify({
            "success": True, 
            "loai_da_phan_tich": loai_da_user, 
            "data": {
                "low_stock": low_stock_ids,
                "next_step": next_step_ids,
                "skin_type": skin_type_ids
            }
        })

    except Exception as e:
        print("Lỗi AI Server:", e)
        return jsonify({"success": False, "message": str(e)}), 500

if __name__ == '__main__':
    app.run(port=5000, debug=True)