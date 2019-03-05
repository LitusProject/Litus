#!/usr/bin/env bash

if [ -z "$APPLICATION_ENV" ]; then
    export APPLICATION_ENV=production
fi

# fail on subcommand
set -e

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY/../"

php upgrade/upgrade.php "$@"
