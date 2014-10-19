#!/bin/bash

# A little script that makes it easier to update the application
#

# don't continue if any subcommand fails
set -e

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY/../"

function checkAndMakeExecutable() {
    if [ ! -x "$1" ]; then
        chmod +x "$1"
    fi
}

function run() {
    php public/index.php "$@"
}

# Making sure our scripts are executable
find bin/ -follow -name '*.sh' | while read file
do
  checkAndMakeExecutable "$file"
done

# Upgrade script
./bin/upgrade.sh

# Updating the database
run orm:schema-tool:update --force
run orm:generate-proxies data/proxies/

# Run installation
run install:all

# Making sure our LESS stylesheets are recompiled
touch module/*/Resources/assets/*/less/base.less

run assetic:build

./bin/litus.sh common:acl-cleanup --flush
