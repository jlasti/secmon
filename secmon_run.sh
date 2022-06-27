#!/bin/bash

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

#echo -e "Copying config files..."
#copy configuration and installation files
#cp docker-compose/db.php config/
#cp docker-compose/anomaly_config.ini config/
#cp docker-compose/aggregator_config.ini config/
#echo -e "${GREEN}Done${NORMAL}"


docker stop $(docker ps -a | grep secmon- | cut -d " " -f 1)

docker-compose down
docker-compose build
docker-compose up -d

docker build -t secmon_base -f docker-compose/secmon_base.Dockerfile docker-compose/
docker build -t secmon_aggregator -f docker-compose/secmon_aggregator.Dockerfile docker-compose/
docker build -t secmon_normalizer -f docker-compose/secmon_normalizer.Dockerfile docker-compose/
docker build -t secmon_geoip -f docker-compose/secmon_geoip.Dockerfile docker-compose/
docker build -t secmon_network -f docker-compose/secmon_network.Dockerfile docker-compose/
docker build -t secmon_correlator -f docker-compose/secmon_correlator.Dockerfile docker-compose/

docker exec secmon-app composer update
docker exec -it secmon-app ./yii migrate --interactive=0
docker exec -it secmon-app chgrp www-data web/assets
docker exec -d secmon-app python3.9 ./commands/db_retention.py

echo -e "Starting secmon Aggregator "
docker run -d --name secmon-aggregator --network secmon_app-network -v ${PWD}:/var/www/html/secmon -v /var/log/secmon:/var/log/secmon secmon_aggregator

echo -e "Starting secmon Normalizer "
docker run -d  --name secmon-normalizer --network secmon_app-network --expose 5557 -v ${PWD}:/var/www/html/secmon -v /var/log/secmon:/var/log/secmon secmon_normalizer

echo -e "Starting secmon GeoIP "
docker run -d --name secmon-geoip --network secmon_app-network --expose 5558 -v ${PWD}:/var/www/html/secmon secmon_geoip

echo -e "Starting secmon Network "
docker run -d --name secmon-network --network secmon_app-network --expose 5559 -v ${PWD}:/var/www/html/secmon secmon_network

echo -e "Starting secmon Correlator "
docker run -d --name secmon-correlator --network secmon_app-network --expose 5560 -v ${PWD}:/var/www/html/secmon secmon_correlator

#echo -e "Initializing SecMon admin user ..."
curl 127.0.0.1:8080/secmon/web/user/init
