#!/bin/bash

# A little script that makes it easier to update the application
#

scriptDirectory=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "${scriptDirectory}/../"

# Updating the database
if [ ! -x bin/MistDoctrine/doctrine ]; then
    chmod +x bin/MistDoctrine/doctrine
fi

bin/MistDoctrine/doctrine orm:schema-tool:update --force
bin/MistDoctrine/doctrine orm:generate-proxies data/proxies/

# Making sure our LESS stylesheets are recompiled
touch module/CommonBundle/src/Resources/assets/admin/less/admin.less
touch module/CommonBundle/src/Resources/assets/bootstrap/less/bootstrap.less
touch module/CommonBundle/src/Resources/assets/site/less/base.less

touch module/CudiBundle/src/Resources/assets/prof/less/base.less
touch module/CudiBundle/src/Resources/assets/sale/less/base.less
touch module/CudiBundle/src/Resources/assets/supplier/less/base.less
