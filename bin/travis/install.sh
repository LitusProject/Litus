#!/bin/bash

# bail out if anything fails
set -e

cd $(dirname "$0")/../..;

composer install

cp config/lilo.config.php.dist     config/lilo.config.php
cp config/database.config.php.dist config/database.config.php

# install npm
# I know, right?
sudo aptitude -y install npm

# install lessc
sudo npm install -g less
