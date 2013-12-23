#!/bin/bash

# A little script that makes it easier to update the application
#

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY/../"

function checkAndMakeExecutable() {
    if [ ! -x $1 ]; then
        chmod +x $1
    fi
}

# Making sure our scripts are executable
checkAndMakeExecutable "bin/sockets.sh"
checkAndMakeExecutable "bin/upgrade.sh"

checkAndMakeExecutable "bin/CommonBundle/gc.sh"
checkAndMakeExecutable "bin/CudiBundle/catalogUpdate.sh"
checkAndMakeExecutable "bin/CudiBundle/expireWarning.sh"
checkAndMakeExecutable "bin/Doctrine/doctrine-module"
checkAndMakeExecutable "bin/MailBundle/parser.sh"

# Upgrade script
./bin/upgrade.sh

# Updating the database
bin/Doctrine/doctrine-module orm:schema-tool:update --force
bin/Doctrine/doctrine-module orm:generate-proxies data/proxies/

bin/Doctrine/doctrine-module orm:generate:proxies data/proxies/

# Making sure our LESS stylesheets are recompiled
touch module/CommonBundle/Resources/assets/admin/less/admin.less
touch module/CommonBundle/Resources/assets/site/less/base.less

touch module/CudiBundle/Resources/assets/prof/less/base.less
touch module/CudiBundle/Resources/assets/sale/less/base.less
touch module/CudiBundle/Resources/assets/supplier/less/base.less

touch module/LogisticsBundle/Resources/assets/logistics/less/base.less

touch module/SportBundle/Resources/assets/run/less/base.less

php public/index.php assetic build
