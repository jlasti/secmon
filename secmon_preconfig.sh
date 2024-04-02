#!/bin/bash

# ------------------------------------------------ #
#           SecMon configuration script
#   Automatically executed once before deployment
# ------------------------------------------------ #

# Set up colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NORMAL='\033[0m'
MAGENTA='\033[0;35m'

# List of dependencies
dependencies=("git" "firewalld" "openssl" "systemctl" "python3" "docker")

# Function to check if a dependency is installed
check_dependency() {
    command -v "$1" >/dev/null 2>&1 || { echo -e "${RED}Package $1 is not installed${NORMAL}"; exit 1; }
}

echo -e "${MAGENTA}Starting SecMon configuration.${NORMAL}"

# Check required packages
echo -e "${YELLOW}Checking for required dependencies${NORMAL}"
for dependency in "${dependencies[@]}"; do
    check_dependency "$dependency"
    echo "$dependency found"
done
echo -e "${GREEN}Done${NORMAL}"

# Add required groups
echo -e "${YELLOW}Creating groups${NORMAL}"
group_name=$(getent group 33 | cut -d ':' -f 1)
if [[ "$group_name" != "www-data" ]]; then
    groupdel "$group_name"
    groupadd -g 33 www-data
fi
groupadd secmon
echo -e "${GREEN}Done${NORMAL}"

# Create and set permissions for directories
echo -e "${YELLOW}Creating log directory${NORMAL}"
mkdir -p /var/log/secmon || { echo -e "${RED}Creating directory /var/log/secmon failed${NORMAL}" ; exit 1; }
chmod 600 /var/log/secmon || { echo -e "${RED}Changing access mode of the directory /var/log/secmon failed${NORMAL}" ; exit 1; }

mkdir -p ./rules/normalization/.bin || { echo -e "${RED}Creating directory rules/normalization/.bin failed${NORMAL}" ; exit 1; }
mkdir -p ./rules/normalization/active || { echo -e "${RED}Creating directory rules/normalization/active failed${NORMAL}" ; exit 1; }
mkdir -p ./rules/normalization/available || { echo -e "${RED}Creating directory rules/normalization/available failed${NORMAL}" ; exit 1; }
mkdir -p ./rules/normalization/ui || { echo -e "${RED}Creating directory rules/normalization/ui failed${NORMAL}" ; exit 1; }

mkdir -p ./rules/correlation/.bin || { echo -e "${RED}Creating directory rules/correlation/.bin failed${NORMAL}" ; exit 1; }
mkdir -p ./rules/correlation/active || { echo -e "${RED}Creating directory rules/correlation/active failed${NORMAL}" ; exit 1; }
mkdir -p ./rules/correlation/available || { echo -e "${RED}Creating directory rules/correlation/available failed${NORMAL}" ; exit 1; }
mkdir -p ./rules/correlation/ui || { echo -e "${RED}Creating directory rules/correlation/ui failed${NORMAL}" ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

# Create pids directory
mkdir -p ./pids

# Generate SSL certificates
COUNTRY="SK"
STATE="Slovakia"
CITY="Bratislava"
ORGANIZATION="secmon"
COMMON_NAME="localhost"

echo -e "${YELLOW}Generating SSL certificates in './deployment/certificates'${NORMAL}"
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

# Copy config files from /deployment - system related
echo -e "${YELLOW}Copying config files${NORMAL}"
cp deployment/config_files/secmon.conf /etc/rsyslog.d/ \
&& cp deployment/config_files/docker.conf /etc/rsyslog.d/ \
&& cp deployment/config_files/secmon.logrotate /etc/logrotate.d/secmon \
|| { echo -e "${RED}Copying config files failed${NORMAL}" ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

# Copy configuration file templates - secmon related
echo -e "Copying configuration file templates"
cp deployment/config_templates/db.php config/ \
&& cp deployment/config_templates/anomaly_config.ini config/ \
&& cp deployment/config_templates/secmon_config.ini config/ \
&& cp deployment/config_templates/docker-compose.yml . \
|| { echo -e "${RED}Copying configuration file templates failed!${NORMAL}" ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

# Download rules from repository configured in secmon_config.ini
echo -e "${YELLOW}Starting download of SecMon rules${NORMAL}"
python3 ./commands/rules_downloader.py os \
|| { echo -e "${RED}Download of SecMon rules failed${NORMAL}" ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

# Set 777 permissions so container secmon_app can write to directories
chgrp www-data .
chmod 777 ./web/assets/
chmod -R 777 ./rules/* || { echo -e "${RED}Changing access mode of the directory ./rules/* failed${NORMAL}" ; exit 1; }

# Create lock file as a sign a config was run.
echo -e "${YELLOW}Creating lock file${NORMAL}"
touch ./config/.lock
chmod 700 ./config/.lock
echo -e "${GREEN}Done${NORMAL}"

# Restart services
echo -e "${YELLOW}Restarting services${NORMAL}"
systemctl restart rsyslog.service || { echo -e "${RED}Restarting rsyslog service failed${NORMAL}" ; exit 1; }
systemctl daemon-reload || { echo -e "${RED}Reloading docker daemon failed${NORMAL}" ; exit 1; }
systemctl restart docker || { echo -e "${RED}Restarting docker service failed${NORMAL}" ; exit 1; }
echo -e "${GREEN}Done${NORMAL}"

echo -e "${MAGENTA}SecMon configuration is complete.${NORMAL}"