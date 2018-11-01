#!/usr/bin/env bash

if [ -z "$APPLICATION_ENV" ]; then
    export APPLICATION_ENV=production
fi

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY/../../"

php bin/console.php cudi:printer-test "$@"
