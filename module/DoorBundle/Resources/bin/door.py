#!/usr/bin/python

#
# Door
# @author Pieter Maene <pieter.maene@litus.cc>
#

import datetime
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
rules = None

# Functions
def allowAccess(identification, academic):
    log('Opening door for ' + identification)
    openDoor()

    data = {
        'key'     : API_KEY,
        'academic': academic
    }

    try:
        log('Logging access to the server')
        result = requests.post(API_HOST + '/api/door/log', data = data).json()

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
getRules()

while True:
    identification = raw_input('[' + datetime.datetime.now().strftime(LOG_TIME_FORMAT) + '] University Identfication: ')

    try:
        rules[identification]
    except KeyError:
        log('No rule for ' + identification)
        continue

    if None != rules[identification]['start_date'] and None != rules[identification]['end_date']:
        startDate = datetime.date.fromtimestamp(int(rules[identification]['start_date']))
        endDate = datetime.date.fromtimestamp(int(rules[identification]['end_date']))

        now = datetime.datetime.now()
        if startDate < now.date() and endDate > now.date():
            startTime = int(rules[identification]['start_time'])
            endTime = int(rules[identification]['end_time'])

            if 0 == startTime and 0 == endTime:
                allowAccess(identification, rules[identification]['academic'])
                continue

            if startTime < now.time().strftime('%H%M') and endTime > now.time().strftime('%H%M'):
                allowAccess(identification, rules[identification]['academic'])
                continue

    allowAccess(identification, rules[identification]['academic'])

    if path.getmtime(CACHE_FILE) < time.time()-CACHE_TTL:
        getRules()
