#!/bin/bash

cd $(dirname "$0");
cd ..;

VERBOSE=''
if [[ "x$1" =~ x(-v|--verbose) ]]; then
    VERBOSE=-vvv
fi

vendor/bin/php-cs-fixer fix $VERBOSE . || exit $?
