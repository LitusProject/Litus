#!/bin/bash
echo "yip yip"
# Change the working dir to the root of the app
pushd ..
# Stop script on error
set -e

# Run as root
if [ "$EUID" -ne 0 ]
    then echo "Please run as root"
    exit
fi

if [[ $# -ne 1 ]];then
    if [ -f "/tmp/db_dump" ];then
        echo "Cached database dump (/tmp/db_dump) detected, do you want to load this database?"
        select yn in "Yes" "No"; do
            case $yn in
                Yes )
                    filename="/tmp/db_dump"
                    break;;
                No )
                    break;;
            esac
        done
    fi
    if [ -z $filename ]; then
        echo "Do you want to use a local stored database?"
        select yn in "Yes" "No"; do
            case $yn in
                Yes ) 
                    echo "No file given, please give the file name of the database dump"
                    read filename
                    if [ ! -f "$filename" ]; then
                        echo "File does not exist!"
                        exit;
                    fi
                    break;;
                No ) 
                    echo "Database server:"
                    read database_server
                    echo "Server user:"
                    read database_user
                    ssh -t $database_user@$database_server "sudo runuser -l postgres -c 'pg_dump litus > /tmp/db_dump'"
                    scp $database_user@$database_server:/tmp/db_dump /tmp
                    ssh -t $database_user@$database_server "sudo rm /tmp/db_dump"
                    filename="/tmp/db_dump"
                    break;;
            esac
        done
    fi
    else
    filename=$1
fi

bn=$(basename $filename)

# Copy over database
docker cp $filename litus-postgres-1:/root

# Delete all tables from database
docker exec litus-postgres-1 psql -U litus -c "
DROP SCHEMA IF EXISTS public CASCADE;
CREATE SCHEMA public AUTHORIZATION litus;
GRANT ALL ON SCHEMA public TO public;
GRANT ALL ON SCHEMA public TO litus;"
# Load new data
docker exec litus-postgres-1 /bin/sh -c "psql -h 127.0.0.1 -U litus -d litus < /root/$bn"

# Set shibboleth url
docker exec litus-postgres-1 psql -U litus -c "
UPDATE general_config SET value='' WHERE key='shibboleth_url';"

popd
