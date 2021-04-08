#!/usr/bin/env python
# encoding: utf-8

import datetime
import pandas
import psycopg2
import re
import numpy as np
from collections import defaultdict
from sequence import pad_sequences
from backports import configparser
from minisom import MiniSom
from text import one_hot

config = configparser.ConfigParser()
config.read('/var/www/html/secmon/config/anomaly_config.ini')

columnsToAnalyze = config['MINISOM']['columns_to_analyze']
cleanText = config.getboolean('MINISOM', 'clean_text')
wholeText = config.getboolean('MINISOM', 'whole_text')
ipOctet = int(config['MINISOM']['ip_octet'])
somShape = (int(config['MINISOM']['number_of_clusters_x']), int(config['MINISOM']['number_of_clusters_y']))
numberOfIteration = int(config['MINISOM']['number_of_iteration'])
sigma = float(config['MINISOM']['sigma'])
learningRate = float(config['MINISOM']['learning_rate'])
randomSeed = int(config['MINISOM']['random_seed'])

# vytvorenie regexu na odstranovanie specialnych znakov
delimiter = "!", "\"", "#", "$", "%%", "&", "(", ")", "*", "+", ",", "-", ".", "/", ":", ";", "<", "=", ">", "?", "@", "[", "\\", "]", "^", "_", "`", "{", "|", "}", "~", "\t", "\n"
regexPattern = '|'.join(map(re.escape, delimiter))


def connect_to_db():
    conn = None

    try:
        conn = psycopg2.connect(host=config['DATABASE']['host'], database=config['DATABASE']['database'],
                                user=config['DATABASE']['user'], password=config['DATABASE']['password'])
    except Exception as e:
        print(f"\nI am unable to connect to the database -> {e}\n")
    return conn


def select_from_db(connection, sql, data):
    cursor = connection.cursor()

    try:
        cursor.execute(sql, data)
        data = cursor.fetchall()
    except Exception as e:
        print(f"\nCannot get data from SecMon databaze -> {e}\n")
    cursor.close()

    return data


def insert_to_db(connection, som, originalData, paddedData):
    cursor = connection.cursor()

    insertRunSql = (
        "INSERT INTO clustered_events_runs (datetime, type_of_algorithm, comment)"
        "VALUES (%s,%s,%s) RETURNING id"
    )

    insertClusterSql = (
        "INSERT INTO clustered_events_clusters (comment, fk_run_id)"
        "VALUES (%s,%s) RETURNING id"
    )

    insertRelationSql = (
        "INSERT INTO clustered_events_relations (fk_run_id, fk_cluster_id, fk_event_id)"
        "VALUES (%s,%s,%s)"
    )

    updateRunStatisticsSql = (
        "UPDATE clustered_events_runs "
        "SET number_of_clusters=%s "
        "WHERE id=%s"
    )

    selectClusterStatisticsSql = (
        "SELECT MAX(cef_severity), COUNT(*) FROM events_normalized "
        "LEFT JOIN clustered_events_relations "
        "ON events_normalized.id=clustered_events_relations.fk_event_id "
        "WHERE clustered_events_relations.fk_cluster_id=%s"
    )

    updateClustersStatisticsSql = (
        "UPDATE clustered_events_clusters "
        "SET severity=%s, number_of_events=%s "
        "WHERE id=%s"
    )

    insertRunData = (datetime.datetime.now(), 'miniSOM', '')

    try:
        clusters = defaultdict(list)
        cursor.execute(insertRunSql, insertRunData)
        runId = cursor.fetchone()[0]

        for (event_id, event) in zip(originalData['id'], paddedData):
            winninPosition = som.winner(event)
            key = winninPosition[1] * somShape[1] + winninPosition[0]
            clusters[key].append(event_id)

        updateRunStatisticsData = (len(clusters), runId)
        cursor.execute(updateRunStatisticsSql, updateRunStatisticsData)

        for key in clusters:
            insertClusterData = ("", runId)
            cursor.execute(insertClusterSql, insertClusterData)
            clusterId = cursor.fetchone()[0]

            for item in clusters[key]:
                insertRelationData = (runId, clusterId, item)
                cursor.execute(insertRelationSql, insertRelationData)

            selectClusterStatisticsData = (clusterId,)
            cursor.execute(selectClusterStatisticsSql, selectClusterStatisticsData)
            statistics = cursor.fetchone()
            updateClustersStatisticsData = statistics + (clusterId,)
            cursor.execute(updateClustersStatisticsSql, updateClustersStatisticsData)

        connection.commit()
    except Exception as e:
        connection.rollback()
        print(f"\nI can't execute insert script! : {e}\n")
    cursor.close()


