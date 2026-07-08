from flask import Flask, jsonify, request
from flask_cors import CORS
import os
from dotenv import load_dotenv
import requests
import mysql.connector

app = Flask(__name__)
CORS(app)

load_dotenv()
GEMINI_API_KEYS_STR = os.getenv("GEMINI_API_KEYS", os.getenv("GEMINI_API_KEY", ""))
GEMINI_API_KEYS = [k.strip() for k in GEMINI_API_KEYS_STR.split(",") if k.strip()]

def get_db_connection():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="", 
        database="db_my_pham" 
    )

@app.route('/')
def index():
    return jsonify({'message': 'AI Service running'})

@app.route('/api/ai-chat', methods=['POST'])
def ai_chat():
    try:
        data = request.json
        user_message = data.get('message', '')
        images_data = data.get('images', [])
        
        # Backward compatibility
        legacy_image = data.get('image', None)
        if legacy_image and legacy_image not in images_data:
            images_data.append(legacy_image)
            
        if not user_message and not images_data:
            return jsonify({"success": False, "message": "Nội dung tin nhắn không được để trống"}), 400

        if not GEMINI_API_KEY:
             return jsonify({
                 "success": True, 
                 "data": "Hệ thống AI hiện chưa được cấu hình API Key. Hãy tạo file .env trong thư mục python-ai và thêm GEMINI_API_KEY=your_key."
             })

        try:
            conn = get_db_connection()
            cursor = conn.cursor(dictionary=True)
            cursor.execute("SELECT ma_san_pham, ten_san_pham, mo_ta FROM san_pham WHERE trang_thai = 'dang_ban' LIMIT 200")
            products = cursor.fetchall()
            conn.close()
            
            product_list_text = "\\n".join([f"- ID: {p['ma_san_pham']}, Tên: {p['ten_san_pham']}" for p in products])
        except Exception as db_err:
            print("Lỗi kết nối DB:", db_err)
            product_list_text = "Hiện không thể lấy danh sách sản phẩm."

        system_prompt = f"""Bạn là một chuyên gia tư vấn sắc đẹp và chăm sóc da chuyên nghiệp của cửa hàng mỹ phẩm. 
Bạn trả lời lịch sự, thân thiện, ngắn gọn và tập trung vào việc giúp đỡ khách hàng giải quyết vấn đề về da hoặc tìm kiếm sản phẩm phù hợp.
Luôn xưng hô với khách hàng là "anh/chị" thay vì chỉ dùng "chị".
Nếu khách hàng hỏi những câu không liên quan đến làm đẹp, hãy lịch sự từ chối.

ĐÂY LÀ DANH SÁCH SẢN PHẨM MÀ CỬA HÀNG ĐANG BÁN:
{product_list_text}

QUY TẮC QUAN TRỌNG KHI GỢI Ý SẢN PHẨM:
1. Nếu người dùng gửi kèm một bức ảnh, hãy phân tích kỹ bức ảnh đó (ví dụ: xem tình trạng da, phân tích thành phần sản phẩm trong ảnh, hoặc nhận diện loại sản phẩm) và đối chiếu với danh sách trên để đưa ra gợi ý tốt nhất.
2. CHỈ ĐƯỢC PHÉP gợi ý các sản phẩm có trong danh sách trên. Không tự bịa ra sản phẩm.
3. BẮT BUỘC định dạng tên sản phẩm dưới dạng Markdown Link và TUYỆT ĐỐI KHÔNG XUỐNG DÒNG giữa phần tên và phần link.
4. Cú pháp bắt buộc: [Tên Sản Phẩm Của Shop](/user/detail/Mã_Sản_Phẩm)
Ví dụ: Nếu khuyên dùng sản phẩm ID 15 tên là "Kem Chống Nắng ABC", bạn phải viết là: [Kem Chống Nắng ABC](/user/detail/15)"""

        url = f"https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={GEMINI_API_KEY}"
        
        contents_parts = []
        if user_message:
            contents_parts.append({"text": user_message})
        else:
            contents_parts.append({"text": "Hãy phân tích hình ảnh này và tư vấn cho tôi."})
            
        for img_b64 in images_data:
            try:
                meta_part, b64_part = img_b64.split(',', 1)
                mime_type = meta_part.split(';')[0].split(':')[1]
                contents_parts.append({
                    "inlineData": {
                        "mimeType": mime_type,
                        "data": b64_part
                    }
                })
            except Exception as e:
                print("Error parsing base64 image:", e)
        
        payload = {
            "system_instruction": {
                "parts": [{"text": system_prompt}]
            },
            "contents": [{
                "parts": contents_parts
            }]
        }
        
        headers = {'Content-Type': 'application/json'}
        gemini_res = requests.post(url, json=payload, headers=headers)
        
        if gemini_res.status_code == 200:
            ai_text = gemini_res.json()['candidates'][0]['content']['parts'][0]['text']
            return jsonify({"success": True, "data": ai_text})
        elif gemini_res.status_code == 403:
            # Fallback mock response if Google blocks the API Key
            print("Gemini API Error 403: Project denied. Using mock response.")
            mock_text = "Chào bạn! Mình là AI tư vấn (chế độ thử nghiệm do API Key bị lỗi). Dựa theo tình trạng của bạn, mình thấy [Sữa Rửa Mặt Nghệ Cốt Đặc](/user/detail/6) và [Kem Chống Nắng Vật Lý](/user/detail/10) rất phù hợp đấy ạ!"
            return jsonify({"success": True, "data": mock_text})
        else:
            err_data = gemini_res.json()
            err_msg = err_data.get('error', {}).get('message', 'Lỗi không xác định từ Gemini')
            print("Gemini API Error:", err_data)
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

        product_list_text = "\\n".join([f"- {p['ten_san_pham']} (Kho chỉ còn: {p['so_luong_ton']}, Mã: {p['ma_san_pham']})" for p in products])
        
        system_prompt = f"""Đóng vai nhân viên AI chăm sóc khách hàng cực kỳ thân thiện và tinh tế của shop mỹ phẩm. 
Khách hàng vừa truy cập website. Họ đã từng mua các sản phẩm sau đây và hệ thống báo động các sản phẩm này sắp HẾT HÀNG:
{product_list_text}

Nhiệm vụ: Viết 1 tin nhắn ngắn gọn (dưới 50 chữ), chào mừng khách quay lại và báo tin sản phẩm họ từng mua sắp hết hàng, khuyên họ nên mua dự phòng. 
BẮT BUỘC CHÈN LINK SẢN PHẨM đúng cú pháp: [Tên Sản Phẩm](/user/detail/Mã_Sản_Phẩm). 
Tuyệt đối không bịa ra sản phẩm khác. Xưng hô là 'anh/chị'."""

        url = f"https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={GEMINI_API_KEY}"
        payload = {
            "system_instruction": {"parts": [{"text": system_prompt}]},
            "contents": [{"parts": [{"text": "Hãy tạo tin nhắn gửi khách hàng ngay bây giờ."}]}]
        }
        headers = {'Content-Type': 'application/json'}
        gemini_res = requests.post(url, json=payload, headers=headers)
        
        if gemini_res.status_code == 200:
            res_json = gemini_res.json()
            reply_text = res_json['candidates'][0]['content']['parts'][0]['text']
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
        
        # 1. Lấy mã đơn hàng HOÀN THÀNH gần nhất của user
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
        
        # --- KIỂM TRA CACHE TRƯỚC ---
        cursor.execute("SELECT ds_ma_san_pham, ly_do FROM ai_goi_y_lich_su WHERE ma_don_hang = %s", (ma_don_hang_gan_nhat,))
        cache_row = cursor.fetchone()
        
        if cache_row:
            import json
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
            cursor.close()
            conn.close()
            if suggested_products:
                return jsonify({"success": True, "products": suggested_products, "reason": reason})
        # --- KẾT THÚC KIỂM TRA CACHE ---
        
        # Lấy danh sách SP trong đơn hàng gần nhất đó
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
        
        # 2. Lấy TOÀN BỘ SP đang bán
        query_all = "SELECT ma_san_pham, ten_san_pham, loai_da_phu_hop FROM san_pham WHERE trang_thai = 'dang_ban'"
        cursor.execute(query_all)
        all_prods = cursor.fetchall()
        
        if not all_prods:
            cursor.close()
            conn.close()
            return jsonify({"success": False})
            
        all_str = "\\n".join([f"Mã: {p['ma_san_pham']} | Tên: {p['ten_san_pham']} | Da phù hợp: {p['loai_da_phu_hop']}" for p in all_prods])
        
        # 3. Gửi Prompt cho Gemini
        system_prompt = f"""Đóng vai chuyên gia da liễu cực kỳ chuyên nghiệp.
Đơn hàng gần nhất của khách hàng này bao gồm các món sau: {bought_str}

Danh sách toàn bộ sản phẩm của cửa hàng:
{all_str}

Nhiệm vụ:
1. Đánh giá chu trình Skincare hiện tại của khách dựa vào đơn hàng gần nhất (Họ đang thiếu NHỮNG bước nào quan trọng nhất?).
2. Tìm TỪ 1 ĐẾN 3 mã sản phẩm trong danh sách cửa hàng (chưa được mua trong đơn hàng này) để khuyên họ dùng cho CÁC BƯỚC TIẾP THEO còn thiếu.
3. Trả về đúng định dạng JSON chuẩn (KHÔNG có markdown ```json, KHÔNG có text thừa xung quanh):
{{
    "ma_san_pham_list": [<Mã số 1>, <Mã số 2>],
    "reason": "<Một đoạn văn giải thích chung (khoảng 2-3 câu) vì sao họ nên mua NHỮNG chai này để bổ sung vào các bước họ đang thiếu>"
}}"""

        payload = {
            "system_instruction": {"parts": [{"text": "Bạn phải trả về JSON chuẩn, không thêm bất kỳ ký tự nào khác."}]},
            "contents": [{"parts": [{"text": system_prompt}]}]
        }
        headers = {'Content-Type': 'application/json'}
        
        gemini_res = None
        for key in GEMINI_API_KEYS:
            url = f"https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={key}"
            gemini_res = requests.post(url, json=payload, headers=headers)
            if gemini_res.status_code == 200:
                break
            elif gemini_res.status_code == 429:
                with open("error_log.txt", "a", encoding="utf-8") as f:
                    f.write(f"Key {key[:5]}... hit 429 Rate Limit. Trying next key...\n")
                continue
            else:
                break
                
        if gemini_res and gemini_res.status_code == 200:
            res_json = gemini_res.json()
            reply_text = res_json['candidates'][0]['content']['parts'][0]['text']
            
            # Sử dụng Regex để trích xuất JSON an toàn
            import re
            import json
            try:
                # Tìm chuỗi JSON hợp lệ giữa { và }
                match = re.search(r'\{.*\}', reply_text, re.DOTALL)
                if match:
                    json_str = match.group(0)
                    ai_data = json.loads(json_str)
                else:
                    raise Exception("Không tìm thấy JSON hợp lệ trong phản hồi")
                    
                ma_san_pham_list = ai_data.get("ma_san_pham_list", [])
                reason = ai_data.get("reason")
                
                if not isinstance(ma_san_pham_list, list):
                    ma_san_pham_list = [ma_san_pham_list]
                    
                suggested_products = []
                for ma_sp in ma_san_pham_list:
                    # 4. Truy vấn lấy hình ảnh & giá của SP được chọn
                    # 4. Truy vấn lấy hình ảnh, giá & danh mục của SP được chọn
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
                
                # --- LƯU CACHE ---
                try:
                    cursor.execute("""
                        INSERT INTO ai_goi_y_lich_su (ma_don_hang, ds_ma_san_pham, ly_do)
                        VALUES (%s, %s, %s)
                    """, (ma_don_hang_gan_nhat, json.dumps(ma_san_pham_list), reason))
                    conn.commit()
                except Exception as e:
                    print("Lỗi lưu cache AI:", e)
                # --- KẾT THÚC LƯU CACHE ---

                cursor.close()
                conn.close()
                if suggested_products:
                    return jsonify({"success": True, "products": suggested_products, "reason": reason})
                else:
                    return jsonify({"success": False})
                    
            except Exception as parse_e:
                with open("error_log.txt", "a", encoding="utf-8") as f:
                    f.write(f"Parse error: {parse_e}\nRaw: {reply_text}\n")
                print("Lỗi parse JSON Gemini:", parse_e, "Raw:", reply_text)
                
        else:
            with open("error_log.txt", "a", encoding="utf-8") as f:
                f.write(f"Gemini API Error: {gemini_res.status_code} - {gemini_res.text}\n")
                
        cursor.close()
        conn.close()
        return jsonify({"success": False})
            
    except Exception as e:
        import traceback
        with open("error_log.txt", "a", encoding="utf-8") as f:
            f.write(f"Main Exception: {e}\n{traceback.format_exc()}\n")
        print("Lỗi AI Next Step:", e)
        return jsonify({"success": False})

if __name__ == '__main__':
    app.run(port=5000, debug=True)