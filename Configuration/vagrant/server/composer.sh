#!/bin/bash

echo ""
echo "Installing composer"

pushd .

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

popd

