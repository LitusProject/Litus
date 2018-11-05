#!/usr/bin/env bash

#
# sockets.sh
# @author Bram Gotink <bram.gotink@litus.cc>
#

# allow job control
set -m

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY"; cd ..;

# check litus configuration
if ! php bin/console.php common:config test socket_path || ! php bin/console.php common:config test socket_log; then
    echo "Cannot get socket info, is litus configured correctly?" >&2
    exit 4
fi

# log function
if [[ "${1:-n}" =~ -d|--daemon ]]; then
    _LOGFILE=$(php bin/console.php common:config get socket_log)
    mkdir -p $(dirname $_LOGFILE)
else
    _LOGFILE=0
fi

log() {
    if [[ "$_LOGFILE" = "0" ]]; then
        echo "[$(date +'%Y-%m-%d %H:%M:%S')]" "$@" >&2
    else
        echo "[$(date +'%Y-%m-%d %H:%M:%S')]" "$@" >> $_LOGFILE
    fi
}

# location of run directory
_TMPDIR=$(php bin/console.php common:config get socket_path)

if [ -d "$_TMPDIR" ]; then
    if [ -f "$_TMPDIR/pid" ]; then
        _PID=$(cat "$_TMPDIR/pid")
        if kill -0 "$_PID" 2>/dev/null; then
            log "Sockets already running, restarting sockets"
            exec kill -USR1 "$_PID"
            log "send USR1 to process $_PID failed"
            exit -1
        elif ps "$_PID" >/dev/null 2>&1; then
            log "Sockets running but cannot send USR1 signal."
            log "Are you executing this script as the right user?"
            exit 1
        else
            rm -r "$_TMPDIR/*"
        fi
    fi
else
    mkdir -p "$_TMPDIR"

    if [ ! -d "$_TMPDIR" ]; then
        log "Cannot create directory $_TMPDIR, aborting"
        exit 2
    fi

    chmod 700 "$_TMPDIR"
fi

# SIGINT SIGQUIT SIGTERM
on_int() {
    log "Got SIGINT/SIGQUIT/SIGTERM, exiting..."

    # remove pid file, this will disable socket restart
    rm "$_TMPDIR/pid"

    # kill sockets
    find "$_TMPDIR/pids" -type f -print0 | xargs -0 cat | xargs kill
}

trap on_int SIGINT SIGQUIT SIGTERM

# SIGUSR1
on_usr1() {
    log "Got SIGUSR1, restarting sockets..."

    # kill sockets
    find "$_TMPDIR/pids" -type f -print0 | xargs -0 cat | xargs kill
}

trap on_usr1 SIGUSR1

# socket function
function socket() {
    if ! php bin/console.php "socket:$1" --is-enabled; then
        return
    fi

    local _PIDFILE="$_TMPDIR/pids/$1.pid"

    while true; do
        if [ ! -f "$_TMPDIR/pid" ]; then
            log "Stopping socket $1"
            rm "$_PIDFILE";

            return
        fi

        log "Starting socket $1";

        # start socket in background
        php bin/console.php "socket:$1" --run >> $_LOGFILE &

        # get PID
        local _PID=$!
        echo "$_PID" > "$_PIDFILE";

        # wait for socket process to quit
        wait "$_PID"
        log "Socket $1 exited with $?"

        # sleep for one second
        # not doing this could result in a DoS if the socket exits instantly
        sleep 1
    done
}

# main
mkdir -p "$_TMPDIR/pids"
echo $$ > "$_TMPDIR/pid"

socket "cudi:sale-queue" &
socket "sport:run-queue" &
socket "syllabus:update" &

while jobs %% >/dev/null 2>&1; do
    log 'Waiting...'
    wait
done
