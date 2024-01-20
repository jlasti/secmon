#!/usr/bin/env python3
# encoding: utf-8

import configparser
import os

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
NORMAL='\033[0m'

config = configparser.ConfigParser()
config.read('./config/secmon_config.ini')

repo_url = config.get('RULES_REPOSITORY', 'url')

os.system(f'git clone {repo_url} ../.git/temp/rules_repository')
os.system('mv ../.git/temp/rules_repository/normalization/* ./rules/normalization/available/')
os.system('mv ../.git/temp/rules_repository/correlation/* ./rules/correlation/available/')
os.system('rm -rf ../.git/temp/rules_repository')
