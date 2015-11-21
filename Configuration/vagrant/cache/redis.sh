#!/usr/bin/env bash

echo ""
echo "Installing redis"

if [[ -z $BOOTSTRAPPER ]]; then apt-get -qq update; fi
apt-get -qq install -y redis-server