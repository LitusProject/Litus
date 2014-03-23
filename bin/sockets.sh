#!/usr/local/bin/bash
# This little script starts our WebSockets
#
# @author Bram Gotink <bram.gotink@litus.cc>
# @license http://litus.cc/LICENSE

# allow job control
#

set -m

# cd to the repository root
#

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY"; cd ..;

# log function
#

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')]" "$@" >&2
}

# location of run directory
#

if ! php public/index.php config:get --quiet socket_path; then
    log "Cannot get socket path, is litus configured correctly?"
    exit 4
fi

_TMPDIR=$(php public/index.php config:get socket_path)

# don't start the sockets if they're already running or if $_TMPDIR cannot be
# created
#

if [ -d "$_TMPDIR" ]; then
    # directory exists, are we running already?
    if [ -f "$_TMPDIR/pid" ]; then
        # pid file still exists, get pid and check if it is running
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

    # disallow group/other access
    chmod 700 "$_TMPDIR"
fi

# trap INT: close all sockets when this process is killed
#

on_int() {
    log "Got SIGINT/SIGQUIT/SIGTERM, exiting..."

    # remove pid file, this will disable socket restart
    rm "$_TMPDIR/pid"

    # kill sockets
    find "$_TMPDIR/pids" -type f -print0 | xargs -0 cat | xargs kill
}

trap on_int SIGINT SIGQUIT SIGTERM

# trap USR1: restart sockets
#

on_usr1() {
    log "Got SIGUSR1, restarting sockets..."

    # kill sockets
    find "$_TMPDIR/pids" -type f -print0 | xargs -0 cat | xargs kill
}

trap on_usr1 SIGUSR1

# function to run a socket
#

function socket() {
    # we know this is only run if the sockets aren't running...

    local _PIDFILE="$_TMPDIR/pids/$1.pid"

    while :
    do
        if [ ! -f "$_TMPDIR/pid" ]; then
            log "Stopping socket $1"
            rm "$_PIDFILE";

            return
        fi

        log "Starting socket $1";

        # start socket in background
        php public/index.php "socket:$1" --run &

        # get PID
        local _PID=$!
        echo "$_PID" > "$_PIDFILE";

        # wait for socket process to quit
        wait "$_PID"
        log "Socket $1 exited with $?"

        # sleep for one second
        # not doing this could result in a DoS if the socket exits instantly
        # added bonus: gives clients time to realise the socket died
        sleep 1
    done
}

# main execution
#

# create sockets directory
mkdir -p "$_TMPDIR/pids"

# store PID
echo $$ > "$_TMPDIR/pid"

# Start the WebSockets
socket "cudi:sale-queue" &
socket "sport:run-queue" &
socket "syllabus:update" &

# wait until all socket ... & calls end
while jobs %% >/dev/null 2>&1
do
    log 'waiting...'
    wait
done
