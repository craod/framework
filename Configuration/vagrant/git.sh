#!/bin/bash

echo ""
echo "Installing git"

if [[ -z $BOOTSTRAPPER ]]; then apt-get -qq update; fi
apt-get -qq install -y build-essential git