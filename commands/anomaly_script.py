#!/usr/bin/env python
# encoding: utf-8

# Place anomaly_daemon.service to /etc/systemd/system/ - run as every service
# Change in anomaly_daemon.service working directory as you wish, defaul is /var/www/working_dir

import pandas
import numpy as np
import datetime
import psycopg2
import random
import time
import math
from backports import configparser

config = configparser.ConfigParser()
config.read('/var/www/html/secmon/config/anomaly_config.ini')

# Bauckhage C. Numpy/scipy Recipes for Data Science: k-Medoids Clustering[R]. Technical Report, University of Bonn, 2015.
def kMedoids(D, k, tmax=100000):
    # determine dimensions of distance matrix D
    m, n = D.shape

    if k > n:
        raise Exception('too many medoids')

    # find a set of valid initial cluster medoid indices since we
    # can't seed different clusters with two points at the same location
    valid_medoid_inds = set(range(n))
    invalid_medoid_inds = set([])
    rs,cs = np.where(D==0)
    # the rows, cols must be shuffled because we will keep the first duplicate below
    index_shuf = list(range(len(rs)))
    np.random.shuffle(index_shuf)
    rs = rs[index_shuf]
    cs = cs[index_shuf]
    for r,c in zip(rs,cs):
        # if there are two points with a distance of 0...
        # keep the first one for cluster init
        if r < c and r not in invalid_medoid_inds:
            invalid_medoid_inds.add(c)
    valid_medoid_inds = list(valid_medoid_inds - invalid_medoid_inds)

    if k > len(valid_medoid_inds):
        raise Exception('too many medoids (after removing {} duplicate points)'.format(
            len(invalid_medoid_inds)))

    # randomly initialize an array of k medoid indices
    M = np.array(valid_medoid_inds)
    np.random.shuffle(M)
    M = np.sort(M[:k])

    # create a copy of the array of medoid indices
    Mnew = np.copy(M)

    # initialize a dictionary to represent clusters
    C = {}
    for t in range(tmax):
        # determine clusters, i. e. arrays of data indices
        J = np.argmin(D[:,M], axis=1)
        for kappa in range(k):
            C[kappa] = np.where(J==kappa)[0]
        # update cluster medoids
        for kappa in range(k):
            J = np.mean(D[np.ix_(C[kappa],C[kappa])],axis=1)
            j = np.argmin(J)
            Mnew[kappa] = C[kappa][j]
        np.sort(Mnew)
        # check for convergence
        if np.array_equal(M, Mnew):
            break
        M = np.copy(Mnew)
    else:
        # final update of cluster memberships
        J = np.argmin(D[:,M], axis=1)
        for kappa in range(k):
            C[kappa] = np.where(J==kappa)[0]

    # return results
    return M, C

def connect_to_db():
    try:
        conn = psycopg2.connect(host=config['DATABASE']['host'],database=config['DATABASE']['database'], user=config['DATABASE']['user'], password=config['DATABASE']['password'])
    except:
        print ("I am unable to connect to the database")
    return conn

def select_from_db(connection, sql):
    cursor = connection.cursor()

    try:
        cursor.execute(sql)
        data = cursor.fetchall()
    except Exception as e:
        print(f"\nCannot get data from SecMon databaze!\n{e}")
    cursor.close()

    return data

def insert_to_db(connection, clusters, ids):
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

    data = (datetime.datetime.now(), "k-Median", "")

    try:
        cursor.execute(run_sql, data)
        run_id = cursor.fetchone()[0]

        for key in clusters:
            data = ("", run_id)
            cursor.execute(cluster_sql, data)
            cluster_id = cursor.fetchone()[0]

            for id in clusters[key]:
                data = (run_id, cluster_id, ids[id])
                cursor.execute(relation_sql, data)

            data = (cluster_id,)
            cursor.execute(select_cluster_statistics_sql, data)
            statistics = cursor.fetchone()
            data = statistics + (cluster_id,)
            cursor.execute(update_clusters_statistics_sql, data)

        data = (run_id,)
        cursor.execute(select_run_statistics_sql, data)
        statistics = cursor.fetchone()[0]
        data = (statistics, run_id)
        cursor.execute(update_run_statistics_sql, data)

        connection.commit()
    except Exception as e:
        connection.rollback()
        print(f"\nI can't execute insert script! : {e}\n")
    cursor.close()

def insert(raw, number,run,comment, conn):
    cur = conn.cursor()
    sql = (
        "INSERT INTO clustered_events (time, raw, cluster_number, cluster_run, comment)"
        "VALUES (%s,%s,%s,%s,%s)"
    )
    data = (datetime.datetime.now(),raw,number,run,comment)
    try:
        cur.execute(sql,data)
        conn.commit()
    except:
        conn.rollback()
        print ("I can't execute insert script!")
    cur.close()

def euclidean_distance(array):
    if(len(array) == 0):
        new_array = [[]]
        return new_array

    line_final = []
    for all_elements in array:
        temp_line = []
        for all_elemets_temp in array:
            line = make_euclidian_distance(all_elements,all_elemets_temp)
            temp_line.append(line)
        line_final.append(temp_line)

    return np.array(line_final)

def make_euclidian_distance(vectorX, vectorY):
    if(len(vectorX) != len(vectorY)):
        raise RuntimeWarning("The length of the two vectors are not the same!")

    zipVector = zip(vectorX, vectorY)
    distance = 0
    for pair_number in zipVector:
        distance += (pair_number[1] - pair_number[0]) ** 2

    return math.sqrt(distance)

if __name__ == '__main__':
    # connect to SecMon database
    connection = connect_to_db()

    select_all_sql = (
        "SELECT * FROM events_normalized"
    )
    rawData = select_from_db(connection, select_all_sql)

    select_headers_sql = (
        "SELECT column_name FROM information_schema.columns "
        "WHERE table_schema = 'public' AND table_name = 'events_normalized'"
    )
    columnHeaders = [columnName[0] for columnName in select_from_db(connection, select_headers_sql)]
    
    dataFrame = pandas.DataFrame(rawData, columns=columnHeaders, dtype=str)
    dataFrame = dataFrame.fillna('')
    
    dataFrameIds = dataFrame['id']

    columns_to_analyze = config['DATA']['columns_to_analyze'].split(",")
    transformed = dataFrame[columns_to_analyze].values.tolist()

    # transform to number
    senetense_to_nums = []
    for sentense in transformed:
        word_to_nums = []
        for word in sentense:
            for character in word:
                number = ord(character)
                word_to_nums.append(number)
            word_to_nums.append(32)
        senetense_to_nums.append(word_to_nums);
    str_temp = " ".join(str(x) for x in senetense_to_nums)
    output_str = str_temp.replace(" ", ",")

    # fill to max length
    max = max([len(i) for i in senetense_to_nums])

    for sentenses in range(0, len(senetense_to_nums)):
        for i in range(len(senetense_to_nums[sentenses]), max):
            senetense_to_nums[sentenses].append(0)

    # distance matrix
    D = euclidean_distance(senetense_to_nums)

    # split into x clusters
    x = int(config['KMEDIAN']['clusters'])

    M, C = kMedoids(D, x)

    insert_to_db(connection, C, dataFrameIds)

    connection.close()   
