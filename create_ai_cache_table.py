import mysql.connector

conn = mysql.connector.connect(host='localhost', user='root', password='', database='db_my_pham')
cursor = conn.cursor()

cursor.execute("""
CREATE TABLE IF NOT EXISTS ai_goi_y_lich_su (
    ma_don_hang INT PRIMARY KEY,
    ds_ma_san_pham VARCHAR(255) NOT NULL,
    ly_do TEXT NOT NULL,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ma_don_hang) REFERENCES don_hang(ma_don_hang) ON DELETE CASCADE
)
""")
conn.commit()
print("Table created successfully.")
cursor.close()
conn.close()
