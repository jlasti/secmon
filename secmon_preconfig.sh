#!/bin/bash
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

echo -e "Creating log directory"
sudo mkdir /var/log/secmon
sudo chmod 777 /var/log/secmon

echo -e "Copying rsyslog and logrotate config files"
sudo cp deployment/config_files/rsyslog.conf /etc/
sudo cp deployment/config_files/logrotate.conf /etc/logrotate.d/secmon

echo -e "Secmon preconfiguration is complete, to deploy SecMon run command \"python3 secmon_manaher.py deploy\""
python3 secmon_manaher.py deploy