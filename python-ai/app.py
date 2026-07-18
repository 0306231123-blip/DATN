from flask import Flask, jsonify, request
from flask_cors import CORS
import os
from dotenv import load_dotenv
import requests
import mysql.connector
import json
import re
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

app = Flask(__name__)
CORS(app)

load_dotenv()
API_KEY = os.getenv("OPENROUTER_API_KEY", "")

# OpenRouter API URL
OPENROUTER_URL = "https://openrouter.ai/api/v1/chat/completions"
# Chọn model nhanh và nhẹ hơn (để tăng tốc độ phản hồi)
AI_MODEL = "meta-llama/llama-3.1-8b-instruct:free"

def get_db_connection():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="", 
        database="db_my_pham" 
    )

FALLBACK_MODELS = [
    "meta-llama/llama-3.1-8b-instruct:free",
    "google/gemma-2-9b-it:free",
    "meta-llama/llama-3.3-70b-instruct:free",
    "tencent/hy3:free"
]

def call_openrouter_api(system_prompt, user_content_parts):
    headers = {
        "Authorization": f"Bearer {API_KEY}",
        "Content-Type": "application/json",
        "HTTP-Referer": "http://localhost:5000",
        "X-Title": "DATN Beauty Shop"
    }
    
    last_response = None
    for model in FALLBACK_MODELS:
        payload = {
            "model": model,
            "messages": [
                {"role": "system", "content": system_prompt},
                {"role": "user", "content": user_content_parts}
            ]
        }
        
        response = requests.post(OPENROUTER_URL, json=payload, headers=headers)
        last_response = response
        if response.status_code == 200:
            return response
        else:
            print(f"Model {model} failed with {response.status_code}. Trying next...")
            
    return last_response

@app.route('/')
def index():
    return jsonify({'message': 'AI Service running with OpenRouter'})

