#!/bin/bash

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

echo -e "Creating log directory"
sudo mkdir -p /var/log/secmon || { echo -e "${RED}Creating directory /var/log/secmon failed${NORMAL}" ; exit 1; }
sudo chmod 777 /var/log/secmon || { echo -e "${RED}Changing access mode of the directory /var/log/secmon failed${NORMAL}" ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

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

sudo systemctl restart rsyslog.service
sudo systemctl daemon-reload
sudo systemctl restart docker

echo -e "Secmon preconfiguration is complete, to deploy SecMon run command \"python3 secmon_manager.py deploy\""
