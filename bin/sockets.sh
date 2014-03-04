#!/bin/bash

# This little script starts our WebSockets
#

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY"; cd ..;

function backgroundTask() {
    if ps aux | grep -v grep | grep "$1" > /dev/null; then
        return 0
    fi

    echo "Starting: $1"
    $1 &
}

# Starting the WebSockets
for i in {1..50}; do
    backgroundTask "php public/index.php socket:cudi:sale-queue --run"
    backgroundTask "php public/index.php socket:sport:run-queue --run"
    backgroundTask "php public/index.php socket:syllabus:update --run"

    sleep 1
done
