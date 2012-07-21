#!/bin/bash

# This script takes care of starting our WebSockets
#

scriptDirectory=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "${scriptDirectory}/../"

function checkAndRun() {
	if ps aux | grep -v grep | grep "$1" > /dev/null; then
		exit 0
	fi
	
	echo "Running: $1"
	$1 &
}

# Starting the WebSockets
checkAndRun 'php bin/CudiBundle/queue.php --run'
checkAndRun 'php bin/SyllabusBundle/update.php --run'