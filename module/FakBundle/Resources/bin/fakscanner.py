#!/usr/bin/python3
import time
import drivers
import RPi.GPIO as GPIO
import requests
import config
import base64

CHECKINS_NEEDED = 10
DEBUGGING = False

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
GPIO.setup(5, GPIO.IN, pull_up_down=GPIO.PUD_UP)
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

def get_bearer_token():
    client_id = config.client_id
    client_secret = config.client_secret
    end_point = 'https://idp.kuleuven.be/auth/realms/kuleuven/protocol/openid-connect/token'
    message = client_id + ":" + client_secret
    message_bytes = message.encode()
    base64_bytes = base64.b64encode(message_bytes)
    base64_message = base64_bytes.decode()

    header = {'Authorization': 'Basic ' + base64_message,
              'Content-Type': 'application/x-www-form-urlencoded'}
    request = requests.post(end_point, headers=header, data={'grant_type': 'client_credentials'})
    return request.json()['access_token']


while True:
    studentInfo = input("Scan your Student Card: ")
    if studentInfo == 'STOP':
        exit()

    try:
        bearer_token = get_bearer_token()

        try:
            studentInfo = studentInfo.split(';')
            cardAppId = studentInfo[1]
            serialNr = studentInfo[0]

            url = 'https://account.kuleuven.be/api/v1/idverification'
            headers = {'Authorization': 'Bearer ' + bearer_token,
                       'Content-Type': 'application/json'}
            body = '{"cardAppId": "' + cardAppId + '","serialNr": "' + serialNr + '"}'

            try:
                r = requests.post(url, headers=headers, data=body)

                user_name = r.json()['userName']


                try:
                    request = requests.post('https://vtk.be/api/fak/add-checkin-username', data={
                        "key": "f6b22f8fd6ec95d8781d2abe867f2a4d", "userName": user_name}, ).json()

                    status = request['status']
                    amount = request['amount']
                    person = user_name

                    if status == 'error':
                        display.lcd_display_string(person + " was al", 1)
                        display.lcd_display_string("ingecheckt", 2)
                        time.sleep(1)

                        display.lcd_clear()

                        display.lcd_display_string(
                            "Al " + str(amount) + " checkins!", 2)

                        time.sleep(1)

                    else:
                        isDouble = request['double']

                        if isDouble:
                            display.lcd_clear()
                            display.lcd_display_string("    Dubbele     ", 1)
                            display.lcd_display_string("    Checkin!    ", 2)
                            time.sleep(1)
                        display.lcd_clear()

                        # Als Veelvoud van 10
                        if (isDouble and amount % CHECKINS_NEEDED in [0, 1]) or (
                                not isDouble and amount % CHECKINS_NEEDED == 0):
                            GPIO.output(26, 1)  # lamp aan
                            while GPIO.input(5) == 1:
                                display.lcd_clear()
                                display.lcd_display_string("!  Gratis pint !", 1)
                                display.lcd_display_string(
                                    "  " + str(amount) + " checkins!", 2)

                                counter = 0
                                while (GPIO.input(5) == 1 and counter < 100):
                                    time.sleep(0.01)
                                    counter += 1

                                display.lcd_clear()
                                display.lcd_display_string("  Gratis pint!", 1)
                                display.lcd_display_string("  Voor " + person, 2)

                                counter = 0
                                while GPIO.input(5) == 1 and counter < 100:
                                    time.sleep(0.01)
                                    counter += 1
                            GPIO.output(26, 0)  # lamp uit
                        else:  # Geen veelvoud van 10
                            display.lcd_display_string("Hallo, " + person, 1)
                            display.lcd_display_string(
                                "Al " + str(amount) + " checkins!", 2)
                            time.sleep(2)


                except Exception as e:
                    print("In vierde except")
                    print(e)
                    display.lcd_clear()
                    display.lcd_display_string("Error")
                    display.lcd_display_string("Scan again")

            except Exception as e:
                print("In derde except")
                print(e)
                display.lcd_clear()
                display.lcd_display_string("Error")
                display.lcd_display_string("Scan again")

        except Exception as e:
            print("In tweede except")
            print(e)
            display.lcd_clear()
            display.lcd_display_string("Error")
            display.lcd_display_string("Scan again")

    except Exception as e:
        print("In eerste except")
        print(e)
        display.lcd_clear()
        display.lcd_display_string("Error")
        display.lcd_display_string("Scan again")

    display.lcd_clear()
    display.lcd_display_string("    Scan je     ", 1)
    display.lcd_display_string(" studentenkaart ", 2)
