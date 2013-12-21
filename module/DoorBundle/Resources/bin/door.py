#!/usr/bin/python

import datetime
import json
import pickle
import urllib, urllib2

# Parameters
API_HOST        = 'http://litus/api/door/getRules'
API_KEY         = ''

CACHE_FILE      = '/tmp/door_rules'
LOG_TIME_FORMAT = '%x %H:%M:%S'

# Globals
now = datetime.datetime.now()
rules = None

# Functions
def allowAccess(identification, academic):
    global now

    print '[' +  now.strftime(LOG_TIME_FORMAT) + '] Opening door for ' + identification

    data = {
        'key'     : API_KEY,
        'academic': academic
    }

def getRules():
    global rules

    data = {
        'key': API_KEY
    }

    try:
        print '[' +  now.strftime(LOG_TIME_FORMAT) + '] Downloading rules'
        rules = json.load(urllib2.urlopen(API_HOST, urllib.urlencode(data)))

        print '[' +  now.strftime(LOG_TIME_FORMAT) + '] Writing rules to cache file'
        cacheFile = open(CACHE_FILE, 'w')
        pickle.dump(rules, cacheFile)
    except Exception:
        print '[' +  now.strftime(LOG_TIME_FORMAT) + '] Reading rules from cache file'
        cacheFile = open(CACHE_FILE, 'r')
        rules = pickle.load(cacheFile)

    cacheFile.close()

# Main
getRules()

while True:
    identification = raw_input('[' +  now.strftime(LOG_TIME_FORMAT) + '] University Identfication: ')

    try:
        rules[identification]
    except KeyError:
        print '[' +  now.strftime(LOG_TIME_FORMAT) + '] No rule for ' + identification
        continue

    if None != rules[identification]['start_date'] and None != rules[identification]['end_date']:
        startDate = datetime.date.fromtimestamp(int(rules[identification]['start_date']))
        endDate = datetime.date.fromtimestamp(int(rules[identification]['end_date']))

        if (startDate < now.date() and endDate > now.date()):
            startTime = int(rules[identification]['start_time'])
            endTime = int(rules[identification]['end_time'])

            if (0 == startTime and 0 == endTime):
                allowAccess(identification, rules[identification]['academic'])
                continue

            if (startTime < now.time().strftime('%H%M') and endTime > now.time().strftime('%H%M')):
                allowAccess(identification, rules[identification]['academic'])
                continue

    allowAccess(identification, rules[identification]['academic'])
