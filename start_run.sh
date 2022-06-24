#!/bin/bash

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

echo -e "Copying config files..."
#copy configuration and installation files
cp docker-compose/db.php config/
cp docker-compose/anomaly_config.ini config/
cp docker-compose/aggregator_config.ini config/
cp docker-compose/docker-compose.yml .

echo -e "${GREEN}Done${NORMAL}"

docker-compose down
docker-compose build
docker-compose up -d

docker exec secmon-app composer update
docker exec secmon-app ./yii migrate --interactive=0
docker exec secmon-app chgrp www-data web/assets
docker exec -d secmon-app python3.9 ./commands/db_retention.py

#echo -e "Initializing SecMon admin user ..."
curl 127.0.0.1:8080/secmon/web/user/init