@app.route('/api/ai-chat', methods=['POST'])
def ai_chat():
    try:
        data = request.json
        user_message = data.get('message', '')
        images_data = data.get('images', [])
        
        legacy_image = data.get('image', None)
        if legacy_image and legacy_image not in images_data:
            images_data.append(legacy_image)
            
        if not user_message and not images_data:
            return jsonify({"success": False, "message": "Nội dung tin nhắn không được để trống"}), 400

        if not API_KEY:
             return jsonify({
                 "success": True, 
                 "data": "Hệ thống AI hiện chưa được cấu hình API Key. Hãy mở file .env trong thư mục python-ai và thêm OPENROUTER_API_KEY=your_key."
             })

        try:
            conn = get_db_connection()
            cursor = conn.cursor(dictionary=True)
            cursor.execute("SELECT ma_san_pham, ten_san_pham, mo_ta, loai_da_phu_hop FROM san_pham WHERE trang_thai = 'dang_ban' AND hien_thi_web = 1")
            products = cursor.fetchall()
            conn.close()
            
            # --- RAG SYSTEM IMPLEMENTATION ---
            # Chỉ lấy Top 5 sản phẩm liên quan nhất nếu có user_message
            if products and user_message:
                # 1. Tạo Corpus từ tên và mô tả sản phẩm
                corpus = [f"{p.get('ten_san_pham', '')} {p.get('mo_ta', '')} {p.get('loai_da_phu_hop', '')}" for p in products]
                
                # 2. Vector hóa
                vectorizer = TfidfVectorizer()
                try:
                    tfidf_matrix = vectorizer.fit_transform(corpus)
                    user_vec = vectorizer.transform([user_message])
                    
                    # 3. Tính độ tương đồng
                    cosine_similarities = cosine_similarity(user_vec, tfidf_matrix).flatten()
                    
                    # 4. Lấy index của Top 5 sản phẩm cao điểm nhất
                    # Chú ý: argsort trả về index xếp hạng tăng dần, nên lấy 5 phần tử cuối và đảo ngược
                    top_5_indices = cosine_similarities.argsort()[-5:][::-1]
                    
                    # 5. Lọc ra danh sách top 5 sản phẩm
                    top_products = [products[i] for i in top_5_indices if cosine_similarities[i] > 0]
                    
                    # Nếu user hỏi linh tinh không khớp cái nào, vẫn lấy mặc định 5 cái đầu
                    if not top_products:
                        top_products = products[:5]
                except Exception as e:
                    print("Lỗi TF-IDF RAG:", e)
                    top_products = products[:5]
            else:
                top_products = products[:5]
                
            product_list_text = "\n".join([f"- ID: {p['ma_san_pham']}, Tên: {p['ten_san_pham']}" for p in top_products])
            
            # Trích xuất danh sách các loại da có sẵn từ database
            skin_types_set = set()
            for p in products:
                if p.get('loai_da_phu_hop'):
                    # Tách các loại da nếu được phân cách bằng phẩy
                    types = [t.strip().lower() for t in str(p['loai_da_phu_hop']).split(',')]
                    for t in types:
                        if t:
                            skin_types_set.add(t)
            
            available_skin_types = ", ".join(list(skin_types_set))
            if not available_skin_types:
                available_skin_types = "da thường, da khô, da dầu, da hỗn hợp, da nhạy cảm" # fallback
                
        except Exception as db_err:
            print("Lỗi kết nối DB:", db_err)
            product_list_text = "Không lấy được danh sách sản phẩm."
            available_skin_types = "da thường, da khô, da dầu, da hỗn hợp, da nhạy cảm"

        system_prompt = f"""Bạn là chuyên viên tư vấn sắc đẹp của cửa hàng DATN Beauty Shop.
Cửa hàng hiện tại đang bán các sản phẩm phù hợp cho các loại da: {available_skin_types}.
Hệ thống RAG đã tự động lọc ra danh sách TOP 5 sản phẩm liên quan nhất đến tin nhắn của khách hàng (Bao gồm ID, Tên):
{product_list_text}
Bạn trả lời lịch sự, thân thiện, ngắn gọn và tập trung vào việc giúp đỡ khách hàng giải quyết vấn đề về da hoặc tìm kiếm sản phẩm phù hợp.
Luôn xưng hô với khách hàng là "anh/chị" thay vì chỉ dùng "chị".
Nếu khách hàng hỏi những câu không liên quan đến làm đẹp, hãy lịch sự từ chối.
BẮT BUỘC TRẢ LỜI 100% BẰNG TIẾNG VIỆT CHUẨN (không dùng từ vay mượn lạ, không viết sai chính tả).

QUY TẮC QUAN TRỌNG KHI GỢI Ý SẢN PHẨM:
1. Bạn hãy tư vấn dựa trên yêu cầu của khách hàng. Nếu cần, hãy hỏi thêm về tình trạng da của họ.
2. CHỈ ĐƯỢC PHÉP gợi ý các sản phẩm có trong danh sách trên. Không tự bịa ra sản phẩm.
3. BẮT BUỘC định dạng tên sản phẩm dưới dạng Markdown Link và TUYỆT ĐỐI KHÔNG XUỐNG DÒNG giữa phần tên và phần link.
4. Cú pháp bắt buộc: [Tên Sản Phẩm Của Shop](/user/detail/Mã_Sản_Phẩm)
Ví dụ: Nếu khuyên dùng sản phẩm ID 15 tên là "Kem Chống Nắng ABC", bạn phải viết là: [Kem Chống Nắng ABC](/user/detail/15)
5. Khi khách hàng nhờ tư vấn sản phẩm nhưng chưa đề cập đến loại da của họ (như da dầu, da khô, da nhạy cảm...), bạn phải chủ động hỏi họ thuộc loại da nào hoặc đang gặp vấn đề gì trước khi đưa ra gợi ý."""

        user_content = []
        if user_message:
            user_content.append({"type": "text", "text": user_message})
        else:
            user_content.append({"type": "text", "text": "Hãy phân tích hình ảnh này và tư vấn cho tôi."})
            
        for img_b64 in images_data:
            if img_b64:
                # Đảm bảo có prefix data:image/jpeg;base64, cho OpenRouter
                if not img_b64.startswith("data:image"):
                    img_b64 = "data:image/jpeg;base64," + img_b64
                user_content.append({
                    "type": "image_url",
                    "image_url": {
                        "url": img_b64
                    }
                })
        
        api_res = call_openrouter_api(system_prompt, user_content)
        
        if api_res.status_code == 200:
            ai_text = api_res.json()['choices'][0]['message']['content']
            return jsonify({"success": True, "data": ai_text})
        elif api_res.status_code == 429:
            print(f"OpenRouter API Error 429. Rate limited.")
            mock_text = "Hệ thống AI (bản miễn phí) đang bị quá tải số lượng người dùng. Bạn vui lòng đợi khoảng 1 phút rồi nhắn lại nhé!"
            return jsonify({"success": True, "data": mock_text})
        elif api_res.status_code == 403:
            print(f"OpenRouter API Error 403. Forbidden.")
            mock_text = "API Key đã bị khóa hoặc hết hạn. Vui lòng kiểm tra lại."
            return jsonify({"success": True, "data": mock_text})
        else:
            err_data = api_res.json()
            err_msg = err_data.get('error', {}).get('message', 'Lỗi không xác định từ OpenRouter')
            print("OpenRouter API Error:", err_data)
            return jsonify({"success": False, "data": f"Lỗi API Key: {err_msg}"})
            
    except Exception as e:
        print("Lỗi AI Chat:", e)
        return jsonify({"success": False, "data": f"Lỗi Server Python: {str(e)}"})

