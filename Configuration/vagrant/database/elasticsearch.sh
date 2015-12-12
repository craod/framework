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
service elasticsearch start

# This does not work well yet - I mean the script does, but the configuration option makes it not work
#if [[ -z $(grep "network.bind_host: localhost" /etc/elasticsearch/elasticsearch.yml) ]]; then
#cat << EOT >> /etc/elasticsearch/elasticsearch.yml

### Craod configuration
#network.bind_host: localhost
#script.disable_dynamic: true
#EOT
#fi