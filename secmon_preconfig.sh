#!/bin/bash

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

sudo yum clean all
sudo yum -y update

echo -e "Installing usefull packages"
sudo yum install -y firewalld rsyslog
sudo yum install -y https://repo.ius.io/ius-release-el7.rpm
sudo yum install -y python36u python36u-libs python36u-devel python36u-pip
sudo pip3.6 install -U configparser
sudo pip3.6 install termcolor

echo -e "Setting up firewall"
sudo firewall-cmd --permanent --add-port=80/tcp
sudo firewall-cmd --permanent --add-port=443/tcp
sudo firewall-cmd --permanent --add-port=514/tcp
sudo firewall-cmd --reload
echo -e "${GREEN}Done${NORMAL}"

echo -e "Creating log directory"
sudo mkdir /var/log/secmon
sudo chmod 777 /var/log/secmon
echo -e "${GREEN}Done${NORMAL}"

echo -e "Copying config files"
sudo cp deployment/config_files/rsyslog.conf /etc/
sudo cp deployment/config_files/logrotate.conf /etc/logrotate.d/secmon
cp deployment/config_files/db.php config/
cp deployment/config_files/anomaly_config.ini config/
cp deployment/config_files/middleware_config.ini config/
cp deployment/docker-compose.yml .
echo -e "${GREEN}Done${NORMAL}"

echo -e "Secmon preconfiguration is complete, to deploy SecMon run command \"python3 secmon_manager.py deploy\""