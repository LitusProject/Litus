#!/bin/bash

# fail if any command fails
set -e

cd $(dirname "$0")/../..

init_database() {
    echo "Initialising database"

    psql -c 'create database litus;' -U postgres
    psql -c "create user litus with login superuser password 'huQeyU8te3aXusaz';" -U postgres
    psql -c 'alter database litus owner to litus;' -U postgres

    cat <<EOF | mongo
use litus
db.addUser({
    user: "litus",
    pwd: "huQeyU8te3aXusaz",
    roles: ["readWrite", "dbAdmin"]
})
EOF

    echo "Database initialised"
    echo
}

install() {
    init_database

    bin/litus.sh orm:schema-tool:create
    bin/litus.sh install:all
}

case $1 in
    codestyle)
        exec bin/fix-cs.sh
        ;;
    install)
        install
        exec bin/update.sh
        ;;
    *)
        echo "Unknown travis test: '$1'" >&2
        ;;
esac

exit 1
