#!/bin/sh

pwd=$(cd $(dirname "$0"); cd ..; pwd);
php "$pwd"/public/index.php "$@";
