#! /usr/bin/python

#
# Door
#
# Original
# @author Pieter Maene <pieter.maene@litus.cc>
# @author Kristof Marien <kristof.marien@litus.cc>
#
# Since 2023
# @author Jethro Young <Jethrong2@gmail.com>
# @author Robbe Serry <robbe.serry@vtk.be>

import datetime
import requests
from requests.exceptions import ConnectionError
import time
import logging
import sys

##################
# CONSTANTS
##################

# The pin on the pi used for control of the lock.
GPIO_PORT       = 15

# API settings
API_HOST        = ''
API_KEY         = ''

# Caching (in case the internet connection fails).
CACHE_FILE      = './door_rules'
CACHE_TTL       = 900

# Logging of entry
LOG_FILE        = './door.log'
LOG_TIME_FORMAT = '%x %H:%M:%S'

# Set to true when debugging on a non-RPi.
DEBUG = True

##################
# INITIALIZATION
##################

# Setup GPIO for RPi
if not DEBUG:
    import RPi.GPIO as GPIO
    GPIO.setmode(GPIO.BOARD)
    GPIO.setup(GPIO_PORT, GPIO.OUT)

# Initialize global variables.
identification = ''
rules = None

logging.basicConfig(
    level=logging.INFO,
    handlers=[
        logging.FileHandler(LOG_FILE),
        logging.StreamHandler(sys.stdout)
    ]
)

##################
# FUNCTIONS
##################

# Set GPIO pins to open the door.
def open_door():
    if not DEBUG:
        GPIO.output(GPIO_PORT, GPIO.HIGH)
        time.sleep(2)
        GPIO.output(GPIO_PORT, GPIO.LOW)

# Open the door and log to file+Litus.
def allow(identification, academic):

    # Write to log file.
    log('Opening door for ' + identification)
    # Make RPi open the door.
    open_door()

    # Log the entry to Litus.
    params = {
        'key'     : API_KEY,
        'academic': academic
    }
    try:
        log('Logging access to the server')
        result = requests.post(API_HOST + '/api/door/log', data=params)
    except ConnectionError:
        log("Could not connect to Litus. Log entry not created.")
    
    # Status OK.
    if result.status_code == 200:
        # Convert to JSON and check OK from backend.
        result_json = result.json()
        if 'success' == result_json['status']:
            log('Log entry was successfully created.')
    # Status Bad Request, Server Error, etc.
    else:
        log(f"Creating log on Litus returned HTTP{result.status_code}. Log not created.")

# Authorize card against Litus.
def authorize(identification):
    try:
        response = requests.post(
            API_HOST + '/api/door/is-allowed',
            data={
                "key": API_KEY,
                "userData": identification.lower()
            }
        )
    except ConnectionError:
        # No internet
        log("Could not connect to Litus. Failed to identify card.")

    if response.status_code == 200:
        person = response.json()['person']
        academic_id = response.json()['academic']
        is_allowed = response.json()['is_allowed']
        if is_allowed:
            allow(person, academic_id)
        else:
            log(f'{person} not allowed.')
    # Status Bad Request, Server Error, etc.
    else:
        log(f"Identifying person with Litus returned HTTP{response.status_code}. Identification failed.")
        log(response.text)


# Log to file (and console)
def log(message):
    line = f"[{datetime.datetime.now().strftime(LOG_TIME_FORMAT)}] {message}"
    logging.info(line)

##################
# MAIN
##################

identification = ""
while identification != "STOP":
    # Get input from keyboard.
    identification = input()
    # Authorize input.
    authorize(identification)
