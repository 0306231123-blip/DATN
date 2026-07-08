import mysql.connector

conn = mysql.connector.connect(host='localhost', user='root', password='', database='db_my_pham')
cursor = conn.cursor(dictionary=True)
user_id = 5

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

print("Recent order:", recent_order)

ma_don_hang_gan_nhat = recent_order['ma_don_hang']
print("ma_don_hang_gan_nhat:", ma_don_hang_gan_nhat)

query_bought = """
    SELECT DISTINCT p.ten_san_pham
    FROM san_pham p
    JOIN chi_tiet_don_hang ctdh ON p.ma_san_pham = ctdh.ma_san_pham
    WHERE ctdh.ma_don_hang = %s
"""
cursor.execute(query_bought, (ma_don_hang_gan_nhat,))
bought = cursor.fetchall()
print("Bought:", bought)
