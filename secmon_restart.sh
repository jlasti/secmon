#!/bin/bash

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

docker-compose restart
docker exec -d secmon-app python3.9 ./commands/db_retention.py

echo -ne "Restarting "
docker restart secmon-aggregator
echo -e " ... ${GREEN}Done${NORMAL}"

echo -ne "Restarting "
docker restart secmon-normalizer
echo -e " ... ${GREEN}Done${NORMAL}"

echo -ne "Restarting "
docker restart secmon-geoip
echo -e " ... ${GREEN}Done${NORMAL}"

echo -ne "Restarting "
docker restart secmon-network
echo -e " ... ${GREEN}Done${NORMAL}"

echo -ne "Restarting "
docker restart secmon-correlator
echo -e " ... ${GREEN}Done${NORMAL}"
