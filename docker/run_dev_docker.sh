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
# Change working dir to the app root
pushd ..

# docker build -t litus .
#docker-compose -f docker-compose.yml -f docker/docker-compose.dev.yml up -d
docker-compose -f docker-compose.yml up -d
docker exec -u 0 -it litus-php-fpm-1 chown -R www-data:www-data /app
trap "trap_ctrlc" 2
docker-compose run --rm php-cli init
# docker-compose logs -f

# echo "Listening for updates..."
# find . | entr sh -c 'docker exec -u 0 -it litus-php-fpm-1 chown -R www-data:www-data /app'
find . | entr sh -c 'echo "Change Detected...";docker cp . litus-php-fpm-1:/app; docker exec -u 0 -it litus-php-fpm-1 chown -R www-data:www-data /app; echo "Updated."'

echo "Goodbye."
popd
