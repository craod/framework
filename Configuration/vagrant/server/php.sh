#!/bin/bash

echo ""
echo "Installing PHP 5.6"

pushd .

if [[ ! -f /etc/apt/trusted.gpg.d/ondrej-php5-5_6.gpg ]]
then
add-apt-repository -y ppa:ondrej/php5-5.6
apt-get update
else
if [[ -z $BOOTSTRAPPER ]]; then apt-get -qq update; fi
fi

apt-get -qq install -y --force-yes php5-cli php5-intl php5-mcrypt php5-fpm php5-curl php5-pgsql php5-xsl php5-dev php-pear

cat /etc/php5/fpm/pool.d/www.conf | sed 's/listen = \/var\/run\/php5-fpm.sock/listen = 127.0.0.1:9000/' > /etc/php5/fpm/pool.d/www.conf2
rm -f /etc/php5/fpm/pool.d/www.conf
mv /etc/php5/fpm/pool.d/www.conf2 /etc/php5/fpm/pool.d/www.conf

rm /etc/php5/cli/php.ini
rm -rf /etc/php5/cli/conf.d
ln -s /etc/php5/fpm/php.ini /etc/php5/cli/
ln -fs /etc/php5/fpm/conf.d /etc/php5/cli/

service php5-fpm restart

popd