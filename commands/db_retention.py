#!/usr/bin/env python3
# encoding: utf-8

from six.moves import configparser
import subprocess
import dbus
import psycopg2
import sys
import os
import fileinput
import time


def connect():
    try:
        conn = psycopg2.connect(host=config.get('DATABASE', 'host'),database=config.get('DATABASE', 'database'), user=config.get('DATABASE', 'user'), password=config.get('DATABASE', 'password'))
    except:
        print ("I am unable to connect to the database")
    return conn

#read configuration file
config = configparser.ConfigParser()
config.read('/var/www/html/secmon/config/middleware_config.ini')

connection = connect()
cursor = connection.cursor()
querry = "SELECT pg_size_pretty(pg_database_size(\'" + config.get('DATABASE', 'database') + "\'));"
cursor.execute(querry)
db_size = cursor.fetchone()
print(db_size[0])

while True:
    #chceck db_size, if its too big delete old normalized events
    #if not, check for stored events with timestamp higher than ...
    #if found delete them
    time.sleep(5)
    
