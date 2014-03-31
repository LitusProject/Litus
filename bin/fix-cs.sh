#!/bin/sh

cd $(dirname "$0");
cd ..;

vendor/bin/php-cs-fixer fix . || exit $?
