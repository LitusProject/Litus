#! /usr/bin/python

#
# Door
#
# @author Pieter Maene <pieter.maene@litus.cc>
# @author Kristof Marien <kristof.marien@litus.cc>
#

import datetime
import evdev
import json
import os.path as path
import pickle
import requests
import time

import RPi.GPIO as GPIO

# Parameters
GPIO_PORT       = 15

API_HOST        = 'http://litus'
API_KEY         = ''

CACHE_FILE      = '/tmp/door_rules'
CACHE_TTL       = 900
LOG_FILE        = '/tmp/door.log'
LOG_TIME_FORMAT = '%x %H:%M:%S'

# GPIO
GPIO.setmode(GPIO.BOARD)
GPIO.setup(GPIO_PORT, GPIO.OUT)

# Globals
identification = ''
rules = None

# Functions
def allowAccess(identification, academic):
    log('Opening door for ' + identification)
    openDoor()

    params = {
        'key'     : API_KEY,
        'academic': academic
    }

    try:
        log('Logging access to the server')
        result = requests.post(API_HOST + '/api/door/log', data = params).json()

        if 'success' == result['status']:
            log('Log entry was successfully created')
    except Exception:
        log('Log entry could not be created')

def getRules():
    global rules

    data = {
        'key': API_KEY
    }

    try:
        log('Downloading rules')
        rules = requests.post(API_HOST + '/api/door/getRules', data = data).json()

        log('Writing rules to cache file')
        cacheFile = open(CACHE_FILE, 'w')
        pickle.dump(rules, cacheFile)
    except Exception:
        log('Reading rules from cache file');
        cacheFile = open(CACHE_FILE, 'r')
        rules = pickle.load(cacheFile)

    cacheFile.close()

def input(identification_data):
    identification = requests.post('https://vtk.be/api/door/get-username', data={"key": API_KEY, "userData": identification}, ).json()['person']
    try:
        rules[identification]
    except KeyError:
        log('No rule for ' + identification)

    if isinstance(rules[identification], list) == False or len(rules[identification]) < 1:
        log('No rule for ' + identification)

    accessAllowed = False
    for rule in rules[identification]:
        if rule['start_date'] == None and rule['end_date'] == None:
            accessAllowed = True
            break
        else:
            startDate = datetime.date.fromtimestamp(int(rule['start_date']))
            endDate = datetime.date.fromtimestamp(int(rule['end_date']))

            now = datetime.datetime.now()
            if startDate < now.date() and endDate > now.date():
                startTime = int(rule['start_time'])
                endTime = int(rule['end_time'])

                if 0 == startTime and 0 == endTime:
                    accessAllowed = True
                    break

                if startTime < now.time().strftime('%H%M') and endTime > now.time().strftime('%H%M'):
                    accessAllowed = True
                    break

    if accessAllowed == True:
        allowAccess(identification, rule['academic'])

    if path.getmtime(CACHE_FILE) < time.time()-CACHE_TTL:
        getRules()

# TODO Use built-in logger
def log(message):
    line = '[' +  datetime.datetime.now().strftime(LOG_TIME_FORMAT) + '] ' + message
    try:
        file = open(LOG_FILE, 'a')
        try:
            file.write(line + '\n')
        finally:
            file.close()
    except IOError:
        pass

    print line

def openDoor():
    GPIO.output(GPIO_PORT, GPIO.HIGH)
    time.sleep(2)
    GPIO.output(GPIO_PORT, GPIO.LOW)

# Main
# TODO Call this every hour
getRules()

device = evdev.InputDevice('/dev/input/event0')
for event in device.read_loop():
    if event.type == evdev.ecodes.EV_KEY:
        if event.value == 1:
            continue

        if event.code == 42:
            continue

        if event.code == 28:
            input(identification)
            identification = ''

            continue

        identification = identification + (format(evdev.ecodes.KEY[event.code]).decode()[-1:]).lower()
