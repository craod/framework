#!/bin/bash

echo ""
echo "Installing database"

/var/www/Configuration/vagrant/database/postgresql.sh
/var/www/Configuration/vagrant/database/elasticsearch.sh