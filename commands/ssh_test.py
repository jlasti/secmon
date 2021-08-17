#!/usr/bin/env python3
# encoding: utf-8

#requirements:
#   pip3 install paramiko

import paramiko
from paramiko import SSHClient
from paramiko.ssh_exception import AuthenticationException

for i in range(60):
    try:
        client = SSHClient()
        #client.load_system_host_keys()
        #client.load_host_keys('~/.ssh/known_hosts')
        client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        client.connect('127.0.0.1', username='swexo', password='secret')
    except AuthenticationException:
            print("SSH no.",i + 1,": Authentication failed.")
    client.close()

