#!/usr/bin/env bash

# fail on subcommand
set -e

cd $(dirname "$0")/../..;

composer install

cp config/sentry.config.php.dist config/sentry.config.php
cp config/database.config.php.dist config/database.config.php

# install npm
sudo aptitude -y install npm
npm config set strict-ssl false

# link node to /usr/bin
sudo ln -s $(which node) /usr/bin/node
sudo ln -s /usr/local/lib/node_modules /usr/lib/node_modules

# install lessc
sudo npm install -g less@1.7.5
