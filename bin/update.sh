#!/usr/bin/env bash

# fail on subcommand
set -e

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY/../"

find bin/ -follow -name '*.sh' | while read f
do
  if [ ! -x "$f" ]; then
      chmod +x "$f"
  fi
done

# cache
rm data/cache/*

# upgrade
./bin/upgrade.sh

# doctrine
php bin/doctrine.php orm:schema-tool:update --force
php bin/doctrine.php orm:generate-proxies data/proxies/

# install
php bin/console.php install:all

# assetic
touch module/*/Resources/assets/*/less/base.less
php bin/assetic.php build

# acl
php bin/console.php common:acl:cleanup --flush
