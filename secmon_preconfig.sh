#!/bin/bash

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

echo -e "Creating log directory"
sudo mkdir /var/log/secmon || { echo 'Creating directory /var/log/secmon failed' ; exit 1; }
sudo chmod 777 /var/log/secmon || { echo 'Changing access mode of the directory /var/log/secmon failed' ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

echo -e "Copying config files"
sudo cp deployment/config_files/secmon.conf /etc/rsyslog.d/ || { echo 'Copying config files failed' ; exit 1; }
sudo cp deployment/config_files/docker.conf /etc/rsyslog.d/ || { echo 'Copying config files failed' ; exit 1; }
sudo cp deployment/config_files/daemon.json /etc/docker/ || { echo 'Copying config files failed' ; exit 1; }
sudo cp deployment/config_files/secmon_logrotate /etc/logrotate.d/ || { echo 'Copying config files failed' ; exit 1; }
cp deployment/config_files/db.php config/ || { echo 'Copying config files failed' ; exit 1; }
cp deployment/config_files/anomaly_config.ini config/ || { echo 'Copying config files failed' ; exit 1; }
cp deployment/config_files/secmon_config.ini config/ || { echo 'Copying config files failed' ; exit 1; }
cp deployment/docker-compose.yml . || { echo 'Copying config files failed' ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

sudo systemctl restart rsyslog.service
sudo systemctl daemon-reload
sudo systemctl restart docker

echo -e "Secmon preconfiguration is complete, to deploy SecMon run command \"python3 secmon_manager.py deploy\""
