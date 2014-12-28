# -*- mode: ruby -*-
# vi: set ft=ruby :
#
# Vagrant configuration file to install and run Litus
#
# @author Bram Gotink <bram.gotink@litus.cc>
# @license AGPLv3

# public HTTP port, (LITUS on an old phone)
public_http_port = 54887

# socket ports
socket_ports = [ 8897, 8898, 8899 ]

# Add a useful function to String
class String
    def strip_heredoc
        indent = scan(/^[ \t]*(?=\S)/).min.size || 0
        gsub(/^[ \t]{#{indent}}/, '')
    end
end

Vagrant.configure(2) do |config|
    config.vm.box = "ubuntu/trusty64"

    # Forward HTTP to 54877 (LITUS on an old phone ;))
    config.vm.network "forwarded_port", guest: 80, host: public_http_port

    # Forward Socket ports
    socket_ports.each do |port|
        config.vm.network "forwarded_port", guest: port, host: port
    end

    # Extra configuration for the shared folder
    config.vm.synced_folder ".", "/vagrant",
        # Use rsync instead of VirtualBox/VMWare shared folders,
        type: "rsync",
        # and exclude some more folders when syncing (i.e. .git)
        rsync_exclude: %(.vagrant .git vendor)

    # configure and install dependencies
    config.vm.provision "shell", privileged: true, inline: <<-SHELL.strip_heredoc
        # fail on error
        set -e

        mkdir -p /.litus_installation

        # run only once
        if [ -f /.litus_installation/1 ]; then
            exit 0
        fi

        # set the timezone to Brussels
        echo Europe/Brussels > /etc/timezone
        dpkg-reconfigure -fnoninteractive tzdata

        touch /.litus_installation/1
    SHELL

    config.vm.provision "shell", privileged: true, inline: <<-SHELL.strip_heredoc
        # fail on error
        set -e

        if [ -f /.litus_installation/2 ]; then
            exit 0
        fi

        cd /vagrant

        # add custom source lists
        apt-add-repository -y ppa:chris-lea/node.js
        apt-add-repository -y ppa:ondrej/nginx
        apt-add-repository -y ppa:ondrej/php5
        apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
        apt-add-repository -y 'deb http://downloads-distro.mongodb.org/repo/ubuntu-upstart dist 10gen'

        # update source lists
        apt-get update

        # install dependencies
        apt-get install -y git ssh-client \
            postgresql-9.3 mongodb \
            php5 php5-cli php5-mongo php5-xdebug php5-memcached php5-intl php5-imagick php5-fpm php5-pgsql \
            nodejs \
            nginx \
            ntp
        apt-get install -y --no-install-recommends fop

        npm install -g less

        # install composer
        which composer >/dev/null 2>&1 || (cd /usr/local/bin && php -r "readfile('https://getcomposer.org/installer');" | php && ln -s composer.phar composer)

        # create log directory
        mkdir -p /var/log/litus

        # configurate nginx
        cp /vagrant/vagrant/nginx.conf /etc/nginx/sites-available/default
        service nginx restart

        # configure php5-fpm
        cp /vagrant/vagrant/fpm-pool.conf /etc/php5/fpm/pool.d/www.conf
        sed -i'' -e 's/--nodaemonize/--nodaemonize -R/' /etc/init/php5-fpm.conf
        service php5-fpm restart

        touch /.litus_installation/2
    SHELL

    # install litus if not installed yet
    config.vm.provision "shell", privileged: true, inline: <<-SHELL.strip_heredoc
        # fail on error
        set -e

        # run only once
        if [ -f /.litus_installation/3 ]; then
            exit 0
        fi

        # create mongo user
        echo -e "use litus\nif (db.system.users.find({user:'litus'}).count() === 0) db.addUser('litus', 'huQeyU8te3aXusaz');" | mongo

        # create postgres user
        if [ 0 -eq $(echo "SELECT COUNT(*) FROM pg_catalog.pg_user WHERE usename = 'litus';" | su -c 'psql -t' postgres) ]; then
            echo "CREATE USER litus WITH SUPERUSER PASSWORD 'huQeyU8te3aXusaz';" | su -c psql postgres
        fi

        # create postgres database
        if [ 0 -eq $(echo "SELECT COUNT(*) FROM pg_catalog.pg_database WHERE datname = 'litus';" | su -c 'psql -t' postgres) ]; then
            echo 'CREATE DATABASE litus WITH OWNER litus;' | su -c psql postgres
        fi

        # initialise database
        /vagrant/bin/litus.sh orm:schema-tool:create

        # initialise litus
        /vagrant/bin/litus.sh install:all

        # install litus init script
        cd /etc/init.d
        ln -sf /vagrant/bin/init.d/litus litus
        echo 'USER=root' > /etc/default/litus
        service litus start
        update-rc.d litus defaults 99

        touch /.litus_installation/3
    SHELL

    config.vm.provision "shell", privileged: true, inline: <<-SHELL.strip_heredoc
        # fail on error
        set -e

        # run only once
        if [ -f /.litus_installation/4 ]; then
            exit 0
        fi

        mkdir -p /usr/local/libexec
        cd /usr/local/libexec

        php -r "readfile('https://getcomposer.org/installer');" | php

        cd /usr/local/bin
        cat /vagrant/vagrant/composer > composer
        chmod +x composer

        touch /.litus_installation/4
    SHELL

    # update litus
    config.vm.provision "shell", privileged: true, inline: <<-SHELL.strip_heredoc
        # fail on error
        set -e

        cd /vagrant

        composer install

        cd ./config

        ln -sf database.config.php.dist database.config.php
        ln -sf lilo.config.php.dist lilo.config.php

        cd ./autoload

        ln -sf nodeprefix.local.php.dist nodeprefix.local.php

        /vagrant/bin/update.sh
    SHELL
end
