#!/bin/bash

# bail out if anything fails
set -e

cd $(dirname $(dirname "$0"));

if [ -f composer.lock ]; then
    composer update --dev
else
    composer install --dev
fi

cp config/lilo.config.php.dist     config/lilo.config.php
cp config/database.config.php.dist config/database.config.php
