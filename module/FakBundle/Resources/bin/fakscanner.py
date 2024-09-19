#Author: Jethro Young
import time
import drivers
import RPi.GPIO as GPIO
import requests
import config
import base64

CHECKINS_NEEDED = 10
DEBUG = False
KUL_AUTH_ENDPOINT = 'https://idp.kuleuven.be/auth/realms/kuleuven/protocol/openid-connect/token'
KUL_ID_ENDPOINT = 'https://account.kuleuven.be/api/v1/idverification'
VTK_REG_ENDPOINT = 'https://vtk.be/api/fak/add-checkin-username'

# Connects to the KU Leuven auth service and requests an access token.
# Required to verify student cards from 2022 onwards.
def get_bearer_token():

    # Get id and secret from the config.
    client_id = config.client_id
    client_secret = config.client_secret
    end_point = KUL_AUTH_ENDPOINT
    
    # Combine and encode in base64.
    message = client_id + ":" + client_secret
    message_bytes = message.encode()
    base64_bytes = base64.b64encode(message_bytes)
    base64_message = base64_bytes.decode()

    # Build appropriate headers.
    header = {'Authorization': 'Basic ' + base64_message,
              'Content-Type': 'application/x-www-form-urlencoded'}
    
    # POST request to the auth service.
    try:
        response = requests.post(end_point, headers=header, data={'grant_type': 'client_credentials'})
    except requests.exceptions.ConnectionError as e:
        return False, "Error when retrieving bearer token (no connection found)."
    
    # Only return on success.
    if response.status_code == 200:
        return True, response.json()['access_token']
    
    # If any other error occurred
    elif response.status_code in [401, 403]:
        return False, "Incorrect credentials supplied to the auth service."
    elif response.status_code == 500:
        return False, "The KU Leuven auth service experienced an error."
    return False, f"Error when retrieving bearer token. Status code {response.status_code}"

# Sets the LCD screen to the default message.
def set_default_screen():
    display.lcd_clear()
    display.lcd_display_string("    Scan je     ", 1)
    display.lcd_display_string(" studentenkaart ", 2)

# Sets the LCD to the error message.
# Prepends the given text (must be 9 chars long).
def set_error_screen(text):
    display.lcd_clear()
    display.lcd_display_string(f"{text} FOUT ", 1)
    display.lcd_display_string("PROBEER OPNIEUW ", 2)

# Initialize GPIO pins.
GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
# Set pin 5 to input, default 1.
GPIO.setup(5, GPIO.IN, pull_up_down=GPIO.PUD_UP)
# Set pin 6 to output, default 0.
GPIO.setup(26, GPIO.OUT)
GPIO.output(26, 0)

try:
    # Initialize LCD Driver
    display = drivers.lcd()
    display.lcd_clear()
    display.lcd_display_string(" Initialiseren..", 1)
    display.lcd_display_string("                ", 2)
except:
    # Something went wrong with the LCD driver.
    # Check whether it is correctly installed on the Pi.
    print("Initialization of LCD failed.")
    exit(1)

# Default display.
set_default_screen()


