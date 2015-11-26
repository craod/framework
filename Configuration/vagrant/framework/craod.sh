#!/bin/bash

echo ""
echo "Installing Craod"

pushd .
cd /var/www

./Application/cli migrations:migrate --no-interaction
./Application/cli data:install
if [[ "$CRAOD_CONTEXT" != "production" ]]; then ./Application/cli fixtures:up; fi

popd