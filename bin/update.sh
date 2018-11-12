#!/usr/bin/env bash

header() {
    COLUMNS=$((tty -s && tput cols) || true)
    COLUMNS=${COLUMNS:-80}
    if [ $COLUMNS -gt 120 ]; then
      COLUMNS=120
    fi

    if $2; then
      echo ""
    fi

    printf "=%.0s" $(eval echo "{1..$COLUMNS}")
    printf "\n"

    printf "%*s\n" $(((${#line} + $COLUMNS)/2)) "$1"

    printf "=%.0s" $(eval echo "{1..$COLUMNS}")
    printf "\n"

    echo ""
}

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
rm -rf data/cache/*
rm -rf public/_assetic/*

# upgrade
header "Upgrade" false
./bin/upgrade.sh

# doctrine
header "Doctrine" true

php bin/doctrine.php orm:schema-tool:update --force
php bin/doctrine.php orm:generate-proxies data/proxies/

# installation
header "Installation" true

php bin/console.php install:all
php bin/assetic.php build

# cleanup
header "Cleanup" true
php bin/console.php common:cleanup-acl --flush
