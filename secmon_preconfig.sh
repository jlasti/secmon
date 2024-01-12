#!/bin/bash

# Set up colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NORMAL='\033[0m'
MAGENTA='\033[0;35m'

# List of dependencies
dependencies=("git" "firewalld" "openssl" "systemctl" "python3")

# Function to check if a dependency is installed
check_dependency() {
    command -v $1 >/dev/null 2>&1 || { echo -e "${RED}Package $1 is not installed${NORMAL}"; exit 1; }
}

# Check required packages
echo -e "${YELLOW}Checking for required dependencies${NORMAL}"
for dependency in "${dependencies[@]}"; do
    check_dependency $dependency
    echo "$dependency found"
done
echo -e "${GREEN}Done${NORMAL}"

# Create and set permissions for log directory
echo -e "${YELLOW}Creating log directory${NORMAL}"
mkdir -p /var/log/secmon || { echo -e "${RED}Creating directory /var/log/secmon failed${NORMAL}" ; exit 1; }
chmod 600 /var/log/secmon || { echo -e "${RED}Changing access mode of the directory /var/log/secmon failed${NORMAL}" ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

# Generate SSL certificates
COUNTRY="SK"
STATE="Slovakia"
CITY="Bratislava"
ORGANIZATION="secmon"
COMMON_NAME="localhost"

echo -e "${YELLOW}Generating SSL cerificates in './deploment/certificates'${NORMAL}"
if [ ! -d "./deployment/certificates" ]; then
    mkdir "./deployment/certificates"
    echo "Created directory ./deployment/certificates"
fi

echo -e "Generating a private server RSA key"
openssl genrsa -out ./deployment/certificates/server.key 2048
echo -e "Generating certificate signing request"
openssl req -key ./deployment/certificates/server.key -new -out ./deployment/certificates/server.csr \
        -subj "/C=$COUNTRY/ST=$STATE/L=$CITY/O=$ORGANIZATION/CN=$COMMON_NAME"
echo -e "Generating self-signed certificate"
openssl x509 -signkey ./deployment/certificates/server.key -in ./deployment/certificates/server.csr -req -days 365 -out ./deployment/certificates/server.crt
echo -e "Generating self-signed certificate (PEM encoding)"
openssl x509 -signkey ./deployment/certificates/server.key -in ./deployment/certificates/server.csr -req -days 365 -out ./deployment/certificates/server.pem
echo -e "${GREEN}Done${NORMAL}"

# remove directory not writable by web process error
# chgrp www-data ./web/assets
# chmod 777 ./web/assets/

# Copy config files from /deployment
echo -e "${YELLOW}Copying config files${NORMAL}"
cp deployment/config_files/secmon.conf /etc/rsyslog.d/ \
&& cp deployment/config_files/docker.conf /etc/rsyslog.d/ \
&& cp deployment/config_files/secmon_logrotate /etc/logrotate.d/ \
&& cp deployment/config_files/db.php config/ \
&& cp deployment/config_files/anomaly_config.ini config/ \
&& cp deployment/config_files/secmon_config.ini config/ \
&& cp deployment/docker-compose.yml . \
|| { echo -e "${RED}Copying config files failed${NORMAL}" ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

# Restart services
echo -e "${YELLOW}Restarting services${NORMAL}"
systemctl restart rsyslog.service || { echo -e "${RED}Restarting rsyslog service failed${NORMAL}" ; exit 1; }
systemctl daemon-reload || { echo -e "${RED}Reloading docker daemon failed${NORMAL}" ; exit 1; }
# sudo systemctl restart docker || { echo -e "${RED}Restarting docker service failed${NORMAL}" ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

echo -e "${MAGENTA}Secmon preconfiguration is complete, to deploy SecMon run command \"python3 secmon_manager.py deploy\"${NORMAL}"
