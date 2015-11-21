#!/bin/bash

echo ""
echo "Installing PostgreSQL 9.4"

if [[ -z $(grep "deb http://apt.postgresql.org/pub/repos/apt/ trusty-pgdg main" /etc/apt/sources.list.d/pgdg.list) ]]
then
echo "deb http://apt.postgresql.org/pub/repos/apt/ trusty-pgdg main" >> /etc/apt/sources.list.d/pgdg.list
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -
apt-get -qq update
else
if [[ -z $BOOTSTRAPPER ]]; then apt-get -qq update; fi
fi

apt-get -qq install -y postgresql-9.4

sudo -i -u postgres <<EOF
psql -c "CREATE USER craod PASSWORD '123';"
psql -c "CREATE DATABASE craod;"
exit
EOF

echo "listen_addresses = '*'" >> /etc/postgresql/9.4/main/postgresql.conf
cat /etc/postgresql/9.4/main/pg_hba.conf | sed 's/host    all             all             127.0.0.1\/32            md5/host    all             all             0.0.0.0\/0            md5/' > /etc/postgresql/9.4/main/pg_hba.conf.old
rm -f /etc/postgresql/9.4/main/pg_hba.conf
mv /etc/postgresql/9.4/main/pg_hba.conf.old /etc/postgresql/9.4/main/pg_hba.conf
service postgresql restart