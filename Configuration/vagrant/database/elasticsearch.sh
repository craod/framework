#!/bin/bash

echo ""
echo "Installing ElasticSearch"

if [[ ! -f /etc/apt/sources.list.d/elasticsearch-2.x.list ]]
then
wget -qO - https://packages.elastic.co/GPG-KEY-elasticsearch | apt-key add -
echo "deb http://packages.elastic.co/elasticsearch/2.x/debian stable main" > /etc/apt/sources.list.d/elasticsearch-2.x.list
apt-get -qq update
else
if [[ -z $BOOTSTRAPPER ]]; then apt-get -qq update; fi
fi
apt-get -qq install -y elasticsearch
service elasticsearch stop

# This does not work well yet - I mean the script does, but the configuration option makes it not work
#if [[ -z $(grep "network.bind_host: localhost" /etc/elasticsearch/elasticsearch.yml) ]]; then
#cat << EOT >> /etc/elasticsearch/elasticsearch.yml

### Craod configuration
#network.bind_host: localhost
#script.disable_dynamic: true
#EOT
#fi

if [[ -z $(which unzip) ]];
then
apt-get -qq install -y unzip
fi

JDBC_VERSION="2.1.0.0"
JDBC_FILENAME="elasticsearch-jdbc-$JDBC_VERSION-dist.zip"
ES_PATH="/var/www/Configuration/elasticsearch-jdbc-$JDBC_VERSION"

wget "http://xbib.org/repository/org/xbib/elasticsearch/importer/elasticsearch-jdbc/$JDBC_VERSION/$JDBC_FILENAME"
unzip "$JDBC_FILENAME"
rm "$JDBC_FILENAME"
wget -O "$ES_PATH/lib/postgresql-9.4-1206-jdbc41.jar" https://jdbc.postgresql.org/download/postgresql-9.4-1206-jdbc41.jar

service elasticsearch start