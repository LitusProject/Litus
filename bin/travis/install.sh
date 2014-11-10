#!/bin/bash

# bail out if anything fails
set -e

cd $(dirname "$0")/../..;

composer install --dev

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

# Composer can install two branches
# The patch differs for these two branches
# Try master first
if ! git apply ../assetic.patch; then
    # try 1.1.x branch now
    cat ../assetic.patch | sed -e "s/':'/PATH_SEPARATOR/" | git apply
fi
rm ../assetic.patch

cd -
