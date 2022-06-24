#!/bin/bash

./yii aggregator >> error.log &
sec --conf=/var/www/html/secmon/rules/active/normalization/*.rule --input=/var/log/secmon/__secOutput --bufsize=1 --detach
sec --conf=/var/www/html/secmon/rules/active/correlation/*.rule --input=/var/www/html/secmon/__secOutput --bufsize=1 --detach
./yii normalizer >> error.log &
./yii correlator >> error.log &
./yii geoip >> error.log &
./yii network >> error.log &



