#!/bin/bash

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

echo -e "Installing usefull packages"
sudo yum install -y firewalld

echo -e "Setting up firewall"
sudo firewall-cmd --permanent --add-port=80/tcp
sudo firewall-cmd --permanent --add-port=443/tcp
sudo firewall-cmd --permanent --add-port=514/tcp
sudo firewall-cmd --reload

sudo mkdir /var/log/secmon
sudo chmod 777 /var/log/secmon

echo -e "Copying config files"
sudo cp docker-compose/rsyslog.conf /etc/
sudo cp docker-compose/logrotate.conf /etc/logrotate.d/secmon
cp docker-compose/db.php config/
cp docker-compose/anomaly_config.ini config/
cp docker-compose/aggregator_config.ini config/
cp docker-compose/middleware_config.ini config/
cp docker-compose/docker-compose.yml .
echo -e "${GREEN}Done${NORMAL}"

#Password creating
echo Create password for database user \'secmon\'
while true; do
	read -s -p "Enter Password: " password1
	echo
	
	if [ "${password1,,}" = "password" ];
		then echo -e "${RED}Entered passwor is forbidden, try again...${NORMAL}"; continue;
	fi
	
	if [ ${#password1} -lt 8 ];
		then echo -e "${RED}Entered password is shorten than 8 characters, try again...${NORMAL}"; continue;
	fi
	
	read -s -p "Re-enter Password: " password2
	echo

	if [ "${password1}" != "$password2" ]
		then echo -e "${RED}Sorry, passwords do not match, try again...${NORMAL}"; continue;
		else break;
	fi
done
echo -e "${GREEN}Password succesfully created${NORMAL}"

#update password in install and config files
sed -i "s/<password>/$password1/g" config/db.php
sed -i "s/<password>/$password1/g" config/anomaly_config.ini
sed -i "s/<password>/$password1/g" config/middleware_config.ini
sed -i "s/<password>/$password1/g" docker-compose.yml

docker-compose build
docker build -t secmon_base -f ./docker-compose/secmon_base.Dockerfile ./docker-compose/
docker build -t secmon_aggregator -f ./docker-compose/secmon_aggregator.Dockerfile ./docker-compose/
docker build -t secmon_normalizer -f ./docker-compose/secmon_normalizer.Dockerfile ./docker-compose/
docker build -t secmon_geoip -f ./docker-compose/secmon_geoip.Dockerfile ./docker-compose/
docker build -t secmon_network -f ./docker-compose/secmon_network.Dockerfile ./docker-compose/
docker build -t secmon_correlator -f ./docker-compose/secmon_correlator.Dockerfile ./docker-compose/

docker-compose up -d
docker exec secmon-app composer update
docker exec -it secmon-app ./yii migrate --interactive=0
docker exec -it secmon-app chgrp -R www-data .

echo -e "Initializing SecMon admin user ..."
curl 127.0.0.1:8080/secmon/web/user/init

docker-compose stop