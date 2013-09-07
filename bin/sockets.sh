#!/bin/bash

# This little script starts our WebSockets
#

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)

function backgroundTask() {
	if ps aux | grep -v grep | grep "$1" > /dev/null; then
		return 0
	fi

	echo "Starting: $1"
	$1 &
}

# Starting the WebSockets
for i in {1..50}
do
    backgroundTask "php $SCRIPT_DIRECTORY/CudiBundle/queue.php --run"
    backgroundTask "php $SCRIPT_DIRECTORY/SportBundle/run.php --run"
    backgroundTask "php $SCRIPT_DIRECTORY/SyllabusBundle/update.php --run"

    sleep 1
done
