#!/bin/bash

echo ""
echo "Installing Craod"

pushd .
cd /var/www

./Application/cli migrations:migrate --no-interaction
if [[ "$CRAOD_CONTEXT" != "production" ]]; then ./Application/cli fixtures:up user; fi

popd