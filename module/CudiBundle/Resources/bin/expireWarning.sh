#!/bin/bash

# A very small wrapper around our expire warning script
#

scriptDirectory=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "${scriptDirectory}/../../"

APPLICATION_ENV="production" php public/index.php cudi:expire -m
