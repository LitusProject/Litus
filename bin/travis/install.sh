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

# install lessc
wget -O- https://github.com/less/less.js/archive/master.tar.gz | tar xz

sudo mkdir -p /usr/local/lib/node_modules
sudo ln -s $(pwd)/less.js-master /usr/local/lib/node_modules/less

# install node to /usr/local/bin
sudo ln -s $(which node) /usr/local/bin/node
