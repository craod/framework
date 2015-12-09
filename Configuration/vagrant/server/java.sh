#!/bin/bash

echo ""
echo "Installing Oracle Java 8"

if [[ -z $(grep "deb http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main" /etc/apt/sources.list.d/webupd8team-java-trusty.list) ]]
then
add-apt-repository --yes ppa:webupd8team/java
apt-get -qq update
else
if [[ -z $BOOTSTRAPPER ]]; then apt-get -qq update; fi
fi

echo "oracle-java8-installer shared/accepted-oracle-license-v1-1 select true" | debconf-set-selections
apt-get -qq install -y oracle-java8-installer