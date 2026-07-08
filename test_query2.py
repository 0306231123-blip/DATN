import mysql.connector
conn = mysql.connector.connect(host='localhost', user='root', password='', database='db_my_pham')
cursor = conn.cursor(dictionary=True)
cursor.execute("SELECT ma_don_hang, ma_nguoi_dung, ngay_dat FROM don_hang WHERE trang_thai_don IN ('giao_thanh_cong', 'hoan_thanh') ORDER BY ngay_dat DESC LIMIT 5")
print(cursor.fetchall())
