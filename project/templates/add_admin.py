import mysql.connector
from mysql.connector import Error
from werkzeug.security import generate_password_hash

# Admin details
first_name = "Sishir"
last_name = "Dangi"
admin_id = "211811"
phone = "9841185811"
email = "sishirdangi83@gmail.com"
password = "sishir2242"

# Hash the password
hashed_password = generate_password_hash(password)

try:
    # Connect to the database
    conn = mysql.connector.connect(
        host='localhost',    # Update with your database host
        user='root',         # Update with your database username
        password='',  # Update with your database password
        database='MeroHajiriStudentData'   # Update with your database name
    )

    if conn.is_connected():
        cursor = conn.cursor()

        # SQL query to insert admin details
        sql = """INSERT INTO admins (first_name, last_name, admin_id, phone, email, password) 
                 VALUES (%s, %s, %s, %s, %s, %s)"""
        values = (first_name, last_name, admin_id, phone, email, hashed_password)

        cursor.execute(sql, values)
        conn.commit()

        print("New admin added successfully")

except Error as e:
    print(f"Error: {e}")

finally:
    if conn.is_connected():
        cursor.close()
        conn.close()
