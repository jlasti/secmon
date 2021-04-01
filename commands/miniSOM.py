#!/usr/bin/env python
# encoding: utf-8

# Place anomaly_daemon.service to /etc/systemd/system/ - run as every service
# Change in anomaly_daemon.service working directory as you wish, defaul is /var/www/working_dir

import datetime
import pandas
import numpy as np
import psycopg2
import re
from collections import defaultdict
from backports import configparser

from sequence import pad_sequences
from text import one_hot
from minisom import MiniSom


config = configparser.ConfigParser()
config.read('/var/www/html/secmon/config/anomaly_config.ini')

columns_to_analyze = config['DATA']['columns_to_analyze']
clean_text = config.getboolean('MINISOM', 'clean_text')
whole_text = config.getboolean('MINISOM', 'whole_text')
whole_ip = config.getboolean('MINISOM', 'whole_ip')
whole_mac = config.getboolean('MINISOM', 'whole_mac')
som_shape = (int(config['MINISOM']['number_of_clusters_x']), int(config['MINISOM']['number_of_clusters_y']))
number_of_iteration = int(config['MINISOM']['number_of_iteration'])
sigma = float(config['MINISOM']['sigma'])
learning_rate = float(config['MINISOM']['learning_rate'])
random_seed = int(config['MINISOM']['random_seed'])

# vytvorenie regexu na odstranovanie specialnych znakov
delimiter = "!", "\"", "#", "$", "%%", "&", "(", ")", "*", "+", ",", "-", ".", "/", ":", ";", "<", "=", ">", "?", "@", "[", "\\", "]", "^", "_", "`", "{", "|", "}", "~", "\t", "\n"
regexPattern = '|'.join(map(re.escape, delimiter))

columns = ['id', 'datetime', 'host', 'cef_version', 'cef_vendor', 'cef_dev_prod', 'cef_dev_version', 'cef_event_class_id','cef_name', 'cef_severity', 'src_ip', 'dst_ip', 'src_port', 'dst_port', 'protocol', 'src_mac', 'dst_mac', 'extensions', 'raw', 'src_code', 'dst_code', 'src_country', 'dst_country', 'src_city', 'dst_city', 'src_latitude', 'dst_latitude', 'src_longitude', 'dst_longitude', 'analyzed']


def connect_to_db():
    try:
        conn = psycopg2.connect(host=config['DATABASE']['host'],database=config['DATABASE']['database'], user=config['DATABASE']['user'], password=config['DATABASE']['password'])
    except:
        print ("\nI am unable to connect to the database\n")
    return conn


def select_from_db(connection, sql, data):
    cursor = connection.cursor()

    try:
        cursor.execute(sql, data)
        data = cursor.fetchall()
    except:
        print("\nCannot get data from SecMon databaze!\n")
    cursor.close()

    return data


def insert_to_db(connection, som, dataFrameCopy, paddedData):
    cursor = connection.cursor()

    run_sql = (
        "INSERT INTO clustered_events_runs (datetime, type_of_algorithm, comment)"
        "VALUES (%s,%s,%s) RETURNING id"
    )

    cluster_sql = (
        "INSERT INTO clustered_events_clusters (comment, fk_run_id)"
        "VALUES (%s,%s) RETURNING id"
    )

    relation_sql = (
        "INSERT INTO clustered_events_relations (fk_run_id, fk_cluster_id, fk_event_id)"
        "VALUES (%s,%s,%s)"
    )

    select_run_statistics_sql = (
        "SELECT COUNT(*) FROM clustered_events_clusters "
        "WHERE fk_run_id=%s"
    )

    update_run_statistics_sql = (
        "UPDATE clustered_events_runs "
        "SET number_of_clusters=%s "
        "WHERE id=%s"
    )

    select_cluster_statistics_sql = (
        "SELECT MAX(cef_severity), COUNT(*) FROM events_normalized "
        "LEFT JOIN clustered_events_relations " 
        "ON events_normalized.id=clustered_events_relations.fk_event_id "
        "WHERE clustered_events_relations.fk_cluster_id=%s"
    )

    update_clusters_statistics_sql = (
        "UPDATE clustered_events_clusters "
        "SET severity=%s, number_of_events=%s "
        "WHERE id=%s"   
    )

    data = (datetime.datetime.now(),"miniSOM","")

    try:
        clusters = defaultdict(list)
        cursor.execute(run_sql, data)
        run_id = cursor.fetchone()[0]

        for (event_id, event) in zip(dataFrameCopy["id"], padded_data):
            winnin_position = som.winner(event)
            key = winnin_position[1] * som_shape[1] + winnin_position[0]
            clusters[key].append(event_id)
        
        for key in clusters:
            data = ("", run_id)
            cursor.execute(cluster_sql, data)
            cluster_id = cursor.fetchone()[0]
            
            for item in clusters[key]:
                data = (run_id, cluster_id, item)
                cursor.execute(relation_sql, data)
            
            data = (cluster_id,)
            cursor.execute(select_cluster_statistics_sql, data)
            statistics = cursor.fetchone()
            data = statistics + (cluster_id,)
            print(f'{cluster_id} = {data}')
            cursor.execute(update_clusters_statistics_sql, data)

        data = (run_id,)
        print(data)
        cursor.execute(select_run_statistics_sql, data)
        statistics = cursor.fetchone()[0]
        data = (statistics, run_id)
        print(data)
        cursor.execute(update_run_statistics_sql, data)
            
        connection.commit()
    except Exception as e:
        connection.rollback()
        print (f"\nI can't execute insert script! : {e}\n")
    cursor.close()


