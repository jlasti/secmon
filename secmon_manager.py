#!/usr/bin/env python3
# encoding: utf-8

import configparser
import sys
import os
import fileinput # TODO: Remove not used
import re
import time

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
NORMAL='\033[0m'

# FIXME: upraviť 'docker compose down', prerobit aby correlation modul bol startovany ako enrichment module (teraz start hardcoded)

def print_help():
    print("Available parameters are:\n")
    print("\"deploy\" - to deploy SecMon")
    print("\"start\" - to start stopped SecMon containers")
    print("\"restart\" - to restart stopped/running SecMon containers")
    print("\"stop\" - to stop running SecMon containers")
    print("\"remove\" - to remove all SecMon containers with database")
    print("\"help\" - to list all available parameters\n")

# Run specific enrichment module
def run_enrichment_module(name):
    command = f'docker run -d --restart unless-stopped --name secmon_{name} --network secmon_app-network -v ${{PWD}}:/var/www/html/secmon secmon_{name}'
    if os.system(command) == 0:
        os.system(f'echo -e "\r\033[1A\033[0KCreating secmon_{name} ... {GREEN}done{NORMAL}"')
    else:
        os.system(f'echo -e "\r\033[1A\033[0KCreating secmon_{name} ... {RED}failed{NORMAL}"')

# Method for starting stopped containers
def start_secmon_containers(all_enrichment_modules):
    print(YELLOW,'\nStarting secmon modules:',NORMAL)
    os.system('docker compose start')

    for module in all_enrichment_modules:
        if os.system(f'docker container inspect secmon_{module} > /dev/null 2>&1') == 0:
            if os.system(f'docker start secmon_{module}') == 0:
                os.system(f'echo -e "\r\033[1A\033[0KStarting secmon_{module} ... {GREEN}done{NORMAL}"')
            else:
                os.system(f'echo -e "\r\033[1A\033[0KStarting secmon_{module} ... {RED}failed{NORMAL}"')

# Method for restarting running/stopped containers
def restart_secmon_containers(all_enrichment_modules, enabled_enrichment_modules):
    stop_secmon_containers(all_enrichment_modules)
    remove_secmon_containers(all_enrichment_modules)
    
    config_file = open("./config/aggregator_config.ini", "r")
    contents = config_file.readlines()

    print(YELLOW,'\nRestarting SecMon modules:',NORMAL)
    os.system('docker compose start')

    print(YELLOW,'\nCreating SecMon enrichment modules:',NORMAL)
    for module in enabled_enrichment_modules:
        if index_containing_substring(contents, module):
            run_enrichment_module(module)

    run_enrichment_module('correlator')
    config_file.close

# Method for stopping running containers
def stop_secmon_containers(all_enrichment_modules):
    print(YELLOW,'\nStopping secmon modules:',NORMAL)

    for module in all_enrichment_modules:
        if os.system(f'docker container inspect secmon_{module} > /dev/null 2>&1') == 0:
            if os.system(f'docker stop secmon_{module}') == 0:
                os.system(f'echo -e "\r\033[1A\033[0KStopping secmon_{module} ... {GREEN}done{NORMAL}"')
            else:
                os.system(f'echo -e "\r\033[1A\033[0KStopping secmon_{module} ... {RED}failed{NORMAL}"')
    os.system('docker compose stop')

# Method for removing stopped containers
def remove_secmon_containers(all_enrichment_modules):
    print(YELLOW,'\nRemoving secmon modules:',NORMAL)
    for module in all_enrichment_modules:
        if os.system(f'docker container inspect secmon_{module} > /dev/null 2>&1') == 0:
            if os.system(f'docker rm secmon_{module}') == 0:
                os.system(f'echo -e "\r\033[1A\033[0KRemoving secmon_{module} ... {GREEN}done{NORMAL}"')
            else:
                os.system(f'echo -e "\r\033[1A\033[0KRemoving secmon_{module} ... {RED}failed{NORMAL}"')

