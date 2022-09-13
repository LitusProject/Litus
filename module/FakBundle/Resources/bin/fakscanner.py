#!/usr/bin/python3
import time
import drivers
import RPi.GPIO as GPIO
import requests

CHECKINS_NEEDED = 10
DEBUGGING = False

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
GPIO.setup(21, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.setup(26, GPIO.OUT)
GPIO.output(26, 0)

try:
    display = drivers.Lcd()
    display.lcd_clear()
    display.lcd_display_string(" Initialiseren..", 1)
    display.lcd_display_string("                ", 2)
except:
    print("Initializing LCD failed. retry?")
    input()
    exit()

display.lcd_clear()
display.lcd_display_string("    Scan je     ", 1)
display.lcd_display_string(" studentenkaart ", 2)

while True:
    studentInfo = input("Scan your Student Card: ")
    if studentInfo == 'STOP':
        exit()

    request = requests.post('https://vtk.be/api/fak/add-checkin', data={"key": "api_key komt hier", "userData": studentInfo}, ).json()

    status = request['status']
    person = request['person']
    amount = request['amount']

    if status == 'error':
        print("No valid checkin")
        display.lcd_clear()
        display.lcd_display_string(person + " was al", 1)
        display.lcd_display_string("ingecheckt", 2)
        time.sleep(1)

        display.lcd_clear()

        display.lcd_display_string("Al " + str(amount) + " checkins!", 2)

        time.sleep(1)

    else:
        isDouble = request['double']

        if isDouble:
            display.lcd_clear()
            display.lcd_display_string("    Dubbele     ", 1)
            display.lcd_display_string("    Checkin!    ", 2)
            time.sleep(1)
        display.lcd_clear()

        if (isDouble and amount % CHECKINS_NEEDED in [0,1]) or (not isDouble and amount % CHECKINS_NEEDED == 0): # Als Veelvoud van 10
            GPIO.output(26, 1) # lamp aan
            counter = 0
            while counter < 500:
                display.lcd_clear()
                display.lcd_display_string("!  Gratis pint !", 1)
                display.lcd_display_string("  " + str(amount) + " checkins!", 2)

                time.sleep(0.01)

                display.lcd_clear()
                display.lcd_display_string("  Gratis pint!", 1)
                display.lcd_display_string("  Voor " + person, 2)

                time.sleep(0.01)
                counter += 1

            GPIO.output(26, 0) # lamp uit
        else: # Geen veelvoud van 10
            display.lcd_display_string("Hallo, " + person, 1)
            display.lcd_display_string("Al " + str(amount) + " checkins!", 2)
            time.sleep(2)

    display.lcd_clear()
    display.lcd_display_string("    Scan je     ", 1)
    display.lcd_display_string(" studentenkaart ", 2)