@app.route('/api/ai-suggest-restock', methods=['POST'])
def ai_suggest_restock():
    try:
        data = request.json
        user_id = data.get('user_id')
        if not user_id:
            return jsonify({"success": False, "has_suggestion": False})

        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        query = """
            SELECT DISTINCT p.ma_san_pham, p.ten_san_pham, p.so_luong_ton 
            FROM san_pham p
            JOIN chi_tiet_don_hang ctdh ON p.ma_san_pham = ctdh.ma_san_pham
            JOIN don_hang dh ON ctdh.ma_don_hang = dh.ma_don_hang
            WHERE dh.ma_nguoi_dung = %s 
            AND dh.trang_thai_don IN ('giao_thanh_cong', 'hoan_thanh')
            AND p.so_luong_ton > 0 AND p.so_luong_ton <= 20
        """
        cursor.execute(query, (user_id,))
        products = cursor.fetchall()
        cursor.close()
        conn.close()

        if not products:
            return jsonify({"success": True, "has_suggestion": False})

        product_list_text = "\n".join([f"- {p['ten_san_pham']} (Kho chỉ còn: {p['so_luong_ton']}, Mã: {p['ma_san_pham']})" for p in products])
        
        system_prompt = f"""Đóng vai nhân viên AI chăm sóc khách hàng cực kỳ thân thiện và tinh tế của shop mỹ phẩm. 
Khách hàng vừa truy cập website. Họ đã từng mua các sản phẩm sau đây và hệ thống báo động các sản phẩm này sắp HẾT HÀNG:
{product_list_text}

Nhiệm vụ: Viết 1 tin nhắn ngắn gọn (dưới 50 chữ), chào mừng khách quay lại và báo tin sản phẩm họ từng mua sắp hết hàng, khuyên họ nên mua dự phòng. 
BẮT BUỘC CHÈN LINK SẢN PHẨM đúng cú pháp: [Tên Sản Phẩm](/user/detail/Mã_Sản_Phẩm). 
Tuyệt đối không bịa ra sản phẩm khác. Xưng hô là 'anh/chị'."""

        user_content = [{"type": "text", "text": "Hãy tạo tin nhắn gửi khách hàng ngay bây giờ."}]
        
        api_res = call_openrouter_api(system_prompt, user_content)
        
        if api_res.status_code == 200:
            res_json = api_res.json()
            reply_text = res_json['choices'][0]['message']['content']
            return jsonify({"success": True, "has_suggestion": True, "message": reply_text})
        else:
            return jsonify({"success": False, "has_suggestion": False})
            
    except Exception as e:
        print("Lỗi AI Suggest:", e)
        return jsonify({"success": False, "has_suggestion": False})