while True:
    # Retrieve scanner input.
    student_info = str(input("Scan your Student Card: "))
    
    # Admin override to exit program.
    if student_info == "STOP":
        exit(0)

    # Get the bearer token from the auth service.
    success, result = get_bearer_token()
    
    # Bearer token not retrieved. Print error and restart.
    if not success:
        print(result)
        set_error_screen("KU LEUVEN")
        time.sleep(1)
        set_default_screen()
        continue

    # NOTE: This is a basic check which verifies if the student card is in the correct format.
    # Currently checks for <any sequence>;<any sequence>
    # You can upgrade this by importing the re package and matching it to the exact regular expr.
    # But if it works it works he
    student_info = student_info.split(';')
    if len(student_info) != 2:
        print("Incorrect student card id. The card is either old or did not register correctly.")
        set_error_screen("    KAART")
        time.sleep(1)
        set_default_screen()
        continue
    card_app_id = student_info[1]
    serial = student_info[0]

    # Get link and set headers and body for request.
    url = KUL_ID_ENDPOINT
    headers = {'Authorization': 'Bearer ' + result,
                'Content-Type': 'application/json'}
    body = '{"cardAppId": "' + card_app_id + '","serialNr": "' + serial + '"}'
    
    # Identify with KU Leuven.
    try:
        response_ku_leuven = requests.post(url, headers=headers, data=body)
    except requests.exceptions.ConnectionError as e:
        print("Connection failed to the KU Leuven service. Do you have internet?")
        set_error_screen("  NETWERK")
        time.sleep(1)
        set_default_screen()
        continue
    if response_ku_leuven.status_code != 200:
        if response_ku_leuven.status_code == 404:
            print("The student card ID was not recognized.")
            set_error_screen("    KAART")
        else:
            print(f"The KU Leuven identification service did not work properly. Status code {response_ku_leuven.status_code}")
            set_error_screen("KU LEUVEN")
        time.sleep(1)
        set_default_screen()
        continue

    # Retrieve username from response.
    try:
        user_name = response_ku_leuven.json()['userName']
    except Exception:
        print("Error when decoding KU Leuven response to JSON.")
        
        time.sleep(1)
        set_default_screen()
        continue

    # Register username with VTK site for points.
    # NOTE: Shouldn't this key be kept in a config somewhere?
    try:
        response = requests.post(VTK_REG_ENDPOINT, data={
            "key": "f6b22f8fd6ec95d8781d2abe867f2a4d",
            "userName": user_name
        })
    except requests.exceptions.ConnectionError as e:
        print("Connection failed to VTK site. Do you have internet?")
        set_error_screen("  NETWERK")
        time.sleep(1)
        set_default_screen()
        continue
    if response.status_code != 200:
        print(f"The request to connect to the VTK site was not handled correctly. Status code {response.status_code}")
        set_error_screen(" VTK SITE")
        time.sleep(1)
        set_default_screen()
        continue

    # Retrieve details from response.
    try:
        user_details = response.json()
    except Exception:
        print("Error when decoding VTK response to JSON")
        set_error_screen(" VTK SITE")
        time.sleep(1)
        set_default_screen()
        continue
    
    status = user_details['status']
    amount = user_details['amount']
    person = user_name

    # Error status: Person was already checked in.
    if status == 'error':
        display.lcd_clear()
        display.lcd_display_string(f"{person} was al", 1)
        display.lcd_display_string("ingecheckt", 2)
        time.sleep(1)

        display.lcd_clear()
        display.lcd_display_string(f"Al {str(amount)} checkins!", 2)
        time.sleep(1)
        set_default_screen()
    # OK status. Person's points were recorded.
    else:

        # Check if double points is active.
        is_double = user_details['double']
        if is_double:
            display.lcd_clear()
            display.lcd_display_string("    Dubbele     ", 1)
            display.lcd_display_string("    Checkin!    ", 2)
            time.sleep(1)
        display.lcd_clear()

        # Check if points is a multiple of 10.
        if (is_double and amount % CHECKINS_NEEDED in [0,1]) \
            or (not is_double and amount % CHECKINS_NEEDED == 0):
            # Turn On The Light by Fred Again...
            GPIO.output(26, 1)
            # Display GRATIS PINT if pin 5 is 1 (this is the default case).
            for i in range(6):
                display.lcd_clear()
                display.lcd_display_string("! GRATIS PINT !", 1)
                display.lcd_display_string(f" {str(amount)} checkins!", 2)

                time.sleep(1)
                display.lcd_clear()
                display.lcd_display_string("! GRATIS PINT !", 1)
                display.lcd_display_string(f" Voor {person}", 2)

                time.sleep(2)
            GPIO.output(26, 0)
        # Points are not a multiple of 10 ;(
        # This person needs to visit the fak more!
        else:
            display.lcd_display_string(f"Hallo, {person}", 1)
            display.lcd_display_string(f"Al {str(amount)} checkins!", 2)
            time.sleep(2)

        # Reset screen to default.
        set_default_screen()
    
    

