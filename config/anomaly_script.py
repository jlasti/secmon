#!/usr/bin/env python
# encoding: utf-8
# Place anomaly_daemon.service to /etc/systemd/system/ - run as every service
import numpy as np
import datetime
import psycopg2
import random
import time
from scipy.spatial import distance
from sklearn.metrics.pairwise import pairwise_distances

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

def insert(raw, number,run,comment):
    try:
        conn = psycopg2.connect(host="localhost",database="secmon", user="secmon", password="secmon")
    except:
        print ("I am unable to connect to the database")
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
    conn.close()

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

while True:
    # read logs from messages
    line_number = 0
    lines = []
    with open("/var/log/mkv/messages") as textFile:
        for i, line in enumerate(textFile):
            if i > line_number:
                lines.append(line.split())
                line_number = i
                # [['Mar', '6', '2019', '16:57:01', 'localhost', 'gdm-password]:gkr-pam:', 'unlocked', 'login', 'keyring']]

    # transform data
    # delete date
    transformed = []
    for line in lines:
        transformed.append(np.delete(line, np.s_[0:4], axis=0))
        # [['localhost' 'gdm-password]:gkr-pam:' 'unlocked' 'login' 'keyring']]

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
    senetense_to_nums.sort(key=len, reverse=True)
    for sentenses in range(1, len(senetense_to_nums)):
        for i in range(len(senetense_to_nums[sentenses]),len(senetense_to_nums[0])):
            senetense_to_nums[sentenses].append(0);

    # distance matrix
    D = euclidean_distance(senetense_to_nums)

    # split into x clusters
    run = 0
    x = 2
    M, C = kMedoids(D, x)

    for label in C:
        for point_idx in C[label]:
            nums_to_words = []
            final_sentences = []
            for number in senetense_to_nums[point_idx]:
                if (isinstance(number,int) and number > 0):
                    character = chr(number)
                    nums_to_words.append(character)
            str_temp = "".join(str(x) for x in nums_to_words)
            output_str = str_temp.replace(",", " ")
            output_str_final = output_str.strip(' ')
            insert(output_str_final,label,run,"")

    run = run + 1
    time.sleep(86400)
