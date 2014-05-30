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

# link node to /usr/local/bin
sudo mkdir -p /usr/local/bin
sudo ln -s $(which node) /usr/local/bin/node

# install npm
# I know, right?
wget -q -O- http://npmjs.org/install.sh | sudo sh

# install lessc
sudo npm install -g less
