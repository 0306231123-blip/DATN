import mysql.connector

try:
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="db_my_pham",
        charset="utf8mb4"
    )
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT DISTINCT loai_da_phu_hop FROM san_pham;")
    for row in cursor.fetchall():
        print(row['loai_da_phu_hop'])
except Exception as e:
    print("Error:", e)
finally:
    if 'conn' in locals() and conn.is_connected():
        cursor.close()
        conn.close()
