#!/bin/bash

# A little script that makes it easier to update the application
#

scriptDirectory=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "${scriptDirectory}/../"

function killAndRun() {
	if ps aux | grep -v grep | grep "$1" > /dev/null]; then
		kill $(ps aux | grep -v grep | grep "$1" | cut -c10-15)
	fi
	
	echo "Running: $1"
	$1 &
}

# Updating the database
bin/MistDoctrine/doctrine orm:schema-tool:update --force
bin/MistDoctrine/doctrine orm:generate-proxies data/proxies/

bin/MistDoctrine/doctrine orm:generate-proxies data/proxies/
chown -R www-data:www-data data/proxies

# Making sure our LESS stylesheets are recompiled
touch module/CommonBundle/src/Resources/assets/admin/less/admin.less
touch module/CommonBundle/src/Resources/assets/bootstrap/less/bootstrap.less
touch module/CommonBundle/src/Resources/assets/site/less/base.less

touch module/CudiBundle/src/Resources/assets/prof/less/base.less
touch module/CudiBundle/src/Resources/assets/sale/less/base.less
touch module/CudiBundle/src/Resources/assets/supplier/less/base.less

# Starting the WebSockets
if [ "$EUID" == 0 ]; then
	killAndRun 'php bin/CudiBundle/queue.php --run'
	killAndRun 'php bin/SyllabusBundle/update.php --run'
fi
