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
sudo cp deployment/config_files/secmon.conf /etc/rsyslog.d/
sudo cp deployment/config_files/docker.conf /etc/rsyslog.d/
sudo cp deployment/config_files/daemon.json /etc/docker/
sudo cp deployment/config_files/secmon_logrotate /etc/logrotate.d/
cp deployment/config_files/db.php config/
cp deployment/config_files/anomaly_config.ini config/
cp deployment/config_files/secmon_config.ini config/
cp deployment/docker-compose.yml .
echo -e "${GREEN}Done${NORMAL}"

sudo systemctl restart rsyslog.service
sudo systemctl daemon-reload
sudo systemctl restart docker

echo -e "Secmon preconfiguration is complete, to deploy SecMon run command \"python3 secmon_manager.py deploy\""