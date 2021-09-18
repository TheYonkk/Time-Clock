# Time Clock Swipey Swipey

This application uses a MagTek card reader to scan student IDs and clock them into the time clock. Please note that student IDs must be added to the database previously for the application to find a user.

## How to run:

Double click either `run.command` on Mac or `run.bat` on Windows. Please note that once the card reader is plugged in and the application is running, you must make sure that the application is the active window by clicking on it!

## Editable parameters

1) Database connection info. You can find that stuff in the script. Note: this application will only connect to the database when connected to the EGR network!
2) The time to wait before considering a clock-out questionable can be found at the top of the script. For example, it it's set to 5 hours, the script will think that every one trying to clock out past five hours has made a mistake, then will prompt them to fix it. 



