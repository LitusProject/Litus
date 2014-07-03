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
curl -L http://npmjs.org/install.sh | sudo sh

# install lessc
sudo npm install -g less

# patch assetic
cd vendor/kriswallsmith/assetic
wget -q -O../assetic.patch https://github.com/alex-pex/assetic/commit/380536cf8f7571a4301b4c42a3f6f8ce4636c48d.patch

# Composer can install two branches
# The patch differs for these two branches
# Try master first
if ! git apply ../assetic.patch; then
    # try 1.1.x branch now
    cat ../assetic.patch | sed -e "s/':'/PATH_SEPARATOR/" | git apply
fi
rm ../assetic.patch

cd -