# Method taken from https://stackoverflow.com/questions/2170900/get-first-list-index-containing-sub-string
def index_containing_substring(the_list, substring):
    for i, s in enumerate(the_list):
        if substring.lower() in s.lower():
              return i
    return -1
  
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

# Create temp config for deployment
def create_temp_config():
    # Read configuration file
    config = configparser.ConfigParser()
    config.read('./config/secmon_config.ini')

    # Validate secmon_config.ini
    if(not validate(config)):
        sys.exit()

    # Write data to temp config for system services
    port = 9000
    aggregator_conf_file = open("./config/aggregator_config.ini", "w+")

    aggregator_conf_file.write("Log_input: %s\nName: %s\n" % (config.get('DEVICE', 'log_input'), config.get('DEVICE', 'name')))
    aggregator_conf_file.write("Nor_input_NP: %s\nNor_output_NP: %s\n" % (config.get('NORMALIZATION', 'input_NP'), config.get('NORMALIZATION', 'output_NP')))
    aggregator_conf_file.write("Cor_input_NP: %s\nCor_output_NP: %s\n" % (config.get('CORRELATION', 'input_NP'), config.get('CORRELATION', 'output_NP')))

    # Write 0MQ port for aggregator
    aggregator_conf_file.write("Aggregator: %d\n" % port)

    # Write 0MQ port for normalizer
    aggregator_conf_file.write("Normalizer: %d\n" % port)

    # Write 0MQ port for geoip
    if config.get('ENRICHMENT', 'geoip').lower() == "true":
        aggregator_conf_file.write("Geoip: %d\n" % port)
        enabled_enrichment_modules.append('geoip')

    # Write 0MQ port for network_model
    if config.get('ENRICHMENT', 'network_model').lower() == "true":
        aggregator_conf_file.write("Network_model: %d\n" % port)
        enabled_enrichment_modules.append('network_model')

    # if config.get('ENRICHMENT', 'rep_ip').lower() == "true":
    #     #write 0MQ port for rep_ip
    #     aggregator_conf_file.write("Rep_ip: %d\n" % port)
    #     enabled_enrichment_modules.append('rep_ip')

    aggregator_conf_file.close()

# Validate config
def validate(config):
    errorMsg = "Validation unsuccessful, found these errors: "
    error = 0

    # log_input path validation
    if(not path_validation("/var/log/", config.get('DEVICE', 'log_input'))):
        errorMsg += '\n' + "Log input must contain /var/log/ path! Please change the path."
        error = 1

    # device name in log_input path validation
    if(not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('DEVICE', 'log_input'), 3)):
        errorMsg += '\n' + "Source directory of log input path must have a same name as device name! Please rename source directory on log input path."
        error = 1

    # normalization input named pipe path validation
    if(not path_validation("/var/log/", config.get('NORMALIZATION', 'input_NP'))):
        errorMsg += '\n' + "Path of normalization INPUT naped pipe must contain /var/log/ path! Please change the path."
        error = 1
    
    # device name in normalization input naped pipe validation
    if(not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('NORMALIZATION', 'input_NP'), 3)):
        errorMsg += '\n' + "Source directory of normalization INPUT named pipe must have a same name as device name! Please rename source directory on log input path."
        error = 1

    # normalization output named pipe path validation
    if(not path_validation("/var/log/", config.get('NORMALIZATION', 'output_NP'))):
        errorMsg += '\n' + "Path of normalization OUTPUT naped pipe must contain /var/log/ path! Please change the path."
        error = 1
    
    # device name in normalization output naped pipe validation
    if(not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('NORMALIZATION', 'output_NP'), 3)):
        errorMsg += '\n' + "Source directory of normalization OUTPUT named pipe must have a same name as device name! Please rename source directory on log input path."
        error = 1

    # correlation input named pipe path validation
    if(not path_validation("/var/www/html/", config.get('CORRELATION', 'input_NP'))):
        errorMsg += '\n' + "Path of correlation INPUT naped pipe must contain /var/www/html/ path! Please change the path."
        error = 1
    
    # device name in correlation input naped pipe validation
    if(not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('CORRELATION', 'input_NP'), 4)):
        errorMsg += '\n' + "Source directory of correlation INPUT named pipe must have a same name as device name! Please rename source directory on log input path."
        error = 1

    # correlation output named pipe path validation
    if(not path_validation("/var/www/html/", config.get('CORRELATION', 'output_NP'))):
        errorMsg += '\n' + "Path of correlation OUTPUT naped pipe must contain /var/www/html/ path! Please change the path."
        error = 1
    
    # device name in correlation output naped pipe validation
    if(not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('CORRELATION', 'output_NP'), 4)):
        errorMsg += '\n' + "Source directory of correlation OUTPUT named pipe must have a same name as device name! Please rename source directory on log input path."
        error = 1

    if(error == 1):
        print(errorMsg)
        return False
    else:
        return True