@app.route('/api/ai-next-step-suggest', methods=['POST'])
def ai_next_step_suggest():
    try:
        data = request.json
        user_id = data.get('user_id')
        if not user_id:
            return jsonify({"success": False})

        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        
        query_recent_order = """
            SELECT ma_don_hang
            FROM don_hang
            WHERE ma_nguoi_dung = %s 
            AND trang_thai_don IN ('giao_thanh_cong', 'hoan_thanh')
            ORDER BY ngay_dat DESC
            LIMIT 1
        """
        cursor.execute(query_recent_order, (user_id,))
        recent_order = cursor.fetchone()
        
        if not recent_order:
            cursor.close()
            conn.close()
            return jsonify({"success": False})
            
        ma_don_hang_gan_nhat = recent_order['ma_don_hang']
        
        # KIỂM TRA CACHE
        cursor.execute("SELECT ds_ma_san_pham, ly_do, ds_san_pham_di_kem, ly_do_di_kem FROM ai_goi_y_lich_su WHERE ma_don_hang = %s", (ma_don_hang_gan_nhat,))
        cache_row = cursor.fetchone()
        
        if cache_row:
            # Xử lý missing
            ds_ma_san_pham = json.loads(cache_row['ds_ma_san_pham'])
            reason = cache_row['ly_do']
            suggested_products = []
            for ma_sp in ds_ma_san_pham:
                cursor.execute("""
                    SELECT sp.*, dm.ten_danh_muc 
                    FROM san_pham sp 
                    LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                    WHERE sp.ma_san_pham = %s
                """, (ma_sp,))
                prod = cursor.fetchone()
                if prod:
                    cursor.execute("SELECT duong_dan_anh FROM anh_san_pham WHERE ma_san_pham = %s AND la_anh_chinh = 1", (ma_sp,))
                    img = cursor.fetchone()
                    prod['duong_dan_anh'] = img['duong_dan_anh'] if img else None
                    suggested_products.append(prod)

            # Xử lý companion
            companion_products_out = []
            ly_do_di_kem = cache_row['ly_do_di_kem']
            if cache_row['ds_san_pham_di_kem']:
                ds_di_kem = json.loads(cache_row['ds_san_pham_di_kem'])
                for item in ds_di_kem:
                    cursor.execute("""
                        SELECT sp.*, dm.ten_danh_muc 
                        FROM san_pham sp 
                        LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                        WHERE sp.ma_san_pham = %s
                    """, (item['ma_sp'],))
                    prod = cursor.fetchone()
                    if prod:
                        cursor.execute("SELECT duong_dan_anh FROM anh_san_pham WHERE ma_san_pham = %s AND la_anh_chinh = 1", (item['ma_sp'],))
                        img = cursor.fetchone()
                        prod['duong_dan_anh'] = img['duong_dan_anh'] if img else None
                        prod['for_product'] = item.get('for_product', '')
                        companion_products_out.append(prod)

            cursor.close()
            conn.close()
            
            return jsonify({
                "success": True, 
                "missing": {"products": suggested_products, "reason": reason},
                "companion": {"products": companion_products_out, "reason": ly_do_di_kem}
            })
        
        query_bought = """
            SELECT DISTINCT p.ten_san_pham
            FROM san_pham p
            JOIN chi_tiet_don_hang ctdh ON p.ma_san_pham = ctdh.ma_san_pham
            WHERE ctdh.ma_don_hang = %s
        """
        cursor.execute(query_bought, (ma_don_hang_gan_nhat,))
        bought = cursor.fetchall()
        
        if not bought:
            cursor.close()
            conn.close()
            return jsonify({"success": False})
            
        bought_str = ", ".join([b['ten_san_pham'] for b in bought])
        
        system_prompt = f"""Đóng vai chuyên gia da liễu cực kỳ chuyên nghiệp.
Đơn hàng gần nhất của khách hàng này bao gồm các món sau: {bought_str}

Nhiệm vụ:
1. Đánh giá chu trình Skincare hiện tại của khách dựa vào đơn hàng gần nhất (Họ đang thiếu NHỮNG bước nào quan trọng nhất?).
2. Gợi ý TỪ 1 ĐẾN 3 TỪ KHÓA CHUNG (ví dụ: "sữa rửa mặt", "kem chống nắng", "nước hoa hồng", "tẩy trang") để bổ sung vào các bước còn thiếu.
3. Gợi ý TỪ 1 ĐẾN 3 TỪ KHÓA CHUNG là sản phẩm ĐI KÈM lý tưởng cho các món họ đã mua. (ví dụ: mua sữa rửa mặt thì gợi ý đi kèm là nước tẩy trang hoặc máy rửa mặt). Ghi rõ sản phẩm đi kèm cho món nào.
4. Trả về đúng định dạng JSON chuẩn (KHÔNG có markdown ```json, KHÔNG có text thừa xung quanh):
{{
    "missing_keywords": ["từ khóa 1", "từ khóa 2"],
    "missing_reason": "<Một đoạn văn giải thích chung vì sao nên bổ sung các bước thiếu>",
    "companion_products": [
        {{"keyword": "từ khóa 1", "for_product": "Tên món hàng đã mua"}}
    ],
    "companion_reason": "<Một đoạn văn giải thích vì sao các sản phẩm này là đi kèm lý tưởng>"
}}"""

        user_content = [{"type": "text", "text": "Bạn phải trả về JSON chuẩn, không thêm bất kỳ ký tự nào khác."}]
        
        api_res = call_openrouter_api(system_prompt, user_content)
        
        if api_res.status_code == 200:
            res_json = api_res.json()
            reply_text = res_json['choices'][0]['message']['content']
            
            try:
                match = re.search(r'\{.*\}', reply_text, re.DOTALL)
                if match:
                    json_str = match.group(0)
                    ai_data = json.loads(json_str)
                else:
                    raise Exception("Không tìm thấy JSON hợp lệ trong phản hồi")
                    
                missing_keywords = ai_data.get("missing_keywords", [])
                missing_reason = ai_data.get("missing_reason", "")
                companion_raw = ai_data.get("companion_products", [])
                companion_reason = ai_data.get("companion_reason", "")
                
                if not isinstance(missing_keywords, list):
                    missing_keywords = [missing_keywords]
                    
                # RAG: Truy vấn DB để tìm sản phẩm khớp với keyword missing
                ma_san_pham_list = []
                for keyword in missing_keywords:
                    if not keyword: continue
                    cursor.execute("""
                        SELECT sp.ma_san_pham FROM san_pham sp
                        LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                        WHERE (sp.ten_san_pham LIKE %s OR dm.ten_danh_muc LIKE %s OR sp.mo_ta LIKE %s) 
                        AND sp.trang_thai = 'dang_ban' AND sp.hien_thi_web = 1
                        ORDER BY 
                            CASE 
                                WHEN dm.ten_danh_muc LIKE %s THEN 1
                                WHEN sp.ten_san_pham LIKE %s THEN 2
                                ELSE 3
                            END
                        LIMIT 1
                    """, (f"%{keyword}%", f"%{keyword}%", f"%{keyword}%", f"%{keyword}%", f"%{keyword}%"))
                    row = cursor.fetchone()
                    if row and row['ma_san_pham'] not in ma_san_pham_list:
                        ma_san_pham_list.append(row['ma_san_pham'])
                    
                suggested_products = []
                for ma_sp in ma_san_pham_list:
                    cursor.execute("""
                        SELECT sp.*, dm.ten_danh_muc 
                        FROM san_pham sp 
                        LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                        WHERE sp.ma_san_pham = %s
                    """, (ma_sp,))
                    prod = cursor.fetchone()
                    if prod:
                        cursor.execute("SELECT duong_dan_anh FROM anh_san_pham WHERE ma_san_pham = %s AND la_anh_chinh = 1", (ma_sp,))
                        img = cursor.fetchone()
                        prod['duong_dan_anh'] = img['duong_dan_anh'] if img else None
                        suggested_products.append(prod)
                        
                # RAG: Truy vấn DB để tìm sản phẩm khớp với keyword companion
                companion_list_db = []
                companion_products_out = []
                for comp in companion_raw:
                    keyword = comp.get("keyword")
                    for_prod = comp.get("for_product")
                    if not keyword: continue
                    cursor.execute("""
                        SELECT sp.ma_san_pham FROM san_pham sp
                        LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                        WHERE (sp.ten_san_pham LIKE %s OR dm.ten_danh_muc LIKE %s OR sp.mo_ta LIKE %s) 
                        AND sp.trang_thai = 'dang_ban' AND sp.hien_thi_web = 1
                        ORDER BY 
                            CASE 
                                WHEN dm.ten_danh_muc LIKE %s THEN 1
                                WHEN sp.ten_san_pham LIKE %s THEN 2
                                ELSE 3
                            END
                        LIMIT 1
                    """, (f"%{keyword}%", f"%{keyword}%", f"%{keyword}%", f"%{keyword}%", f"%{keyword}%"))
                    row = cursor.fetchone()
                    if row:
                        ma_sp = row['ma_san_pham']
                        # Check if already added
                        if not any(x['ma_sp'] == ma_sp for x in companion_list_db):
                            companion_list_db.append({"ma_sp": ma_sp, "for_product": for_prod})
                            
                for item in companion_list_db:
                    cursor.execute("""
                        SELECT sp.*, dm.ten_danh_muc 
                        FROM san_pham sp 
                        LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                        WHERE sp.ma_san_pham = %s
                    """, (item['ma_sp'],))
                    prod = cursor.fetchone()
                    if prod:
                        cursor.execute("SELECT duong_dan_anh FROM anh_san_pham WHERE ma_san_pham = %s AND la_anh_chinh = 1", (item['ma_sp'],))
                        img = cursor.fetchone()
                        prod['duong_dan_anh'] = img['duong_dan_anh'] if img else None
                        prod['for_product'] = item['for_product']
                        companion_products_out.append(prod)
                
                try:
                    cursor.execute("""
                        INSERT INTO ai_goi_y_lich_su (ma_don_hang, ds_ma_san_pham, ly_do, ds_san_pham_di_kem, ly_do_di_kem)
                        VALUES (%s, %s, %s, %s, %s)
                    """, (ma_don_hang_gan_nhat, json.dumps(ma_san_pham_list), missing_reason, json.dumps(companion_list_db), companion_reason))
                    conn.commit()
                except Exception as e:
                    print("Lỗi lưu cache AI:", e)

                cursor.close()
                conn.close()
                return jsonify({
                    "success": True, 
                    "missing": {"products": suggested_products, "reason": missing_reason},
                    "companion": {"products": companion_products_out, "reason": companion_reason}
                })
                    
            except Exception as parse_e:
                with open("error_log.txt", "a", encoding="utf-8") as f:
                    f.write(f"Parse error: {parse_e}\nRaw: {reply_text}\n")
                print("Lỗi parse JSON OpenRouter:", parse_e, "Raw:", reply_text)
                
        else:
            with open("error_log.txt", "a", encoding="utf-8") as f:
                f.write(f"OpenRouter API Error: {api_res.status_code} - {api_res.text}\n")
                
        cursor.close()
        conn.close()
        return jsonify({"success": False})
            
    except Exception as e:
        import traceback
        with open("error_log.txt", "a", encoding="utf-8") as f:
            f.write(f"Main Exception: {e}\n{traceback.format_exc()}\n")
        print("Lỗi AI Next Step:", e)
        return jsonify({"success": False})


