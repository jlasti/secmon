#!/bin/bash

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NORMAL='\033[0m'

echo -e "Creating log directory"
sudo mkdir -p /var/log/secmon || { echo -e "${RED}Creating directory /var/log/secmon failed${NORMAL}" ; exit 1; }
sudo chmod 600 /var/log/secmon || { echo -e "${RED}Changing access mode of the directory /var/log/secmon failed${NORMAL}" ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"


# generating SSL files
COUNTRY="SK"
STATE="Slovakia"
CITY="Bratislava"
ORGANIZATION="secmon"
COMMON_NAME="localhost"
echo -e "${YELLOW}Generating files for SSL${NORMAL}"
echo -e "Generating certificate directory"
sudo mkdir ./deployment/certificates
echo -e "Generating a private server RSA key"
sudo openssl genrsa -out ./deployment/certificates/server.key 2048
echo -e "Generating certificate signing request"
sudo openssl req -key ./deployment/certificates/server.key -new -out ./deployment/certificates/server.csr \
        -subj "/C=$COUNTRY/ST=$STATE/L=$CITY/O=$ORGANIZATION/CN=$COMMON_NAME"
echo -e "Generating self-signed certificate"
sudo openssl x509 -signkey ./deployment/certificates/server.key -in ./deployment/certificates/server.csr -req -days 365 -out ./deployment/certificates/server.crt
sudo openssl x509 -signkey ./deployment/certificates/server.key -in ./deployment/certificates/server.csr -req -days 365 -out ./deployment/certificates/server.pem
echo -e "${GREEN}Generating certificates for SSL done ${NORMAL}"

# remove directory not writable by web process error
sudo chgrp www-data ./web/assets
sudo chmod 777 ./web/assets/


echo -e "Copying config files"
sudo cp deployment/config_files/secmon.conf /etc/rsyslog.d/ \
&& sudo cp deployment/config_files/docker.conf /etc/rsyslog.d/ \
&& sudo cp deployment/config_files/daemon.json /etc/docker/ \
&& sudo cp deployment/config_files/secmon_logrotate /etc/logrotate.d/ \
&& cp deployment/config_files/db.php config/ \
&& cp deployment/config_files/anomaly_config.ini config/ \
&& cp deployment/config_files/secmon_config.ini config/ \
&& cp deployment/docker-compose.yml . \
|| { echo -e "${RED}Copying config files failed${NORMAL}" ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

sudo systemctl restart rsyslog.service || { echo -e "${RED}Restarting rsyslog service failed${NORMAL}" ; exit 1; }
sudo systemctl daemon-reload || { echo -e "${RED}Reloading docker daemon failed${NORMAL}" ; exit 1; }
sudo systemctl restart docker || { echo -e "${RED}Restarting docker service failed${NORMAL}" ; exit 1; }

echo -e "Secmon preconfiguration is complete, to deploy SecMon run command \"python3 secmon_manager.py deploy\""
