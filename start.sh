#!/bin/bash

#copy configuration and installation files
cp docker-compose/db.php config/
cp docker-compose/anomaly_config.ini config/
cp docker-compose/docker-compose.yml .

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

#Password creating
echo Create password for database user \'secmon\'
while true; do
	read -s -p "Enter Password: " password1
	echo
	
	if [ "${password1,,}" = "password" ];
		then echo -e "${RED}Passwor: 'password' is forbidden, try again...${NORMAL}"; continue;
	fi
	
	if [ ${#password1} -lt 8 ];
		then echo -e "${RED}Entered password is shorten than 8 characters, try again...${NORMAL}"; continue;
	fi
	
	read -s -p "Re-enter Password: " password2
	echo
	if [ "${password1}" != "$password2" ]
		then echo -e "${RED}Sorry, passwords do not match, try again...${NORMAL}"; continue;
		else break;
	fi
done
echo -e "${GREEN}Password succesfully created${NORMAL}"

sed -i "s/<password>/$password1/g" config/db.php
sed -i "s/<password>/$password1/g" config/anomaly_config.ini
sed -i "s/<password>/$password1/g" docker-compose.yml

echo db.php
cat config/db.php | grep 9801153934
echo ----------------------------
echo anomaly_config.ini
cat config/anomaly_config.ini | grep 9801153934
echo ----------------------------
echo docker-compose.yml
cat docker-compose.yml | grep 9801153934
echo ----------------------------

docker-compose down
docker-compose build --no-cache
docker-compose up -d
docker exec -it app composer update
docker exec -it app ./yii migrate --interactive=0
sudo chown -R $USER:apache .
docker-compose restart
echo -e "${GREEN}Installation has been successfully completed${NORMAL}"
