#!/bin/bash

# A little script that makes it easier to update the application
#

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY/../"

function checkAndMakeExecutable() {
    if [ ! -x $1 ]; then
        chmod +x $1
    fi
}

# Making sure our scripts are executable
find bin/ -follow -name '*.sh' | while read file
do
  checkAndMakeExecutable "$file"
done

# Upgrade script
./bin/upgrade.sh

# Updating the database
php public/index.php orm:schema-tool:update --force
php public/index.php orm:generate-proxies data/proxies/

# Making sure our LESS stylesheets are recompiled
find module/ -name base.less | xargs touch

php public/index.php assetic:build
