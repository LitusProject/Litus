#!/bin/bash

# fail if any command fails
set -e

cd $(dirname "$0")/../..

do_psql() {
    psql -c "$1" -U postgres -d litus
}

init_database() {
    echo "Initialising database"

    psql -c 'create database litus;' -U postgres
    psql -c "create user litus with login superuser password 'huQeyU8te3aXusaz';" -U postgres
    psql -c 'alter database litus owner to litus;' -U postgres

    for schema in acl cudi general mail shifts tickets api forms nodes publications sport users br gallery logistics quiz syllabus; do
        do_psql "create schema $schema authorization litus;"
    done

    do_psql "insert into general.config values ('last_upgrade', '$(date %Y%m%d)99', 'The last upgrade that was applied');"

    cat <<EOF | mongo
use litus
db.createUser({
    user: "litus",
    pwd: "huQeyU8te3aXusaz",
    roles: ["readWrite", "dbAdmin"]
})
EOF

    echo "Database initialised"
    echo
}

case $1 in
    codestyle)
        exec bin/fix-cs.sh
        ;;
    install)
        init_database
        exec bin/update.sh
        ;;
    *)
        echo "Unknown travis test: '$1'" >&2
        ;;
esac

exit 1
