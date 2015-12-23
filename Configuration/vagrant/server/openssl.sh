#!/bin/bash

echo ""
echo "Installing OpenSSL"

if [[ -z $BOOTSTRAPPER ]]; then apt-get -qq update; fi
apt-get -qq install -y openssl