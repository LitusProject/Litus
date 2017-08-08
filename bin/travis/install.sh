#!/bin/bash

# bail out if anything fails
set -e

cd $(dirname "$0")/../..;

composer install

cp config/lilo.config.php.dist     config/lilo.config.php
cp config/database.config.php.dist config/database.config.php

# link node to /usr/local/bin
sudo mkdir -p /usr/local/bin
sudo ln -s $(which node) /usr/local/bin/node

# install npm
# I know, right?
sudo apt-get install npm
sudo npm install
npm --version

# install lessc
 sudo npm install -g less
