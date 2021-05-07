#!/bin/bash
cp docker-compose/db.php config/
cp docker-compose/anomaly_config.ini config/
docker-compose down
docker-compose build --no-cache
docker-compose up -d
docker exec -it app composer update
docker exec -it app ./yii migrate --interactive=0
sudo chown -R $USER:apache .
docker-compose restart