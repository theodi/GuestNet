#!/bin/bash

sudo add-apt-repository ppa:kohana/stable
sudo sed -i "s/`lsb_release -cs`/maverick/" /etc/apt/sources.list.d/kohana-stable-lucid.list

sudo apt-get update
sudo apt-get install git-core libkohana3.2-core-php libkohana3.2-mod-auth-php libkohana3.2-mod-cache-php libkohana3.2-mod-codebench-php libkohana3.2-mod-database-php libkohana3.2-mod-image-php libkohana3.2-mod-orm-php libkohana3.2-mod-unittest-php mysql-client mysql-server php5-mysql libmysqlclient15-dev


sudo a2enmod mod_rewrite
sudo /etc/init.d/apache2 restart
sudo /etc/init.d/mysql restart

sudo mkdir -p /srv/www/default/application
sudo cp /usr/share/php/kohana3.2/index.php /srv/www/default

cd /srv/www/default
