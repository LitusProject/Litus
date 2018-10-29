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
    vendor/bin/phpcs -q --runtime-set ignore_warnings_on_exit true --report=summary
}

install() {
    init_database

    php bin/doctrine.php orm:schema-tool:create
    php bin/console.php install:all
}

case $1 in
    analyze)
        phpcs
        ;;
    install)
        install
        bin/update.sh
        ;;
    *)
        echo "Unknown Travis test: '$1'" >&2
        ;;
esac
