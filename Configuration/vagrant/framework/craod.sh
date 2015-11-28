#!/bin/bash

echo ""
echo "Installing Craod"

pushd .
cd /var/www

/var/www/Application/cli cache:flush
/var/www/Application/cli migrations:execute 20151122000000 --down --no-interaction
./Application/cli migrations:migrate --no-interaction
if [[ "$CRAOD_CONTEXT" != "production" ]]; then ./Application/cli fixtures:up; fi

popd