#!/bin/bash

./yii normalizer >> error.log &
sec --conf=/var/www/html/secmon/rules/active/normalization/*.rule --input=/var/log/secmon/__secOutput --bufsize=1 --detach

