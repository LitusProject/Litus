#!/bin/sh

cd $(dirname "$0"); cd ..;
php public/index.php "$@";
