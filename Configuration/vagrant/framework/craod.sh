#!/bin/bash

echo ""
echo "Installing Craod"

pushd .
cd /var/www

if [[ -z $(which craod) ]]; then
ln -s /var/www/Application/cli /usr/bin/craod
chmod +x /usr/bin/craod
fi

craod cache:flush
craod migrations:execute 20151122000000 --down --no-interaction
craod migrations:migrate --no-interaction
craod data:user:install
if [[ "$CRAOD_CONTEXT" != "production" ]]; then craod fixtures:up; fi

popd