def prepare_data(raw_data):
    preprocessed_data = ''

    for column in columns_to_analyze.split(','):
        # vyplni sa kazda prazdna bunka hodnotou 0 ktora bude analyzovana

        print(column)

        raw_data[column] = raw_data[column].fillna('')

        # text nebude obsahovat specialne znaky
        if clean_text:
            raw_data[column] = raw_data[column].str.replace(regexPattern, '')

        # analyza textu bude na zaklade celeho textu a nie rozparsovaneho na zaklade medier
        if whole_text:
            raw_data[column] = raw_data[column].str.replace(' ', '')

        # analyza IP adresy bude podla celku alebo rozparsovana na oktety
        if (column == 'src_ip' or column == 'dst_ip') and whole_ip:
            raw_data[column] = raw_data[column].str.replace('.', '')

        # analyza MAC adresy bude podla celku alebo rozparsovana na oktety
        if (column == 'src_mac' or column == 'dst_mac') and whole_mac:
            raw_data[column] = raw_data[column].str.replace(':', '')

        # polia okrem prveho sa napajaju na seba medzerov
        if isinstance(preprocessed_data, np.ndarray):
            preprocessed_data = np.char.add(preprocessed_data, ' ')

        # spojenie stlpcov do jedneho
        preprocessed_data = np.char.add(preprocessed_data, raw_data[column].tolist())

    return preprocessed_data.tolist()


def encode_data(choosen_data):
    return [one_hot(item, 1000000) for item in choosen_data]


def normalize_data(encode_data):
    normalized_data = []

    max_value = max([max(array) for array in encode_data])
    min_value = min([min(array) for array in encode_data])

    for array in encode_data:
        normalized = [float(item - min_value) / (max_value - min_value) for item in array]
        normalized_data.append(normalized)
        normalized = []

    return normalized_data


def get_size_of_longest_data(normalized_data):
    return max([len(i) for i in normalized_data])


def padding_data(normalized_data, size_of_longest_data):
    return pad_sequences(normalized_data, maxlen=size_of_longest_data, padding='post', dtype=float)


def miniSOM(padded_data, size_of_longest_data):
    som = MiniSom(som_shape[0], som_shape[1], size_of_longest_data,
                  sigma=sigma,
                  learning_rate=learning_rate,
                  random_seed=random_seed)
    som.train(np.array(padded_data), number_of_iteration, random_order=True, verbose=True)
    return som


if __name__ == '__main__':
          
    #connect to SecMon database
    connection = connect_to_db()

    #get rawData from events_normalized table
    if config['MINISOM']['not_older_than']:
        sql = "SELECT * FROM events_normalized WHERE datetime >= %s ORDER BY datetime DESC LIMIT %s"
        data = (config['MINISOM']['not_older_than'], config['MINISOM']['number_of_events'])
    else:
        sql = "SELECT * FROM events_normalized ORDER BY datetime DESC LIMIT %s"
        data = (config['MINISOM']['number_of_events'],)

    rawData = select_from_db(connection, sql, data)

    if not rawData:
        exit("Input data are empty!")

    #get column headers from events_normalized table
    columnHeaders = [columnName[0] for columnName in select_from_db(connection, "SELECT column_name FROM information_schema.columns WHERE table_schema = 'public' AND table_name = %s", ('events_normalized',))]

    #convert rawData from events_normalized table to dataFrame
    dataFrame = pandas.DataFrame(rawData, columns=columnHeaders, dtype=str)
   
    #print(dataFrame)
    
    #create copy of dataFrame
    dataFrameCopy = dataFrame.copy()

    
    prepared_data = prepare_data(dataFrame)
    encoded_data = encode_data(prepared_data)
    normalized_data = normalize_data(encoded_data)
    size_of_longest_data = get_size_of_longest_data(normalized_data)
    padded_data = padding_data(normalized_data, size_of_longest_data)
    som = miniSOM(padded_data, size_of_longest_data)
    insert_to_db(connection, som, dataFrameCopy, padded_data)    
    
    connection.close()
    print("End")
