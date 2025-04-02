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
# @author Tiddo Nees <tiddo.nees@vtk.be>

import datetime
import requests
from requests.exceptions import ConnectionError, Timeout, RequestException
import time
import logging
import sys
import json
import os
import threading
from logging.handlers import RotatingFileHandler

##################
# CONSTANTS
##################

# The pin on the pi used for control of the lock.
GPIO_PORT = 7

# API settings
API_HOST = 'https://vtk.be'
API_KEY = 'fed78ef862ed4000bedc1927ba1b44aa'

# Caching (in case the internet connection fails).
CACHE_FILE = './door_rules'
CACHE_TTL = 3600  # Extended to 1 hour for better offline support
BACKUP_CACHE_FILE = './door_rules_backup'  # Backup cache file

# Logging of entry
LOG_FILE = './door.log'
LOG_TIME_FORMAT = '%x %H:%M:%S'
MAX_LOG_SIZE = 5 * 1024 * 1024  # 5MB
BACKUP_COUNT = 3  # Keep 3 backup log files

# Request timeouts
REQUEST_TIMEOUT = 5  # seconds

# Set to true when debugging on a non-RPi.
DEBUG = False

# Add to CONSTANTS section
DAILY_REFRESH_INTERVAL = 24 * 60 * 60  # 24 hours in seconds
LAST_FULL_REFRESH_FILE = './last_refresh_time'

##################
# INITIALIZATION
##################

# Setup GPIO for RPi
if not DEBUG:
    try:
        import RPi.GPIO as GPIO

        GPIO.setmode(GPIO.BOARD)
        GPIO.setwarnings(False)
        GPIO.setup(GPIO_PORT, GPIO.OUT)
        # Initialize to locked state
        GPIO.output(GPIO_PORT, GPIO.LOW)
    except ImportError:
        print("Failed to import RPi.GPIO. Running in fallback mode.")
        DEBUG = True
    except Exception as e:
        print(f"GPIO initialization error: {e}. Running in fallback mode.")
        DEBUG = True

# Initialize logging
try:
    handler = RotatingFileHandler(
        LOG_FILE,
        maxBytes=MAX_LOG_SIZE,
        backupCount=BACKUP_COUNT
    )
    logging.basicConfig(
        level=logging.INFO,
        format='[%(asctime)s] %(levelname)s: %(message)s',
        datefmt=LOG_TIME_FORMAT,
        handlers=[
            handler,
            logging.StreamHandler(sys.stdout)
        ]
    )
except Exception as e:
    # Fallback to basic logging if rotation fails
    print(f"Error setting up rotating log: {e}. Using basic logging.")
    logging.basicConfig(
        level=logging.INFO,
        format='[%(asctime)s] %(levelname)s: %(message)s',
        datefmt=LOG_TIME_FORMAT,
        handlers=[
            logging.FileHandler(LOG_FILE),
            logging.StreamHandler(sys.stdout)
        ]
    )

# Initialize global variables
identification = ''


##################
# FUNCTIONS
##################

# Set GPIO pins to open the door with retry logic
def open_door():
    if DEBUG:
        log("DEBUG MODE: Door would open now")
        return True

    for attempt in range(3):  # Try up to 3 times
        try:
            GPIO.output(GPIO_PORT, GPIO.HIGH)
            time.sleep(5)
            GPIO.output(GPIO_PORT, GPIO.LOW)
            return True
        except Exception as e:
            log(f"Door opening error (attempt {attempt + 1}/3): {e}")
            time.sleep(1)  # Short delay before retry

    log("CRITICAL: Failed to open door after multiple attempts")
    return False


# Open the door and log to file+Litus
def allow(identification, academic):
    # Write to log file
    log(f"Opening door for {identification}")

    # Make RPi open the door
    door_opened = open_door()
    if door_opened:
        log("Door operated successfully")
    else:
        log("ALERT: Door operation failed, please check hardware")

    # Log the entry to Litus in a separate thread to not block door operation
    threading.Thread(
        target=log_access_to_server,
        args=(academic,),
        daemon=True
    ).start()


# Log access to server without blocking
def log_access_to_server(academic):
    params = {
        'key': API_KEY,
        'academic': academic
    }
    try:
        result = requests.post(
            API_HOST + '/api/door/log',
            data=params,
            timeout=REQUEST_TIMEOUT
        )

        if result.status_code == 200:
            try:
                result_json = result.json()
                if 'success' == result_json.get('status'):
                    log('Log entry was successfully created')
                else:
                    log(f"Server returned error: {result_json.get('message', 'Unknown error')}")
            except json.JSONDecodeError:
                log("Could not parse server response for logging")
        else:
            log(f"Creating log on server returned HTTP{result.status_code}. Log not created")
    except Exception as e:
        log(f"Error logging access to server: {e}")


