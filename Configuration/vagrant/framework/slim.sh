#!/bin/bash

echo ""
echo "Installing Slim"

pushd .
cd /var/www

composer install

popd