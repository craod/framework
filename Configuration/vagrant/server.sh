#!/bin/bash

echo ""
echo "Installing server"

echo "127.0.0.1 api.craod.dev" >> /etc/hosts

/var/www/Configuration/vagrant/server/nginx.sh
/var/www/Configuration/vagrant/server/php.sh
/var/www/Configuration/vagrant/server/composer.sh