# Save cache with backup
def save_to_cache(user_id, user_data):
    try:
        # Load existing cache or create new
        cache_data = {}
        if os.path.exists(CACHE_FILE):
            with open(CACHE_FILE, 'r') as f:
                try:
                    cache_data = json.load(f)
                except json.JSONDecodeError:
                    log("Invalid cache file, creating new cache")
                    # Try to restore from backup
                    if os.path.exists(BACKUP_CACHE_FILE):
                        try:
                            with open(BACKUP_CACHE_FILE, 'r') as backup:
                                cache_data = json.load(backup)
                            log("Restored cache from backup")
                        except:
                            log("Backup cache also corrupted")

        # Add or update user data with timestamp
        cache_data[user_id.lower()] = {
            'timestamp': time.time(),
            'data': user_data
        }

        # First save to backup, then to main file
        with open(BACKUP_CACHE_FILE, 'w') as f:
            json.dump(cache_data, f)

        with open(CACHE_FILE, 'w') as f:
            json.dump(cache_data, f)

    except Exception as e:
        log(f"Error saving to cache: {e}")


# Get from cache with fallback to backup
def get_from_cache(user_id):
    cache_files = [CACHE_FILE, BACKUP_CACHE_FILE]

    for file in cache_files:
        if not os.path.exists(file):
            continue

        try:
            with open(file, 'r') as f:
                cache_data = json.load(f)

            user_cache = cache_data.get(user_id.lower())
            if not user_cache:
                continue

            # Check if cache is still valid
            if time.time() - user_cache['timestamp'] <= CACHE_TTL:
                if file == BACKUP_CACHE_FILE:
                    log("Using backup cache file")
                return user_cache['data']
        except Exception as e:
            log(f"Error reading from cache file {file}: {e}")

    return None


# Authorize with robust error handling
def authorize(identification):

    # Then check cache
    cached_data = get_from_cache(identification)
    if cached_data:
        try:
            person = cached_data.get('person', identification)
            academic_id = cached_data.get('academic', '0')
            is_allowed = cached_data.get('is_allowed', False)
            log(f"Using cached data for {person}")

            if is_allowed:
                allow(person, academic_id)
            else:
                log(f'{person} not allowed (from cache)')

            # Try to update cache in background
            threading.Thread(
                target=refresh_cache,
                args=(identification,),
                daemon=True
            ).start()
            return
        except Exception as e:
            log(f"Error processing cached data: {e}")

    # If no valid cache, try the API
    try:
        response = requests.post(
            API_HOST + '/api/door/is-allowed',
            data={
                "key": API_KEY,
                "userData": identification.lower()
            },
            timeout=REQUEST_TIMEOUT
        )

        if response.status_code == 200:
            try:
                result = response.json()
                person = result.get('person', identification)
                academic_id = result.get('academic', '0')
                is_allowed = result.get('is_allowed', False)

                # Cache the result
                save_to_cache(identification, result)

                if is_allowed:
                    allow(person, academic_id)
                else:
                    log(f'{person} not allowed')
            except json.JSONDecodeError:
                log("Could not parse server response")
        else:
            log(f"Server returned HTTP{response.status_code}. Identification failed")
            if cached_data:  # Fall back to cache if API call fails
                log("Falling back to expired cache as API call failed")
                person = cached_data.get('person', identification)
                academic_id = cached_data.get('academic', '0')
                is_allowed = cached_data.get('is_allowed', False)

                if is_allowed:
                    log("WARNING: Using expired cache data")
                    allow(person, academic_id)
                else:
                    log(f'{person} not allowed (from expired cache)')

    except (ConnectionError, Timeout) as e:
        log(f"Connection error: {e}")
        if cached_data:  # Use cache if available, even if expired
            log("Network error - using available cache data")
            person = cached_data.get('person', identification)
            academic_id = cached_data.get('academic', '0')
            is_allowed = cached_data.get('is_allowed', False)

            if is_allowed:
                log("WARNING: Using cache due to network error")
                allow(person, academic_id)
            else:
                log(f'{person} not allowed (from cache)')
        else:
            log("No cached data available and cannot reach server")
    except Exception as e:
        log(f"Unexpected error in authorization: {e}")


# Update cache in background
def refresh_cache(identification):
    try:
        response = requests.post(
            API_HOST + '/api/door/is-allowed',
            data={
                "key": API_KEY,
                "userData": identification.lower()
            },
            timeout=REQUEST_TIMEOUT
        )

        if response.status_code == 200:
            result = response.json()
            save_to_cache(identification, result)
            log(f"Cache refreshed for {result.get('person', identification)}")
    except Exception as e:
        log(f"Background cache refresh failed: {e}")


