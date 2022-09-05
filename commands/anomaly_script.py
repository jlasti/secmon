#!/usr/bin/env python
# encoding: utf-8

import re
import datetime
import math
import numpy as np
import pandas
import psycopg2
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
    rs, cs = np.where(D == 0)
    # the rows, cols must be shuffled because we will keep the first duplicate below
    index_shuf = list(range(len(rs)))
    np.random.shuffle(index_shuf)
    rs = rs[index_shuf]
    cs = cs[index_shuf]
    for r, c in zip(rs, cs):
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
        J = np.argmin(D[:, M], axis=1)
        for kappa in range(k):
            C[kappa] = np.where(J == kappa)[0]
        # update cluster medoids
        for kappa in range(k):
            J = np.mean(D[np.ix_(C[kappa], C[kappa])], axis=1)
            j = np.argmin(J)
            Mnew[kappa] = C[kappa][j]
        np.sort(Mnew)
        # check for convergence
        if np.array_equal(M, Mnew):
            break
        M = np.copy(Mnew)
    else:
        # final update of cluster memberships
        J = np.argmin(D[:, M], axis=1)
        for kappa in range(k):
            C[kappa] = np.where(J == kappa)[0]

    # return results
    return M, C


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


def insert_to_db(connection, clusters, ids):
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
        "SELECT MAX(cef_severity), COUNT(*) FROM security_events "
        "LEFT JOIN clustered_events_relations "
        "ON security_events.id=clustered_events_relations.fk_event_id "
        "WHERE clustered_events_relations.fk_cluster_id=%s"
    )

    updateClustersStatisticsSql = (
        "UPDATE clustered_events_clusters "
        "SET severity=%s, number_of_events=%s "
        "WHERE id=%s"
    )

    insertRunData = (datetime.datetime.now(), "k-median", "")

    try:
        cursor.execute(insertRunSql, insertRunData)
        runId = cursor.fetchone()[0]

        data = (len(clusters), runId)
        cursor.execute(updateRunStatisticsSql, data)

        for key in clusters:
            data = ("", runId)
            cursor.execute(insertClusterSql, data)
            clusterId = cursor.fetchone()[0]

            for item in clusters[key]:
                insertRelationData = (runId, clusterId, ids[item])
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


def euclidean_distance(array):
    if len(array) == 0:
        new_array = [[]]
        return new_array

    line_final = []
    for all_elements in array:
        temp_line = []
        for all_elemets_temp in array:
            line = make_euclidian_distance(all_elements, all_elemets_temp)
            temp_line.append(line)
        line_final.append(temp_line)

    return np.array(line_final)


def make_euclidian_distance(vectorX, vectorY):
    if len(vectorX) != len(vectorY):
        raise RuntimeWarning("The length of the two vectors are not the same!")

    zipVector = zip(vectorX, vectorY)
    distance = 0
    for pair_number in zipVector:
        distance += (pair_number[1] - pair_number[0]) ** 2

    return math.sqrt(distance)


def loadDataFromDB(connection):
    selectSql = "SELECT * FROM security_events"
    selectData = ()

    if config['KMEDIAN']['not_older_than']:
        if re.search("^\\d+w \\d+d \\d+h \\d+m \\d+s$", config['KMEDIAN']['not_older_than']):
            time = np.fromstring(re.sub("[^0-9 ]", "", config['KMEDIAN']['not_older_than']), dtype=np.float, sep=' ')
            selectData = ((datetime.datetime.today() - datetime.timedelta(weeks=time[0], days=time[1], hours=time[2], minutes=time[3], seconds=time[4])).strftime("%Y-%m-%d %H:%M:%S"),)
        else:
            selectData = (config['KMEDIAN']['not_older_than'],)
        selectSql += " WHERE datetime >= %s"

    selectSql += " ORDER BY datetime DESC"

    if config['KMEDIAN']['number_of_events']:
        selectSql += " LIMIT %s"
        selectData = selectData + (config['KMEDIAN']['number_of_events'],)

    rawData = select_from_db(connection, selectSql, selectData)

    if not rawData:
        exit("Input data are empty!")

    # get column headers from security_events table
    print('Loading headers from SecMon database...')
    headersSql = (
        "SELECT column_name FROM information_schema.columns "
        "WHERE table_schema = 'public' AND table_name = %s"
    )
    headersData = ('security_events',)

    columnHeaders = [columnName[0] for columnName in select_from_db(connection, headersSql, headersData)]

    # convert rawData from security_events table to dataFrame
    print('Converting data to dataFrame...')
    dataFrame = pandas.DataFrame(rawData, columns=columnHeaders, dtype=str)

    return dataFrame


if __name__ == '__main__':
    # connect to SecMon database
    print('Connecting to SecMon database...')
    connection = connect_to_db()

    # get rawData from security_events table
    print('Loading data from SecMon database...')
    dataFrame = loadDataFromDB(connection)

    print('Preparing data...')
    dataFrame = dataFrame.fillna('0')
    dataFrameIds = dataFrame['id']
    columns_to_analyze = config['KMEDIAN']['columns_to_analyze'].split(",")
    transformed = dataFrame[columns_to_analyze].values.tolist()

    # transform to number
    print('Encoding data...')
    senetense_to_nums = []
    for sentense in transformed:
        word_to_nums = []
        for word in sentense:
            for character in word:
                number = ord(character)
                word_to_nums.append(number)
            word_to_nums.append(32)
        senetense_to_nums.append(word_to_nums)
    str_temp = " ".join(str(x) for x in senetense_to_nums)
    output_str = str_temp.replace(" ", ",")

    # fill to max length
    print('Finding longest data...')
    maxLenght = max([len(i) for i in senetense_to_nums])

    print('Padding unevenly data...')
    for sentenses in range(0, len(senetense_to_nums)):
        for i in range(len(senetense_to_nums[sentenses]), maxLenght):
            senetense_to_nums[sentenses].append(0)

    # distance matrix
    print('Calculating euclidean distance...')
    D = euclidean_distance(senetense_to_nums)

    # split into x clusters
    print('Setting number of clusters...')
    x = int(config['KMEDIAN']['clusters'])

    print('Starting k-median...')
    M, C = kMedoids(D, x)

    print('Inserting to db...')
    insert_to_db(connection, C, dataFrameIds)

    connection.close()
    print('End')
