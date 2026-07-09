import re

with open('app.py', 'r', encoding='utf-8') as f:
    text = f.read()

skin_type_route = """
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
        
        system_prompt = f\"\"\"Đóng vai chuyên gia da liễu cực kỳ chuyên nghiệp.
Khách hàng này có loại da: {loai_da_text}.

Nhiệm vụ:
1. Gợi ý TỪ 1 ĐẾN 3 TỪ KHÓA CHUNG (ví dụ: "kiềm dầu", "BHA", "cấp ẩm", "HA", "làm dịu", "Ceramide", "sữa rửa mặt") về các tính năng hoặc thành phần phù hợp nhất cho loại da này.
2. Trả về đúng định dạng JSON chuẩn (KHÔNG có markdown ```json, KHÔNG có text thừa xung quanh):
{{
    "missing_keywords": ["từ khóa 1", "từ khóa 2"],
    "reason": "<Một đoạn văn giải thích chung (khoảng 2-3 câu) vì sao loại da này cần dùng các sản phẩm có tính năng/thành phần này>"
}}\"\"\"

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
                    cursor.execute(\"\"\"
                        SELECT ma_san_pham FROM san_pham 
                        WHERE (ten_san_pham LIKE %s OR mo_ta LIKE %s) 
                        AND trang_thai = 'dang_ban' 
                        LIMIT 1
                    \"\"\", (f"%{keyword}%", f"%{keyword}%"))
                    row = cursor.fetchone()
                    if row and row['ma_san_pham'] not in ma_san_pham_list:
                        ma_san_pham_list.append(row['ma_san_pham'])
                    
                suggested_products = []
                for ma_sp in ma_san_pham_list:
                    cursor.execute(\"\"\"
                        SELECT sp.*, dm.ten_danh_muc 
                        FROM san_pham sp
                        LEFT JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                        WHERE sp.ma_san_pham = %s
                    \"\"\", (ma_sp,))
                    sp_row = cursor.fetchone()
                    if sp_row:
                        # Get images
                        cursor.execute("SELECT duong_dan FROM hinh_anh_san_pham WHERE ma_san_pham = %s LIMIT 1", (ma_sp,))
                        img = cursor.fetchone()
                        sp_row['hinh_anh_url'] = img['duong_dan'] if img else None
                        suggested_products.append(sp_row)
                        
                cursor.close()
                conn.close()
                
                return jsonify({
                    "success": True,
                    "reason": reason,
                    "products": suggested_products,
                    "loai_da_text": loai_da_text
                })
            except Exception as e:
                print("Lỗi parse JSON:", str(e), reply_text)
                cursor.close()
                conn.close()
                return jsonify({"success": False, "message": "Lỗi parse dữ liệu từ AI"}), 500
        else:
            cursor.close()
            conn.close()
            return jsonify({"success": False, "message": "Lỗi gọi OpenRouter API"}), 500
            
    except Exception as e:
        print("Lỗi xử lý API ai-skin-type-suggest:", str(e))
        return jsonify({"success": False, "message": str(e)}), 500

"""

# Append route to app.py
if 'def ai_skin_type_suggest()' not in text:
    text = text.replace("if __name__ == '__main__':", skin_type_route + "\nif __name__ == '__main__':")
    with open('app.py', 'w', encoding='utf-8') as f:
        f.write(text)
    print('Added ai_skin_type_suggest route.')
else:
    print('Route already exists.')
