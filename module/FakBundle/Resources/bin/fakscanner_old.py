#!/usr/bin/python3
import sqlite3 as lite
import sys
import time
import drivers
import RPi.GPIO as GPIO
import datetime
import socket
import _thread as thread

CHECKINS_NEEDED=10
DEBUGGING = False
REMOTE_SERVER = "www.google.com"

def is_connected():
  try:
    # see if we can resolve the host name -- tells us if there is
    # a DNS listening
    host = socket.gethostbyname(REMOTE_SERVER)
    # connect to the host -- tells us if the host is actually
    # reachable
    s = socket.create_connection((host, 80), 2)
    return True
  except:
     pass
  return False


GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
GPIO.setup(21,GPIO.IN,pull_up_down=GPIO.PUD_UP)
GPIO.setup(26,GPIO.OUT)
GPIO.output(26,0)

try:
    display = drivers.Lcd()
    display.lcd_clear()
    display.lcd_display_string(" Initialiseren..",1)
    display.lcd_display_string("                ",2)
except:
    print("Initializing LCD failed. retry?")
    input()
    exit()
import gspread
from oauth2client.service_account import ServiceAccountCredentials

scope = ['https://www.googleapis.com/auth/drive	']
creds =ServiceAccountCredentials.from_json_keyfile_name('client_secret.json',scope)
client = gspread.authorize(creds)

sheet = client.open('Klantenkaartsysteem_checkins').sheet1


# thread function for uploading data to google sheet
def upload_checkin(row):
    try:
        gspread.Worksheet.append_row(sheet,row)
    except:
        print("Error uploading")


while not is_connected():
    time.sleep(1)

con = lite.connect('Database.db')
con.text_factory = str
cur = con.cursor()

try:
    cur.execute("CREATE TABLE Checkins(Rnummer TEXT, Timestamp FLOAT, Checkins INTEGER)")
except:
    print("table checkins bestond al")

display.lcd_clear()
display.lcd_display_string("    Scan je     ",1)
display.lcd_display_string(" studentenkaart ",2)

while True:
    Rnummer=input("Rnummer? ")
    if Rnummer=='STOP':
        display.lcd_clear()
        display.lcd_display_string("Uitgeschakeld",1)
        exit()

    now = datetime.datetime.now()
    timestampnow=time.mktime(now.timetuple())
    midday= datetime.datetime(now.year,now.month,now.day,12)
    timestampmidday=time.mktime(midday.timetuple())
    hhourstart = datetime.datetime(now.year,now.month,now.day,22)
    hhourend = datetime.datetime(now.year,now.month,now.day,23)
    hhour = hhourstart < now < hhourend
    #print "Timestampmidday: "+ str(timestampmidday)
    #print "Timestampnow: " + str(timestampnow)

    if timestampmidday>timestampnow:
        timestampmidday-=3600*24
        #print "Timestampmidday updated: " + str(timestampmidday)


    cur.execute("SELECT * FROM Checkins WHERE Rnummer=?",[Rnummer])
    checkin = cur.fetchall()
    valid_checkin=True

    if len(checkin) == 0:
        checkin = [Rnummer, 0, 0]
        cur.execute("INSERT INTO Checkins(Rnummer, Timestamp, Checkins) VALUES('%s', %s, %s)" % (Rnummer, 0, 0))
        con.commit()
    else:
        checkin = checkin[0]

    valid_checkin = False if checkin[1] > timestampmidday and not DEBUGGING else True


    if valid_checkin:
        number_of_checkins=checkin[2]+1 if not hhour else checkin[2]+2

        if hhour:
            display.lcd_clear()
            display.lcd_display_string("    Dubbele     ", 1)
            display.lcd_display_string("    Checkin!    ", 2)
            time.sleep(1)
        display.lcd_clear()

        if(hhour and number_of_checkins%CHECKINS_NEEDED in [0,1] or not hhour and number_of_checkins%CHECKINS_NEEDED == 0): #remainder equals 0, dus om de CHECKINS_NEEDED checkins
            GPIO.output(26,1)       # lamp aan
            while GPIO.input(21)==1:
                display.lcd_clear()
                display.lcd_display_string("!  Gratis pint !",1)
                display.lcd_display_string("  "+str(number_of_checkins)+" checkins!",2)
                counter=0
                while GPIO.input(21)==1 and counter<100:
                    time.sleep(0.01)
                    counter+=1
                display.lcd_clear()
                display.lcd_display_string("  Gratis pint!",1)
                display.lcd_display_string("  Voor "+Rnummer,2)
                counter=0
                while GPIO.input(21)==1 and counter<100:
                    time.sleep(0.01)
                    counter+=1
            GPIO.output(26,0)       # lamp uit
        else:
            display.lcd_display_string("Hallo, "+Rnummer,1)
            if number_of_checkins==1:
                display.lcd_display_string("Reeds "+str(number_of_checkins)+" checkin!",2)
            else:
                display.lcd_display_string("Al "+str(number_of_checkins)+" checkins!",2)
            if DEBUGGING:
                time.sleep(1)
            else:
                time.sleep(2)

        params = (timestampnow, number_of_checkins, Rnummer)
        cur.execute("UPDATE Checkins SET Timestamp = %s, Checkins = %s WHERE Rnummer = '%s';" % params)
        con.commit()

        try:
            thread.start_new_thread(upload_checkin,((Rnummer, timestampnow, number_of_checkins),))
        except:
            print("Error: unable to start thread")
    else:
        print("No valid checkin")
        display.lcd_clear()
        display.lcd_display_string(Rnummer+" was al",1)
        display.lcd_display_string("ingecheckt",2)
        time.sleep(1)

        number_of_checkins=checkin[2]
        display.lcd_clear()
        if number_of_checkins==1:
            display.lcd_display_string("Reeds "+str(number_of_checkins)+" checkin!",2)
        else:
            display.lcd_display_string("Al "+str(number_of_checkins)+" checkins!",2)
        time.sleep(1)

    display.lcd_clear()
    display.lcd_display_string("    Scan je     ",1)
    display.lcd_display_string(" studentenkaart ",2)