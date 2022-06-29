#!/bin/bash

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

echo -e "Installing usefull packages"
#sudo yum install -y firewalld

echo -e "Setting up firewall"
#sudo firewall-cmd --permanent --add-port=80/tcp
#sudo firewall-cmd --permanent --add-port=443/tcp
#sudo firewall-cmd --permanent --add-port=514/tcp
#sudo firewall-cmd --reload

#sudo mkdir /var/log/secmon
#sudo chmod 777 /var/log/secmon

echo -e "Copying config files"
#sudo cp deployment/config_files/rsyslog.conf /etc/
#sudo cp deployment/config_files/logrotate.conf /etc/logrotate.d/secmon
cp deployment/config_files/db.php config/
cp deployment/config_files/anomaly_config.ini config/
cp deployment/config_files/middleware_config.ini config/
cp deployment/docker-compose.yml .
echo -e "${GREEN}Done${NORMAL}"

#Password creating
echo Create password for database user \'secmon\'
while true; do
	read -s -p "Enter Password: " password1
	echo
	
	if [ "${password1,,}" = "password" ];
		then echo -e "${RED}Entered password is forbidden, try again...${NORMAL}"; continue;
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
docker build -t secmon_base -f deployment/dockerfiles/secmon_base.Dockerfile deployment/dockerfiles/
#docker build -t secmon_aggregator -f deployment/dockerfiles/secmon_aggregator.Dockerfile deployment/dockerfiles/
#docker build -t secmon_normalizer -f deployment/dockerfiles/secmon_normalizer.Dockerfile deployment/dockerfiles/
docker build -t secmon_geoip -f deployment/dockerfiles/secmon_geoip.Dockerfile deployment/dockerfiles/
docker build -t secmon_network -f deployment/dockerfiles/secmon_network.Dockerfile deployment/dockerfiles/
docker build -t secmon_correlator -f deployment/dockerfiles/secmon_correlator.Dockerfile deployment/dockerfiles/

docker run -d --rm --name secmon-app -v ${PWD}:/var/www/html/secmon secmon_app
docker exec secmon-app composer update
docker stop secmon-app
echo -e "${RED}removing tmp container secmon-app${NORMAL}"