def prepare_data(raw_data):
    prepared_data = ''

    # iterate though selected columns
    for column in columnsToAnalyze.replace(' ', '').split(','):
        print(f'    Preparing column -> {column}')

        # fill empty cells with empty string
        raw_data[column] = raw_data[column].fillna('')

        # analyze whole ip or specified octet
        if (column == 'src_ip' or column == 'dst_ip') and config['MINISOM']['ip_octet']:
            raw_data[column] = raw_data[column].str.replace('.', 'x', ipOctet - 1).str.replace('.', ' ')
        else:
            # clean text from special characters
            if cleanText: raw_data[column] = raw_data[column].str.replace(regexPattern, ' ')

            # remove white space from text -> analyze according whole text
            if wholeText: raw_data[column] = raw_data[column].str.replace(' ', '')

        # add white space bettwen rows
        if isinstance(prepared_data, np.ndarray):
            prepared_data = np.char.add(prepared_data, ' ')

        # spojenie stlpcov do jedneho
        prepared_data = np.char.add(prepared_data, raw_data[column].tolist())

    return prepared_data.tolist()


def encode_data(prepared_data):
    return [one_hot(item, 1000000) for item in prepared_data]


def normalize_data(encode_data):
    normalized_data = []

    max_value = max([max(array) for array in encode_data])
    min_value = min([min(array) for array in encode_data])

    for array in encode_data:
        normalized = [float(item - min_value) / (max_value - min_value) for item in array]
        normalized_data.append(normalized)

    return normalized_data


def get_size_of_longest_data(normalized_data):
    return max([len(i) for i in normalized_data])


def padding_data(normalized_data, size_of_longest_data):
    return pad_sequences(normalized_data, maxlen=size_of_longest_data, padding='post', dtype=float)


def miniSOM(paddedData, sizeOfLongestData):
    som = MiniSom(somShape[0], somShape[1], sizeOfLongestData,
                  sigma=sigma,
                  learning_rate=learningRate,
                  random_seed=randomSeed)
    som.train(np.array(paddedData), numberOfIteration, random_order=True, verbose=True)
    return som


if __name__ == '__main__':

    # connect to SecMon database
    print('Connecting to SecMon database...')
    connection = connect_to_db()

    # get rawData from events_normalized table
    print('Loading data from SecMon database...')
    selectSql = "SELECT * FROM events_normalized"
    selectData = ()

    if config['MINISOM']['not_older_than']:
        selectSql += " WHERE datetime >= %s"
        selectData = (config['MINISOM']['not_older_than'],)

    selectSql += " ORDER BY datetime DESC"

    if config['MINISOM']['number_of_events']:
        selectSql += " LIMIT %s"
        selectData = selectData + (config['MINISOM']['number_of_events'],)

    rawData = select_from_db(connection, selectSql, selectData)

    if not rawData:
        exit("Input data are empty!")

    # get column headers from events_normalized table
    print('Loading headers from SecMon database...')
    headersSql = (
        "SELECT column_name FROM information_schema.columns "
        "WHERE table_schema = 'public' AND table_name = %s"
    )
    headersData = ('events_normalized',)

    columnHeaders = [columnName[0] for columnName in select_from_db(connection, headersSql, headersData)]
    # convert rawData from events_normalized table to dataFrame
    print('Converting data to dataFrame...')
    dataFrame = pandas.DataFrame(rawData, columns=columnHeaders, dtype=str)
    dataFrameCopy = dataFrame.copy()

    # select columns and create list of rows
    print('Preparing data...')
    preparedData = prepare_data(dataFrame)

    # encode list of strings
    print('Encoding data...')
    encodedData = encode_data(preparedData)

    # normalize data to <0,1>
    print('Normalizing data...')
    normalizedData = normalize_data(encodedData)

    # get longes list
    print('Finding longest data...')
    sizeOfLongestData = get_size_of_longest_data(normalizedData)

    # add padding according longest list
    print('Padding unevenly data...')
    paddedData = padding_data(normalizedData, sizeOfLongestData)

    # start miniSOM
    print('Starting miniSOM...')
    som = miniSOM(paddedData, sizeOfLongestData)

    # insert to database
    print('Inserting to db...')
    insert_to_db(connection, som, dataFrameCopy, paddedData)

    connection.close()
    print('End...')

