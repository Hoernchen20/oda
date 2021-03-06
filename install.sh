#!/bin/bash
#install mongodb, nginx, php-fpm

#nginx
if [[ -f $(which nginx 2>/dev/null) ]]
	then
		echo "nginx is already installed"
	else
		echo "Installing nginx"
		wget https://nginx.org/keys/nginx_signing.key
		sudo apt-key add nginx_signing.key
		rm nginx_signing.key
		echo "deb http://nginx.org/packages/ubuntu/ xenial nginx" | sudo tee /etc/apt/sources.list.d/nginx.list
		echo "deb-src http://nginx.org/packages/ubuntu/ xenial nginx" | sudo tee -a /etc/apt/sources.list.d/nginx.list
		sudo apt-get update
		sudo apt-get install -y nginx
		sudo cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.original
		sudo cp nginx.conf /etc/nginx/
		
		sudo mkdir /etc/nginx/ssl
		sudo openssl dhparam -outform PEM -out /etc/nginx/ssl/dhparam2048.pem 2048
		sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/ssl/private.key -out /etc/nginx/ssl/cert.crt
		
		sudo cp -R site/* /var/www/html/
#		sudo apt-get update
#		sudo apt-get install software-properties-common
#		sudo add-apt-repository ppa:certbot/certbot
#		sudo apt-get update
#		sudo apt-get install -y python-certbot-nginx 
#		sudo certbot --nginx
		
		echo "nginx installed"
fi

#php7.0-fpm
if [[ -f $(which php-fpm7.0 2>/dev/null) ]]
	then
		echo "php-fpm7.0 is already installed"
	else
		echo "Installing php-fpm7.0"
		sudo apt-get -y install nginx php7.0 php7.0-fpm php7.0-dev php7.0-mbstring php-pear pkg-config
		sudo cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.original
		sudo cp default /etc/nginx/sites-available/
		echo "php-fpm7.0 installed"
fi

#MongoDB
if [[ -f $(which mongod 2>/dev/null) ]]
	then
		echo "MongoDB is already installed"
	else
		echo "Installing MongoDB"
		sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 2930ADAE8CAF5059EE73BB4B58712A2291FA4AD5
		echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.6 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.6.list
		sudo apt-get update
		sudo apt-get install -y mongodb-org composer
		sudo pecl install mongodb
		echo "extension=mongodb.so" | sudo tee -a /etc/php/7.0/fpm/php.ini
		echo "extension=mongodb.so" | sudo tee -a /etc/php/7.0/cli/php.ini
		cd /var/www/html/
		sudo composer require mongodb/mongodb
		echo "MongoDB installed"
fi

sudo chown -R www-data /var/www/html
sudo chgrp -R www-data /var/www/html

#start services
sudo systemctl restart mongod.service
sudo systemctl restart php7.0-fpm.service
sudo systemctl restart nginx.service
