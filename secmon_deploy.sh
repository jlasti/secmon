#!/bin/bash

# Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

# Check log file and restart Rsyslog
FILE=/var/log/docker/secmon.log
if test -f "$FILE"; then
	TIMESTAMP=$(date +%Y%m%d)
	mv $FILE /var/log/docker/secmon.log.old-"$TIMESTAMP" || { echo -e "${RED}Renaming old /var/log/docker/secmon.log failed!${NORMAL}" ; exit 1; }
	systemctl restart rsyslog.service || { echo -e "${RED}Restarting rsyslog service failed${NORMAL}" ; exit 1; }
fi

# Password creation
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

# Update placeholder password in configuration files
echo -e "Updating passwords in configuration files"
sed -i "s/<password>/$password1/g" config/db.php \
&& sed -i "s/<password>/$password1/g" config/anomaly_config.ini \
&& sed -i "s/<password>/$password1/g" config/secmon_config.yaml \
&& sed -i "s/<password>/$password1/g" docker-compose.yml \
|| { echo "${RED}Updating passwords in configuration file templates failed${NORMAL}" ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

# Docker setup
echo -e "Building docker images"
docker pull php:7.4-fpm || { echo "${RED}Pulling docker image failed${NORMAL}" ; exit 1; }
docker build -t secmon_base -f deployment/dockerfiles/secmon_base.Dockerfile ./ \
&& docker build -t secmon_geoip -f deployment/dockerfiles/secmon_geoip.Dockerfile ./deployment \
&& docker build -t secmon_network_model -f deployment/dockerfiles/secmon_network_model.Dockerfile ./deployment \
&& docker build -t secmon_correlator -f deployment/dockerfiles/secmon_correlator.Dockerfile ./deployment \
&& docker build -t secmon_db_retention -f deployment/dockerfiles/secmon_db_retention.Dockerfile ./deployment \
&& docker build -t secmon_cti -f deployment/dockerfiles/secmon_cti.Dockerfile ./deployment \
|| { echo "${RED}Building docker images failed${NORMAL}" ; exit 1; }

docker compose build || { echo "${RED}Docker compose build failed${NORMAL}" ; exit 1; }

docker run -d --rm --name secmon_app -v "${PWD}":/var/www/html/secmon secmon_app && echo -e "\r\033[1A\033[0KCreating temporary container ... ${GREEN}done${NORMAL}"
docker exec secmon_app composer update
docker stop secmon_app && echo -e "\r\033[1A\033[0KRemoving temporary container ... ${GREEN}done${NORMAL}"