# Script main entry point

if len(sys.argv) < 2 or sys.argv[1] == "help":
    print_help()
    sys.exit()

all_enrichment_modules = ['geoip', 'network_model', 'correlator']
enabled_enrichment_modules = []

# Start stopped containers
if sys.argv[1] == "start":
    start_secmon_containers(all_enrichment_modules)
    sys.exit()

# Stop running containers
if sys.argv[1] == "stop":
    stop_secmon_containers(all_enrichment_modules)
    sys.exit()

# Stop and remove all secmon containers
if sys.argv[1] == "remove":
    stop_secmon_containers(all_enrichment_modules)
    remove_secmon_containers(all_enrichment_modules)
    os.system('docker compose down')
    sys.exit()

if sys.argv[1] == "deploy":
    create_temp_config()

    answer = input("Deploying SecMon will remove all existing SecMon containers and existing SecMon database. This process also includes setting up different \
                    config files and creating new SecMon containers.\nDo you want to still deploy SecMon? [y/N] ")
    if answer == "N":
        sys.exit()
    elif answer == "y":
        # Stop and remove enrichment modules
        stop_secmon_containers(all_enrichment_modules)
        remove_secmon_containers(all_enrichment_modules)
        # Stop and remove core modules
        os.system('docker compose down')

        # Auto execute 'secmon_deploy.sh'
        if os.system('./secmon_deploy.sh') != 0:
            print(RED,'\nError occured during script secmon_deploy.sh execution, SecMon deploying process was unsuccessful.',NORMAL)
            sys.exit()
        
        # Create and start core modules + selected enrichment modules
        config_file = open("./config/aggregator_config.ini", "r")
        contents = config_file.readlines()
        os.system('docker compose -p secmon up -d')
        for module in enabled_enrichment_modules:
            if index_containing_substring(contents, module):
                run_enrichment_module(module)
        run_enrichment_module('correlator')
        config_file.close

        # Check the status of the database and wait until it is ready to receive connections
        os.system('docker logs secmon_db 2>&1 | grep -q "listening on IPv4 address \\"0.0.0.0\\", port 5432" && echo "Database is ready to receive connections" || echo "Database is not ready to receive connections..."')
        time.sleep(1)
        while os.system('docker logs secmon_db 2>&1 | grep -q "listening on IPv4 address \\"0.0.0.0\\", port 5432"') != 0:
            print('Waiting for database to be ready to receive connections...')
            time.sleep(5)
        os.system('docker exec -it secmon_app ./yii migrate --interactive=0') # Run database migration
        os.system('docker exec -it secmon_app chgrp -R www-data .') # Ensure the web-app files are accesible to web server
        
        # Initialize admin user 
        os.system(f'echo -n "Initializing SecMon admin user ... {GREEN}"')
        os.system('curl 127.0.0.1:8080/secmon/web/user/init')
        
        print(NORMAL)
        restart_secmon_containers(all_enrichment_modules, enabled_enrichment_modules)
        sys.exit()
    else:
        sys.exit()

# Restart running containers
if sys.argv[1] == "restart":
    create_temp_config()

    restart_secmon_containers(all_enrichment_modules, enabled_enrichment_modules)
    sys.exit()

print_help()
