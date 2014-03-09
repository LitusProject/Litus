#!/bin/bash

# bail out if anything fails
set -e

cd $(dirname "$0")/../..;

if [ -f vendor/composer.lock ]; then
    cp vendor/composer.lock ./composer.lock
    composer update --dev
else
    composer install --dev
fi
cp ./composer.lock vendor/composer.lock

cp config/lilo.config.php.dist     config/lilo.config.php
cp config/database.config.php.dist config/database.config.php
