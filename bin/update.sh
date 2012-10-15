#!/bin/bash

# A little script that makes it easier to update the application
#

scriptDirectory=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "${scriptDirectory}/../"

# Updating the database
if [ ! -x bin/Doctrine/doctrine-module ]; then
    chmod +x bin/Doctrine/doctrine-module
fi

bin/Doctrine/doctrine-module orm:schema-tool:update --force
bin/Doctrine/doctrine-module orm:generate-proxies data/proxies/

bin/Doctrine/doctrine-module odm:generate:proxies data/proxies/
bin/Doctrine/doctrine-module odm:generate:hydrators data/hydrators/

# Making sure our LESS stylesheets are recompiled
touch module/CommonBundle/src/Resources/assets/admin/less/admin.less
touch module/CommonBundle/src/Resources/assets/bootstrap/less/bootstrap.less
touch module/CommonBundle/src/Resources/assets/site/less/base.less

touch module/CudiBundle/src/Resources/assets/prof/less/base.less
touch module/CudiBundle/src/Resources/assets/sale/less/base.less
touch module/CudiBundle/src/Resources/assets/supplier/less/base.less

touch module/LogisticsBundle/src/Resources/assets/logistics/less/base.less

touch module/SportBundle/src/Resources/assets/run/less/base.less
