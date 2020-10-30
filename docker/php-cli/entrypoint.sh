#! /bin/sh

# fail on subcommand
set -e

case "$1" in
    init)
        php /app/bin/doctrine.php orm:schema-tool:create
        php /app/bin/doctrine.php migrations:version --add --all --no-interaction

        php /app/bin/console.php install:all
        ;;

    upgrade)
        php /app/bin/doctrine.php migrations:migrate --no-interaction

        php /app/bin/console.php install:all
        php /app/bin/console.php common:cleanup-acl --flush
        ;;

    "")
        exit 0
        ;;

    *)
        exit 1;
        ;;
esac
