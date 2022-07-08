#!/usr/bin/env python3
# encoding: utf-8

import configparser
import sys
import os
import fileinput
import re
import time

RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

def run_enrichment_modul(name, port):
    command = f'docker run -d --restart unless-stopped --name secmon_{name} --network secmon_app-network --expose {port} -v ${{PWD}}:/var/www/html/secmon secmon_{name}'
    if os.system(command) == 0:
        os.system(f'echo -e "\r\033[1A\033[0KCreating secmon_{name} ... {GREEN}done{NORMAL}"')

#method for starting stopped containers
def start_secmon_containers(enabled_enrichment_modules):
    print("Starting secmon modules")
    os.system('docker-compose start')
    os.system('docker exec -d secmon_app python3.9 ./commands/db_retention.py')
    for module in enabled_enrichment_modules:
        command = f'docker ps --filter "name=secmon_{module}" | grep -q . && docker start secmon_{module}'
        if os.system(command) == 0:
            os.system(f'echo -e "\r\033[1A\033[0KStarting secmon_{module} ... {GREEN}done{NORMAL}"')

#method for restarting running/stopped containers
def restart_secmon_containers(all_enrichment_modules, enabled_enrichment_modules):
    #stopping
    stop_secmon_containers(all_enrichment_modules)
    
    #removing
    remove_secmon_containers(all_enrichment_modules)

    print("Restarting secmon modules:")
    os.system('docker-compose restart')
    os.system('docker exec -d secmon_app python3.9 ./commands/db_retention.py')

    config_file = open("./config/aggregator_config.ini", "r")
    contents = config_file.readlines()

    print("Creating secmon enrichment modules:")
    for module in enabled_enrichment_modules:
        if index_containing_substring(contents, module):
            port = int(re.findall('[0-9]+', contents[index_containing_substring(contents, module)])[0]) - 1
            run_enrichment_modul(module, port)

    #calculatiog port for correlator
    port = int(re.findall('[0-9]+', contents[len(contents)-1])[0])
    run_enrichment_modul('correlator', port)
    
    config_file.close

#method for stopping running containers
def stop_secmon_containers(all_enrichment_modules):
    print("Stopping secmon modules:")
    for module in all_enrichment_modules:
        command = f'docker ps --filter "name=secmon_{module}" | grep -q . && docker stop secmon_{module}'
        if os.system(command) == 0:
            os.system(f'echo -e "\r\033[1A\033[0KStopping secmon_{module} ... {GREEN}done{NORMAL}"')

#method for removing stopped containers
def remove_secmon_containers(all_enrichment_modules):
    print("Removing secmon modules:")
    for module in all_enrichment_modules:
        command = f'docker ps --filter "name=secmon_{module}" | grep -q . && docker rm secmon_{module}'
        if os.system(command) == 0:
            os.system(f'echo -e "\r\033[1A\033[0KRemoving secmon_{module} ... {GREEN}done{NORMAL}"')      

#Method taken from https://stackoverflow.com/questions/2170900/get-first-list-index-containing-sub-string
def index_containing_substring(the_list, substring):
    for i, s in enumerate(the_list):
        if substring.lower() in s.lower():
              return i
    return -1

def connect():
    return psycopg2.connect(host=config.get('DATABASE', 'host'),database=config.get('DATABASE', 'database'), user=config.get('DATABASE', 'user'), password=config.get('DATABASE', 'password'))
    
def path_validation(path, input_data):
    return (path in input_data)

def log_input_device_name_validation(name, input_data, index):
    if(not (name in input_data)):
        return False

    tmp = input_data.split("/")
    if(tmp[index] != name):
        return False
    else:
        return True


