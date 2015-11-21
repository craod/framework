#!/bin/bash

echo ""
echo "Installing grunt"

if [[ -z $BOOTSTRAPPER ]]; then apt-get -qq update; fi
apt-get -qq install -y nodejs npm

pushd .
cd /var/www

npm install

popd