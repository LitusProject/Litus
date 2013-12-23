#!/bin/bash

# A very small wrapper around our upgrade script
#

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY/../"

php upgrade/upgrade.php
