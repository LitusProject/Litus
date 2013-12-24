#!/bin/bash

# A very small wrapper around our reminder mail script
#

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "${SCRIPT_DIRECTORY}/../../"

php bin/CudiBundle/reminderMail.php -rm