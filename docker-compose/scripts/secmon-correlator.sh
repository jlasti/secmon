#!/bin/bash

./yii correlator >> error.log &
sec --conf=/var/www/html/secmon/rules/active/correlation/*.rule --input=/var/www/html/secmon/__secOutput --bufsize=1 --detach
