#!/bin/bash

echo "yip yip"
function trap_ctrlc (){
    # perform cleanup here
    echo "Ctrl-C caught...performing clean up"
    docker-compose down
    echo "Goodbye" 
    exit 2
}

if [ "$EUID" -ne 0 ]
    then echo "Please run as root"
    exit
fi

docker build -t litus .
docker-compose up -d
trap "trap_ctrlc" 2
docker-compose run --rm php-cli init
# TODO: entr vervangen door rsync --> Zorgt voor betere performance (denk)
echo "Listening for updates..."
find . | entr sh -c 'echo "Change Detected...";docker cp . litus-php-fpm-1:/app; docker exec -u 0 -it litus-php-fpm-1 chown -R www-data:www-data /app; echo "Updated."'

echo "Goodbye."

