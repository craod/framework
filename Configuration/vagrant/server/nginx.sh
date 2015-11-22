#!/bin/bash

echo ""
echo "Installing nginx"

if [[ -z $BOOTSTRAPPER ]]; then apt-get -qq update; fi
apt-get -qq install -y nginx
service nginx stop

cat << EOF > /etc/nginx/conf.d/craod.conf
server {
	listen 80;
	index api.php;
	server_name api.craod.dev;
	error_log /var/www/Logs/nginx/access.log;
	root '/var/www/Application/';
	try_files \$uri \$uri/ /api.php?\$query_string;

	rewrite_log on;

	location ~ \.php {
		fastcgi_pass 127.0.0.1:9000;
		fastcgi_index /api.php;

		include /etc/nginx/fastcgi_params;

		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		fastcgi_param PATH_INFO \$fastcgi_path_info;
		fastcgi_param PATH_TRANSLATED \$document_root\$fastcgi_path_info;
		fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
		fastcgi_read_timeout 3000;
		fastcgi_send_timeout 3000;
	}
}

EOF

cat /etc/nginx/nginx.conf | sed 's/sendfile.*on;/sendfile off;/' > /etc/nginx/nginx.conf.alternate
rm -f /etc/nginx/nginx.conf
mv /etc/nginx/nginx.conf.alternate /etc/nginx/nginx.conf
service nginx start