# Add these new functions after refresh_cache function

def refresh_all_cached_users():
    """Check all cached users against the server and update their access status."""
    try:
        # Load existing cache
        if not os.path.exists(CACHE_FILE) and not os.path.exists(BACKUP_CACHE_FILE):
            log("No cache files found to refresh")
            return

        cache_data = {}
        for cache_file in [CACHE_FILE, BACKUP_CACHE_FILE]:
            if os.path.exists(cache_file):
                try:
                    with open(cache_file, 'r') as f:
                        cache_data = json.load(f)
                    if cache_data:
                        break
                except json.JSONDecodeError:
                    log(f"Invalid cache file: {cache_file}")

        if not cache_data:
            log("Empty or invalid cache files, nothing to refresh")
            return

        log(f"Starting daily refresh of {len(cache_data)} cached users")
        updated_count = 0

        # Process each user in the cache
        for user_id, cache_entry in list(cache_data.items()):
            try:
                # Check this user's access status
                response = requests.post(
                    API_HOST + '/api/door/is-allowed',
                    data={
                        "key": API_KEY,
                        "userData": user_id.lower()
                    },
                    timeout=REQUEST_TIMEOUT
                )

                if response.status_code == 200:
                    result = response.json()
                    # Update cache with new data
                    cache_data[user_id] = {
                        'timestamp': time.time(),
                        'data': result
                    }
                    updated_count += 1

                    # Log significant changes
                    old_allowed = cache_entry.get('data', {}).get('is_allowed', False)
                    new_allowed = result.get('is_allowed', False)
                    if old_allowed != new_allowed:
                        person = result.get('person', user_id)
                        if new_allowed:
                            log(f"Access GRANTED for {person} (was previously denied)")
                        else:
                            log(f"Access REVOKED for {person} (was previously granted)")

                time.sleep(1)  # Prevent overloading the server

            except Exception as e:
                log(f"Error refreshing user {user_id}: {e}")
                continue

        # Save updated cache
        with open(BACKUP_CACHE_FILE, 'w') as f:
            json.dump(cache_data, f)

        with open(CACHE_FILE, 'w') as f:
            json.dump(cache_data, f)

        # Update last refresh time
        with open(LAST_FULL_REFRESH_FILE, 'w') as f:
            f.write(str(int(time.time())))

        log(f"Daily refresh completed. Updated {updated_count}/{len(cache_data)} users.")

    except Exception as e:
        log(f"Error during cache refresh: {e}")


def is_daily_refresh_needed():
    """Check if it's time for a daily refresh of the cache."""
    try:
        if not os.path.exists(LAST_FULL_REFRESH_FILE):
            return True

        with open(LAST_FULL_REFRESH_FILE, 'r') as f:
            last_refresh = int(f.read().strip())

        # Check if enough time has passed since last refresh
        return (time.time() - last_refresh) >= DAILY_REFRESH_INTERVAL
    except Exception as e:
        log(f"Error checking refresh time: {e}")
        return True  # If there's an error, better to refresh


def background_refresh_daemon():
    """Background thread that periodically refreshes all cached users."""
    while True:
        try:
            if is_daily_refresh_needed():
                log("Starting scheduled daily refresh of all cached users")
                refresh_all_cached_users()

            # Sleep for a while before checking again
            time.sleep(3600)  # Check every hour if refresh is needed

        except Exception as e:
            log(f"Error in background refresh thread: {e}")
            time.sleep(3600)  # Wait before retrying

# Log function
def log(message):
    try:
        logging.info(message)
    except Exception as e:
        print(f"[{datetime.datetime.now().strftime(LOG_TIME_FORMAT)}] {message}")
        print(f"Logging error: {e}")


##################
# MAIN
##################

def main():
    log("Door access system starting up")
    try:
        # Start background refresh thread
        refresh_thread = threading.Thread(
            target=background_refresh_daemon,
            daemon=True
        )
        refresh_thread.start()
        log("Background refresh daemon started")

        identification = ""
        while identification != "STOP":
            try:
                # Get input from keyboard with timeout
                identification = input().strip()
                if identification:
                    # Authorize input
                    authorize(identification)
            except KeyboardInterrupt:
                log("Keyboard interrupt received, shutting down")
                break
            except Exception as e:
                log(f"Error processing input: {e}")
                time.sleep(1)  # Prevent tight loop on error

    except Exception as e:
        log(f"Critical error in main loop: {e}")
    finally:
        log("Door access system shutting down")
        if not DEBUG:
            try:
                GPIO.cleanup()
            except:
                pass

if __name__ == "__main__":
    main()