def validate(config):
    errorMsg = "Validation unsuccessful, found these errors: "
    error = 0

    #db connection validation
    #if(not connect()):
    #    errorMsg += '\n' + "Unable to connect to the database! Please check database credentials."
    #    error = 1

    #log_input path validation
    if(not path_validation("/var/log/", config.get('DEVICE', 'log_input'))):
        errorMsg += '\n' + "Log input must contain /var/log/ path! Please change the path."
        error = 1

    #device name in log_input path validation
    if(not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('DEVICE', 'log_input'), 3)):
        errorMsg += '\n' + "Source directory of log input path must have a same name as device name! Please rename source directory on log input path."
        error = 1

    #normalization input named pipe path validation
    if(not path_validation("/var/log/", config.get('NORMALIZATION', 'input_NP'))):
        errorMsg += '\n' + "Path of normalization INPUT naped pipe must contain /var/log/ path! Please change the path."
        error = 1
    
    #device name in normalization input naped pipe validation
    if(not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('NORMALIZATION', 'input_NP'), 3)):
        errorMsg += '\n' + "Source directory of normalization INPUT named pipe must have a same name as device name! Please rename source directory on log input path."
        error = 1

    #normalization output named pipe path validation
    if(not path_validation("/var/log/", config.get('NORMALIZATION', 'output_NP'))):
        errorMsg += '\n' + "Path of normalization OUTPUT naped pipe must contain /var/log/ path! Please change the path."
        error = 1
    
    #device name in normalization output naped pipe validation
    if(not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('NORMALIZATION', 'output_NP'), 3)):
        errorMsg += '\n' + "Source directory of normalization OUTPUT named pipe must have a same name as device name! Please rename source directory on log input path."
        error = 1

    #correlation input named pipe path validation
    if(not path_validation("/var/www/html/", config.get('CORRELATION', 'input_NP'))):
        errorMsg += '\n' + "Path of correlation INPUT naped pipe must contain /var/www/html/ path! Please change the path."
        error = 1
    
    #device name in correlation input naped pipe validation
    if(not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('CORRELATION', 'input_NP'), 4)):
        errorMsg += '\n' + "Source directory of correlation INPUT named pipe must have a same name as device name! Please rename source directory on log input path."
        error = 1

    #correlation output named pipe path validation
    if(not path_validation("/var/www/html/", config.get('CORRELATION', 'output_NP'))):
        errorMsg += '\n' + "Path of correlation OUTPUT naped pipe must contain /var/www/html/ path! Please change the path."
        error = 1
    
    #device name in correlation output naped pipe validation
    if(not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('CORRELATION', 'output_NP'), 4)):
        errorMsg += '\n' + "Source directory of correlation OUTPUT named pipe must have a same name as device name! Please rename source directory on log input path."
        error = 1

    if(error == 1):
        print(errorMsg)
        return False
    else:
        return True

def assign_output_named_pipes(type, named_pipe):
    path_to_rules = "./rules/active/" + type
    for file in os.listdir(path_to_rules):
        if file.endswith(".rule"):
            file_to_open = path_to_rules + "/" + file
            for line in fileinput.input(file_to_open, inplace=1):
                if "write" in line and line[0] != "#":
                    index = line.find("$")
                    index2 = line.find("/")
                    line = line.replace(line, line[0:index2] + named_pipe + line[index - 1:])
                sys.stdout.write(line)

    path_to_rules = "./rules/available/" + type
    for file in os.listdir(path_to_rules):
        if file.endswith(".rule"):
            file_to_open = path_to_rules + "/" + file
            for line in fileinput.input(file_to_open, inplace=1):
                if "write" in line and line[0] != "#":
                    index = line.find("$")
                    index2 = line.find("/")
                    line = line.replace(line, line[0:index2] + named_pipe + line[index - 1:])
                sys.stdout.write(line)

def change_log_input_directory(log_input):
    sys_config = "/etc/rsyslog.conf"
    for line in fileinput.input(sys_config, inplace=1):
        if "/var/log/" in line and "local7.*" not in line and "uucp,news.crit" not in line and "cron.*" not in line and "mail.* " not in line:
            index = line.find("/")
            index2 = line.find("/", index + len("var/log/") + 1)
            line = line.replace(line, line[0:index] + log_input + line[index2:])
        sys.stdout.write(line)

if len(sys.argv) < 2 or sys.argv[1] == "help":
    print("Available parrameters are:\n")
    print("\"status\" - to print out actual SecMon status")
    print("\"deploy\" - to deploy SecMon")
    print("\"start\" - to start stopped SecMon containers")
    print("\"restart\" - to restart SecMon")
    print("\"stop\" - to stop SecMon")
    print("\"remove\" - to remove all SecMon containers with database")
    print("\"help\" - to list all available parameters\n")
    sys.exit()

#read configuration file
config = configparser.ConfigParser()
config.read('./config/middleware_config.ini')

all_enrichment_modules = ['geoip', 'network_model', 'correlator']
enabled_enrichment_modules = []

