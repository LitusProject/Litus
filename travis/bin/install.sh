#!/usr/bin/env bash

# fail on subcommand
set -e

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY/../../"

cd $(dirname "$0")/../..;

composer install

cp config/database.config.php.dist config/database.config.php
cp config/redis.config.php.dist config/redis.config.php
cp config/sentry.config.php.dist config/sentry.config.php
cp config/session.config.php.dist config/session.config.php

npm install -g less@1.7.5
