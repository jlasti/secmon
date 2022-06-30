#!/bin/bash

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

echo -e "Stopping secmon containers..."
docker stop $(docker ps -a | grep 'secmon-correlator\|secmon-network\|secmon-geoip' | cut -d " " -f 1)

echo -e "Removing secmon containers..."
docker rm $(docker ps -a | grep 'secmon-correlator\|secmon-network\|secmon-geoip' | cut -d " " -f 1)

docker-compose down
docker-compose build
docker build -t secmon_base -f deployment/dockerfiles/secmon_base.Dockerfile deployment/dockerfiles/
docker build -t secmon_geoip -f deployment/dockerfiles/secmon_geoip.Dockerfile deployment/dockerfiles/
docker build -t secmon_network -f deployment/dockerfiles/secmon_network.Dockerfile deployment/dockerfiles/
docker build -t secmon_correlator -f deployment/dockerfiles/secmon_correlator.Dockerfile deployment/dockerfiles/


echo 'creating tmp container secmon-app'
docker run -d --rm --name secmon-app -v ${PWD}:/var/www/html/secmon secmon_app
docker exec secmon-app composer update
docker stop secmon-app
echo 'removing tmp container secmon-app'

docker ps -a

docker-compose up --project-name secmon -d

#echo -e "Starting secmon Aggregator "
#docker run -d --restart unless-stopped --name secmon-aggregator --network secmon_app-network -v ${PWD}:/var/www/html/secmon -v /var/log/secmon:/var/log/secmon secmon_aggregator

#echo -e "Starting secmon Normalizer "
#docker run -d --restart unless-stopped --name secmon-normalizer --network secmon_app-network --expose 5557 -v ${PWD}:/var/www/html/secmon -v /var/log/secmon:/var/log/secmon secmon_normalizer

echo -e "Starting secmon GeoIP "
docker run -d --restart unless-stopped --name secmon-geoip --network secmon_app-network --expose 5558 -v ${PWD}:/var/www/html/secmon secmon_geoip

echo -e "Starting secmon Network "
docker run -d --restart unless-stopped --name secmon-network --network secmon_app-network --expose 5559 -v ${PWD}:/var/www/html/secmon secmon_network

echo -e "Starting secmon Correlator "
docker run -d --restart unless-stopped --name secmon-correlator --network secmon_app-network --expose 5560 -v ${PWD}:/var/www/html/secmon secmon_correlator

#docker exec secmon-app composer update
docker exec -it secmon-app ./yii migrate --interactive=0
docker exec -it secmon-app chgrp -R www-data .
docker exec -d secmon-app python3.9 ./commands/db_retention.py

echo -e "Initializing SecMon admin user ..."
curl 127.0.0.1:8080/secmon/web/user/init