#!/bin/bash

# This script takes care of starting our WebSockets
#

scriptDirectory=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "${scriptDirectory}/../"

function killAndRun() {
	if ps aux | grep -v grep | grep "$1" > /dev/null; then
		kill $(ps aux | grep -v grep | grep "$1" | cut -c10-15)
	fi
	
	echo "Running: $1"
	bash -c $1 &
}

# Starting the WebSockets
killAndRun 'php bin/CudiBundle/queue.php --run'
killAndRun 'php bin/SyllabusBundle/update.php --run'