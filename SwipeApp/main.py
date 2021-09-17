import getpass as gp
import re
import time
import mysql.connector
from mysql.connector import errorcode
from datetime import datetime, timedelta
import os
import shutil




# if the user swipes and has been clocked in for longer than the amount of time below, ask them if they
# really mean to clock out.
QUESTIONABLE_SHOP_HOURS = timedelta(hours=0, minutes=5, seconds=0, milliseconds=0)

TERMINAL_COLS, TERMINAL_ROWS = shutil.get_terminal_size()


class bcolors:
    HEADER = '\033[95m'
    OKBLUE = '\033[94m'
    OKCYAN = '\033[96m'
    OKGREEN = '\033[92m'
    WARNING = '\033[93m'
    FAIL = '\033[91m'
    ENDC = '\033[0m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'



def main():

    # clear the screen from whatever garbage was on there previously
    os.system('cls' if os.name == 'nt' else 'clear')




    while True:

        # clear the screen
        os.system('cls' if os.name == 'nt' else 'clear')

        # update constants in case window resized
        TERMINAL_COLS, TERMINAL_ROWS = shutil.get_terminal_size()

        # print a header
        print("*" * TERMINAL_COLS)
        print("*" + " " * (TERMINAL_COLS - 2) + "*")
        print("*" + "MSU Formula Racing Time Clock".center(TERMINAL_COLS - 2) + "*")
        print("*" + " " * (TERMINAL_COLS - 2) + "*")
        print("*" + " " * (TERMINAL_COLS - 2) + "*")
        print("*" + "Please swipe your MSU ID to clock in.".center(TERMINAL_COLS - 2) + "*")
        print("*" + " " * (TERMINAL_COLS - 2) + "*")
        print("*" * TERMINAL_COLS)
        data = gp.getpass("")
        apid = get_apid_number(data)


        # prompt again if the card read did not recognize a number
        if apid is None:
            print_beautiful_message("Your card was unreadable. Please try again.", bcolors.FAIL, TERMINAL_COLS)
            time.sleep(2.5)
            continue

        # connect to database
        try:
            cnx = mysql.connector.connect(user='yonkers4', password='gogreen',
                                  host='mysql-user.cse.msu.edu',
                                  database='yonkers4')
        except mysql.connector.Error as err:
            print_beautiful_message("Something went wrong when attempting to connect to the database.", bcolors.FAIL, TERMINAL_COLS)
            continue

        userid, username = get_user_id_from_db(apid, cnx)

        # if no user id found in the database, continue
        if userid == None:
            continue

        # determine if we need to clock in or clock out, then do so
        handle_swipe(userid, username, cnx, TERMINAL_COLS)

        cnx.close()

        # sleep
        time.sleep(3.5)




def print_beautiful_message(error_msg, color_code, cols):

    max_characters_per_line = cols - 2

    lines = [""]

    current_line_length = 0
    current_line_index = 0
    for word in error_msg.split(" "):

        # if we need to start a new line because this word will not fit...
        if current_line_length + len(word) >= max_characters_per_line:
            current_line_index += 1
            lines.append("")
            current_line_length = 0

        # add the word to the line
        addition = " " if current_line_length != 0 else ""
        addition += word
        lines[current_line_index] = lines[current_line_index] + addition
        current_line_length += len(word) + 1


    print(color_code + "*" * cols)
    print("*" + " " * (cols - 2) + "*")

    for line in lines:
        print("*" + bcolors.ENDC + line.center(cols - 2) + color_code + "*")

    print("*" + " " * (cols - 2) + "*")
    print("*" * cols + bcolors.ENDC)


def get_apid_number(data):
    """
    When given input from a card reader, this function will search for the string of numbers following "A" in
    the given user's APID. For example, if your apid is A12345678, the function will return an integer, 12345678.
    Similarly, if the apid starts with a 1, the function will return the bits after the 1. For example, 112345678 will
    also return 12345678.
    @returns Int - the part of the APID following the A (or 1 in newbies case)
    """

    # attempt to find the first ID in the scan
    match = re.search("\^0000000A(\d\d\d\d\d\d\d\d)", data)

    # attempt to find the second ID in the scan if the first ID was not found (card partially corrupted)
    if match is None:
        match = re.search("000000001(\d\d\d\d\d\d\d\d)", data)

    #
    if match is not None:
        return int(match.group(1))
    else:
        return None


def get_user_id_from_db(apid, db_connector):
    """
    Gets the user ID from the database when given the user's APID.
    @param apid - Int, the apid "without the A" as an integer
    @param db_connector - Connector Object, the connector connected to the MySQL database
    """

    cursor = db_connector.cursor(dictionary=True)

    query = ("SELECT `id`,`name` FROM timeclock_user "
             "WHERE `apid` = %s")

    cursor.execute(query, (apid,))

    # look for some sort of error
    if cursor.rowcount == 0:
        print(f"User with APID A{apid} not found in the database!")
        return None
    elif cursor.rowcount > 1:
        print(f"More than one user has an APID A{apid}! There must be an error.")
        return None

    # get the user id from the successful query
    row = cursor.fetchall()[0]
    userid = row["id"]
    name = row["name"]

    cursor.close()

    return userid, name



def handle_swipe(userid, username, db_connector, term_cols):

    cursor = db_connector.cursor(dictionary=True)

    query = "SELECT * FROM timeclock_event WHERE userid = %s ORDER BY `in` DESC LIMIT 1"

    #hire_start = datetime.date(1999, 1, 1)
    #hire_end = datetime.date(1999, 12, 31)

    cursor.execute(query, (userid,))

    if cursor.rowcount == 0:
        print("In")
        return

    row = cursor.fetchall()[0]
    clock_in = row["in"]
    clock_out = row["out"]
    event_id = row["id"]

    # clock out!
    if clock_out is None and datetime.now() - clock_in >= QUESTIONABLE_SHOP_HOURS:

        # clock dat boi in?
        questionable_hours, remainder = divmod((datetime.now() - clock_in).seconds, 3600)
        print_beautiful_message(f"You've been clocked in for over {questionable_hours} hours. If you forgot to clock "
                                f"out last time, contact a system administrator before clocking in. Would you like to "
                                f"continue clocking out?", bcolors.WARNING, term_cols)
        print()  # blank line intentional
        x = input("Type 'Y' to confirm clocking out or press any other key to cancel: ")

        if x.lower() == "y":
            insert_clock_out(event_id, username, cursor, term_cols)
        else:
            print_beautiful_message(f"{username} cancelled the transaction", bcolors.FAIL, term_cols)

    # clock out
    elif clock_out is None:
        # glock u out boiii
        insert_clock_out(event_id, username, cursor, term_cols)

    # clock in
    elif clock_out is not None:
        # mmm clock that boi in!
        insert_clock_in(userid, username, cursor, term_cols)

    db_connector.commit()

    cursor.close()


def insert_clock_in(userid, username, cursor, term_cols):

    query = ("INSERT INTO timeclock_event(userid, `in`) values(%s, %s)")
    cursor.execute(query, (userid, datetime.now()))

    if cursor.getlastrowid() is not None:
        print_beautiful_message(f"{username} was successfully clocked in!", bcolors.OKGREEN, term_cols)


def insert_clock_out(event_id, username, cursor, term_cols):
    query = ("UPDATE timeclock_event SET `out`=%s WHERE `id`=%s")

    cursor.execute(query, (datetime.now(), event_id))

    if cursor.getlastrowid() is not None:
        print_beautiful_message(f"{username} was successfully clocked out!", bcolors.OKGREEN, term_cols)



if __name__ == "__main__":
    main()



