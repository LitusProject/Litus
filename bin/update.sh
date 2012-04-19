#!/bin/bash

# A little script that makes it easier to update the application
#

cd ../
git pull

# Updating the database
bin/MistDoctrine/doctrine orm:schema-tool:update --force

# Making sure our LESS stylesheets are recompiled
touch module/CommonBundle/src/Resources/assets/admin/less/admin.less
touch module/CommonBundle/src/Resources/assets/bootstrap/less/bootstrap.less
touch module/CudiBundle/src/Resources/assets/sale/less/base.less
touch module/CudiBundle/src/Resources/assets/supplier/less/base.less
touch module/ProfBundle/src/Resources/assets/prof/less/base.less
