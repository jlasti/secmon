#!/usr/bin/env python3
# encoding: utf-8

import configparser
import os
import sys

enviroment = sys.argv[1] # supported values: web, os

if enviroment == "os":
    # Execute this if rules update called in deployment, configuration...
    config = configparser.ConfigParser()
    config.read('./config/secmon_config.ini')
    repo_url = config.get('RULES_REPOSITORY', 'url')

    os.system(f'git clone {repo_url} ../.git/temp/rules_repository')
    os.system(f'mv ../.git/temp/rules_repository/normalization/* ./rules/normalization/available/')
    os.system(f'mv ../.git/temp/rules_repository/correlation/* ./rules/correlation/available/')
    os.system(f'rm -rf ../.git/temp/rules_repository')
elif enviroment == 'web':
    rules = sys.argv[2] # supported values: c, n
    # Execute this if rules update called from web
    config = configparser.ConfigParser()
    config.read('/var/www/html/secmon/config/secmon_config.ini')
    repo_url = config.get('RULES_REPOSITORY', 'url')

    os.system(f'git clone {repo_url} ./assets/temp/rules_repository')
    if rules == 'c':
        os.system(f'mv ./assets/temp/rules_repository/correlation/* /var/www/html/secmon/rules/correlation/available/')
    elif rules == 'n':
        os.system(f'mv ./assets/temp/rules_repository/normalization/* /var/www/html/secmon/rules/normalization/available/')
    os.system(f'rm -rf ./assets/temp')
