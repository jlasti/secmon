#!/bin/bash

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

echo -e "Copying config files"
cp deployment/config_files/db.php config/ && cp deployment/config_files/anomaly_config.ini config/ && cp deployment/config_files/secmon_config.ini config/ && cp deployment/docker-compose.yml . || { echo 'Copying config files failed! Make sure, that you run command from secmon directory.' ; exit 1; }
#cp deployment/config_files/anomaly_config.ini config/ || { echo 'Copying config files failed! Make sure, that you run command from secmon directory.' ; exit 1; }
#cp deployment/config_files/secmon_config.ini config/ || { echo 'Copying config files failed! Make sure, that you run command from secmon directory.' ; exit 1; }
#cp deployment/docker-compose.yml . || { echo 'Copying config files failed! Make sure, that you run command from secmon directory.' ; exit 1; }
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
		then echo -e "${RED}Entered passwords do not match, try again...${NORMAL}"; continue;
		else break;
	fi
done
echo -e "${GREEN}Password successfully created${NORMAL}"

#update password in config files
sed -i "s/<password>/$password1/g" config/db.php || { echo 'Updating password in config/db.php failed' ; exit 1; }
sed -i "s/<password>/$password1/g" config/anomaly_config.ini || { echo 'Updating password in config/anomaly_config.ini failed' ; exit 1; }
sed -i "s/<password>/$password1/g" config/secmon_config.ini || { echo 'Updating password in config/secmon_config.ini failed' ; exit 1; }
sed -i "s/<password>/$password1/g" docker-compose.yml || { echo 'Updating password in docker-compose.yml failed' ; exit 1; }

docker pull php:7.4-fpm
docker build -t secmon_base -f deployment/dockerfiles/secmon_base.Dockerfile ./ || { echo 'docker build secmon_base image failed' ; exit 1; }
docker build -t secmon_geoip -f deployment/dockerfiles/secmon_geoip.Dockerfile ./deployment || { echo 'docker build secmon_geoip image failed' ; exit 1; }
docker build -t secmon_network_model -f deployment/dockerfiles/secmon_network_model.Dockerfile ./deployment || { echo 'docker build secmon_network_model image failed' ; exit 1; }
docker build -t secmon_correlator -f deployment/dockerfiles/secmon_correlator.Dockerfile ./deployment || { echo 'docker build secmon_correlator image failed' ; exit 1; }
docker build -t secmon_db_retention -f deployment/dockerfiles/secmon_db_retention.Dockerfile ./deployment || { echo 'docker build secmon_db_retention image failed' ; exit 1; }

docker compose build || { echo 'docker compose build failed' ; exit 1; }

docker run -d --rm --name secmon_app -v ${PWD}:/var/www/html/secmon secmon_app && echo -e "\r\033[1A\033[0KCreating temporary container ... ${GREEN}done${NORMAL}"
docker exec secmon_app composer update
docker stop secmon_app && echo -e "\r\033[1A\033[0KRemoving temporary container ... ${GREEN}done${NORMAL}"
