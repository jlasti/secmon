#!/usr/bin/env python3
# encoding: utf-8

import configparser
import psycopg2
import sys
import os
import time
import datetime

def connect():
    try:
        conn = psycopg2.connect(host=config.get('DATABASE', 'host'),database=config.get('DATABASE', 'database'), user=config.get('DATABASE', 'user'), password=config.get('DATABASE', 'password'))
    except:
        os.system('echo -e "Connection to the database was unsuccessful"')
    return conn

def size_check(max_db_size):
    os.system('echo -e "Proceeding database size check"')
    connection = connect()
    cursor = connection.cursor()
    querry = "SELECT pg_size_pretty(pg_database_size(\'" + config.get('DATABASE', 'database') + "\'));"
    cursor.execute(querry)
    db_size = cursor.fetchone()
    act_size = db_size[0].split()
    if int(act_size[0]) > int(max_db_size):
        cursor.execute("SELECT count(id) from events_normalized")
        no_of_events = cursor.fetchone()
        events_to_delete = (no_of_events[0] / 100) * 15
        querry = ("DELETE from events_normalized where id in ("
            "SELECT id from events_normalized order by datetime asc limit (%s))")
        data = (events_to_delete,)
        cursor.execute(querry, data)
        connection.commit()
        connection.close()

def timestamp_check(last_date):
    os.system('echo -e "Proceeding database timestamp check"')
    connection = connect()
    cursor = connection.cursor()
    querry = ("DELETE from events_normalized where id in ("
            "SELECT id from events_normalized where datetime < (%s::TIMESTAMP))")
    data = (last_date,)
    cursor.execute(querry, data)
    connection.commit()
    connection.close()

#read configuration file
config = configparser.ConfigParser()
config.read('./config/middleware_config.ini')

max_db_size = config.get('DATABASE', 'max_size')
no_of_days = config.get('DATABASE', 'max_days')
sleep_interval= config.get('DATABASE', 'sleep_interval')
while True:
    size_check(max_db_size)
    dt = datetime.datetime.now()
    last_date = dt - datetime.timedelta(int(no_of_days))
    timestamp_check(last_date)
    time.sleep(int(sleep_interval))
    
