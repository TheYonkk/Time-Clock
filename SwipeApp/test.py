import mysql.connector
from mysql.connector import errorcode

try:
    cnx = mysql.connector.connect(user='yonkers4', password='gogreen',
                                  host='mysql-user.cse.msu.edu',
                                  database='yonkers4')

except mysql.connector.Error as err:
    if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
        print("Something is wrong with your user name or password")
    elif err.errno == errorcode.ER_BAD_DB_ERROR:
        print("Database does not exist")
    else:
        print(err)
# else:
#     cnx.close()
#
#
cursor = cnx.cursor()
#
query = ("SELECT * FROM timeclock_user;")

#hire_start = datetime.date(1999, 1, 1)
#hire_end = datetime.date(1999, 12, 31)

cursor.execute(query, ())

print(cursor.fetchall())

cursor.close()
cnx.close()