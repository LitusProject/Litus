#!/usr/bin/env bash

# fail on subcommand
set -e

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY/../../"

init_database() {
    psql -c 'create database litus;' -U postgres
    psql -c "create user litus with login superuser password 'huQeyU8te3aXusaz';" -U postgres
    psql -c 'alter database litus owner to litus;' -U postgres

    cat <<EOF | mongo
use litus
db.createUser({
    user: "litus",
    pwd: "huQeyU8te3aXusaz",
    roles: ["readWrite", "dbAdmin"]
})
EOF
}

phpcs() {
    if [ ! -d phpcs/ ]; then
        mkdir phpcs/
    fi

    vendor/bin/phpcs -q --cache=phpcs/cache.json --report=source --runtime-set ignore_warnings_on_exit true
}

phpstan() {
    vendor/bin/phpstan analyze --no-progress
}

install() {
    init_database

    php bin/doctrine.php orm:schema-tool:create
    php bin/console.php install:all
}

update() {
    bin/update.sh
}

case $1 in
    analyze)
        phpcs
        phpstan
        ;;
    install)
        install
        update
        ;;
    *)
        exit 1
        ;;
esac
