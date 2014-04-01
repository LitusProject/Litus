#!/bin/bash

# A very small wrapper around our e-mail alias importer
#

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
MAIN_DIRECTORY=$(cd "$SCRIPT_DIRECTORY/../../" && pwd)

php "$MAIN_DIRECTORY"/public/index.php mail:import-aliases "$@";
