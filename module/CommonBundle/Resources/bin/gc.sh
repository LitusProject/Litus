#!/bin/bash

# A very small wrapper around our garbage collector
#

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY/../../"

APPLICATION_ENV="production" php public/index.php common:gc -a
