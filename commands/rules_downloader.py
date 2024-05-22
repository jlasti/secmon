#!/usr/bin/env python3
# encoding: utf-8

import os
import sys
import yaml

enviroment = sys.argv[1] # supported values: web, os

if enviroment == "os":
    # Execute this if rules update called in deployment
    yaml_config = open('./config/secmon_config.yaml')
    config = yaml.safe_load(yaml_config)
    repo_url = config['RULES_REPOSITORY']['url']

    os.system(f'git clone {repo_url} ../.git/temp/rules_repository')
    os.system(f'mv ../.git/temp/rules_repository/normalization/* ./rules/normalization/available/')
    os.system(f'mv ../.git/temp/rules_repository/correlation/* ./rules/correlation/available/')
    os.system(f'rm -rf ../.git/temp/rules_repository')
    os.system('chmod -R 777 ./rules/')

elif enviroment == 'web':
    # Execute this if rules update called from web
    rules = sys.argv[2] # supported values: correlation, normalization
    yaml_config = open('/var/www/html/secmon/config/secmon_config.yaml')
    config = yaml.safe_load(yaml_config)
    repo_url = config['RULES_REPOSITORY']['url']

    os.system(f'git clone {repo_url} ./assets/temp/rules_repository')
    if rules == 'correlation':
        os.system(f'mv ./assets/temp/rules_repository/correlation/* /var/www/html/secmon/rules/correlation/available/')
    elif rules == 'normalization':
        os.system(f'mv ./assets/temp/rules_repository/normalization/* /var/www/html/secmon/rules/normalization/available/')
    os.system(f'rm -rf ./assets/temp')
