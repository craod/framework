#!/bin/bash

echo ""
echo "Running bootstrapper"

rm -rf /var/www
ln -fs /vagrant /var/www

BOOTSTRAPPER=1

apt-get -qq update
apt-get -qq upgrade

/var/www/Configuration/vagrant/git.sh
/var/www/Configuration/vagrant/server.sh
/var/www/Configuration/vagrant/grunt.sh
/var/www/Configuration/vagrant/database.sh
/var/www/Configuration/vagrant/cache.sh
/var/www/Configuration/vagrant/framework.sh