if sys.argv[1] == "status":
    if os.system(f'docker ps -a | grep -q secmon_ && echo -e "{GREEN}SecMon is not running{NORMAL}" || echo -e "{RED}SecMon is not running{NORMAL}"') == 0:
        os.system(f'docker ps -a | grep secmon_ && echo running')
    sys.exit()

if sys.argv[1] == "stop":
    stop_secmon_containers(all_enrichment_modules)
    os.system('docker-compose stop')
    sys.exit()

if sys.argv[1] == "remove":
    stop_secmon_containers(all_enrichment_modules)
    os.system('docker-compose stop')
    remove_secmon_containers(all_enrichment_modules)
    os.system('docker-compose down')
    sys.exit()


#input data validation
if(not validate(config)):
    sys.exit()

#write data to temp file for system services
port = 5557
aggregator_conf_file = open("./config/aggregator_config.ini", "w+")
aggregator_conf_file.write("Log_input: %s\nName: %s\n" % (config.get('DEVICE', 'log_input'), config.get('DEVICE', 'name')))

aggregator_conf_file.write("Nor_input_NP: %s\nNor_output_NP: %s\n" % (config.get('NORMALIZATION', 'input_NP'), config.get('NORMALIZATION', 'output_NP')))
aggregator_conf_file.write("Cor_input_NP: %s\nCor_output_NP: %s\n" % (config.get('CORRELATION', 'input_NP'), config.get('CORRELATION', 'output_NP')))

#write 0MQ port for aggregator
aggregator_conf_file.write("Aggregator: %d\n" % port)
port += 1
#write 0MQ port for normalizer
aggregator_conf_file.write("Normalizer: %d\n" % port)
port += 1

if config.get('ENRICHMENT', 'geoip').lower() == "true":
    #write 0MQ port for geoip
    aggregator_conf_file.write("Geoip: %d\n" % port)
    port += 1
    enabled_enrichment_modules.append('geoip')

if config.get('ENRICHMENT', 'network_model').lower() == "true":
    #write 0MQ port for network_model
    aggregator_conf_file.write("Network_model: %d\n" % port)
    port += 1
    enabled_enrichment_modules.append('network_model')

# if config.get('ENRICHMENT', 'rep_ip').lower() == "true":
#     #write 0MQ port for rep_ip
#     aggregator_conf_file.write("Rep_ip: %d\n" % port)
#     port += 1

aggregator_conf_file.close()

if sys.argv[1] == "deploy":
    answer = input("Deploying SecMon will remove all existing SecMon containers and existing SecMon database. This process also includes setting up different config files and creating new SecMon containers.\nDo you want to still deploy SecMon? [y/N] ")
    if answer == "N":
        sys.exit()
    elif answer == "y":
        stop_secmon_containers(all_enrichment_modules)
        remove_secmon_containers(all_enrichment_modules)
        os.system('docker-compose down')
        os.system('./secmon_deploy.sh')
        os.system('docker-compose -p secmon up -d')
        config_file = open("./config/aggregator_config.ini", "r")
        contents = config_file.readlines()

        for module in enabled_enrichment_modules:
            if index_containing_substring(contents, module):
                port = int(re.findall('[0-9]+', contents[index_containing_substring(contents, module)])[0]) - 1
                run_enrichment_modul(module, port)

        #calculation port for correlator
        port = int(re.findall('[0-9]+', contents[len(contents)-1])[0])
        run_enrichment_modul('correlator', port)
        config_file.close

        print('Waiting for database to be ready to receive connections...')
        time.sleep(1)
        while os.system('docker logs secmon_db 2>&1 | grep -q "database system is ready to accept connections"') != 0:
            print('Waiting for database to be ready to receive connections...')
            time.sleep(1)

        os.system('docker exec -it secmon_app ./yii migrate --interactive=0')
        os.system('docker exec -it secmon_app chgrp -R www-data .')
        os.system('docker exec -d secmon_app python3.9 ./commands/db_retention.py')
        os.system('echo -e "Initializing SecMon admin user ..."')
        os.system('curl 127.0.0.1:8080/secmon/web/user/init')
    else:
        sys.exit()

#start stopped containers
if sys.argv[1] == "start":
    start_secmon_containers(all_enrichment_modules)

#restart running containers
if sys.argv[1] == "restart":
    restart_secmon_containers(all_enrichment_modules, enabled_enrichment_modules)