@app.route('/api/ai-skin-type-suggest', methods=['POST'])
def ai_skin_type_suggest():
    try:
        data = request.json
        user_id = data.get('user_id')
        if not user_id:
            return jsonify({"success": False, "message": "Thiếu user_id"}), 400
            
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        
        # Get user's skin type
        cursor.execute("SELECT loai_da FROM nguoi_dung WHERE ma_nguoi_dung = %s", (user_id,))
        user_info = cursor.fetchone()
        
        if not user_info:
            cursor.close()
            conn.close()
            return jsonify({"success": False, "message": "Không tìm thấy user"}), 404
            
        loai_da_map = {
            'da_dau': 'Da dầu',
            'da_kho': 'Da khô',
            'da_hon_hop': 'Da hỗn hợp',
            'da_nhay_cam': 'Da nhạy cảm',
            'da_thuong': 'Da thường'
        }
        
        loai_da_raw = user_info['loai_da'] or 'da_thuong'
        loai_da_text = loai_da_map.get(loai_da_raw, 'Da thường')
        
        from datetime import datetime, timedelta
        vn_time = datetime.utcnow() + timedelta(hours=7)
        current_hour = vn_time.hour
        
        if 5 <= current_hour < 11:
            session_name = "Buổi Sáng"
        elif 11 <= current_hour < 14:
            session_name = "Buổi Trưa"
        elif 14 <= current_hour < 18:
            session_name = "Buổi Chiều"
        elif 18 <= current_hour < 22:
            session_name = "Buổi Tối"
        else:
            session_name = "Ban Đêm"
        
        system_prompt = f"""Đóng vai chuyên gia da liễu cực kỳ chuyên nghiệp.
Khách hàng này có loại da: {loai_da_text}.
Hiện tại đang là {session_name} (theo giờ Việt Nam).

Nhiệm vụ:
1. Gợi ý CHÍNH XÁC 3 TỪ KHÓA CHUNG (ví dụ: "sữa rửa mặt", "kem chống nắng", "kem dưỡng", "tẩy trang", "serum", "mặt nạ ngủ") về các loại sản phẩm hoặc tính năng cần thiết nhất để chăm sóc da vào {session_name} dành riêng cho {loai_da_text}.
2. Trả về đúng định dạng JSON chuẩn (KHÔNG có markdown ```json, KHÔNG có text thừa xung quanh):
{{
    "missing_keywords": ["từ khóa 1", "từ khóa 2", "từ khóa 3"],
    "reason": "<Một đoạn văn giải thích (khoảng 3-4 câu) vì sao {loai_da_text} nên dùng 3 sản phẩm này vào {session_name}>"
}}"""

        user_content = [{"type": "text", "text": "Bạn phải trả về JSON chuẩn, không thêm bất kỳ ký tự nào khác."}]
        
        api_res = call_openrouter_api(system_prompt, user_content)
        
        if api_res.status_code == 200:
            res_json = api_res.json()
            reply_text = res_json['choices'][0]['message']['content']
            
            try:
                import re
                match = re.search(r'\{.*\}', reply_text, re.DOTALL)
                if match:
                    json_str = match.group(0)
                    ai_data = json.loads(json_str)
                else:
                    raise Exception("Không tìm thấy JSON hợp lệ trong phản hồi")
                    
                missing_keywords = ai_data.get("missing_keywords", [])
                reason = ai_data.get("reason")
                
                if not isinstance(missing_keywords, list):
                    missing_keywords = [missing_keywords]
                    
                # RAG: Query products matching keywords
                ma_san_pham_list = []
                for keyword in missing_keywords:
                    if not keyword: continue
                    cursor.execute("""
                        SELECT sp.ma_san_pham FROM san_pham sp
                        LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                        WHERE (sp.ten_san_pham LIKE %s OR dm.ten_danh_muc LIKE %s OR sp.mo_ta LIKE %s) 
                        AND sp.trang_thai = 'dang_ban' AND sp.hien_thi_web = 1
                        ORDER BY 
                            CASE 
                                WHEN dm.ten_danh_muc LIKE %s THEN 1
                                WHEN sp.ten_san_pham LIKE %s THEN 2
                                ELSE 3
                            END
                        LIMIT 1
                    """, (f"%{keyword}%", f"%{keyword}%", f"%{keyword}%", f"%{keyword}%", f"%{keyword}%"))
                    row = cursor.fetchone()
                    if row and row['ma_san_pham'] not in ma_san_pham_list:
                        ma_san_pham_list.append(row['ma_san_pham'])
                    
                suggested_products = []
                for ma_sp in ma_san_pham_list:
                    cursor.execute("""
                        SELECT sp.*, dm.ten_danh_muc 
                        FROM san_pham sp
                        LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                        WHERE sp.ma_san_pham = %s
                    """, (ma_sp,))
                    sp_row = cursor.fetchone()
                    if sp_row:
                        # Get images
                        cursor.execute("SELECT duong_dan_anh FROM anh_san_pham WHERE ma_san_pham = %s LIMIT 1", (ma_sp,))
                        img = cursor.fetchone()
                        sp_row['hinh_anh_url'] = img['duong_dan_anh'] if img else None
                        suggested_products.append(sp_row)
                        
                cursor.close()
                conn.close()
                
                return jsonify({
                    "success": True,
                    "reason": reason,
                    "products": suggested_products,
                    "loai_da_text": loai_da_text,
                    "session_name": session_name
                })
            except Exception as e:
                print("Lỗi parse JSON:", str(e), reply_text)
                cursor.close()
                conn.close()
                return jsonify({"success": False, "message": "Lỗi parse dữ liệu từ AI"})
        else:
            cursor.close()
            conn.close()
            return jsonify({"success": False, "message": "Lỗi gọi OpenRouter API"})
            
    except Exception as e:
        print("Lỗi xử lý API ai-skin-type-suggest:", str(e))
        return jsonify({"success": False, "message": str(e)})


if __name__ == '__main__':
    app.run(port=5000, debug=True)