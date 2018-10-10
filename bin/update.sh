#!/usr/bin/env bash

# fail on subcommand
set -e

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY/../"

function run() {
    php bin/console.php "$@"
}

# making sure our scripts are executable
find bin/ -follow -name '*.sh' | while read f
do
  if [ ! -x "$file" ]; then
        chmod +x "$file"
    fi
done

# upgrade
./bin/upgrade.sh

# doctrine
php bin/console.php orm:schema-tool:update --force
run orm:generate-proxies data/proxies/

# install
run install:all

# assetic
touch module/*/Resources/assets/*/less/base.less
run assetic:build

run common:acl-cleanup --flush
