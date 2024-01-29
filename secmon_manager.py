#!/usr/bin/env python3
# encoding: utf-8

import configparser
import sys
import os
import time

RED = '\033[0;31m'
GREEN = '\033[0;32m'
YELLOW = '\033[0;33m'
NORMAL = '\033[0m'
MAGENTA = '\033[0;35m'

SECMON_MAIN_CONF = './config/secmon_config.ini'
SECMON_AGGREGATOR_CONF = './config/aggregator_config.ini'


def print_help():
    print("Available parameters are:\n")
    print("\"deploy\" - to deploy SecMon")
    print("\"start\" - to start stopped SecMon containers")
    print("\"restart\" - to restart stopped/running SecMon containers")
    print("\"stop\" - to stop running SecMon containers")
    print("\"remove\" - to remove all SecMon containers with database")
    print("\"help\" - to list all available parameters")
    print("\"config\" - to run initial SecMon configuration")
    print("\"get-rules\" - to manually update default rules\n")


# Return all enabled enrichment modules in "secmon_config.ini"
def get_enabled_enr_modules():
    config = configparser.ConfigParser()
    config.read(SECMON_MAIN_CONF)

    enabled_enrichment_modules = []
    for module in all_enrichment_modules:
        if config.get('ENRICHMENT', module) == 'true':
            enabled_enrichment_modules.append(module)

    return enabled_enrichment_modules


# Run specific enrichment module
def create_enrichment_modules():
    print(YELLOW, '\nCreating secmon enrichment modules:', NORMAL)

    enabled_enrichment_modules = get_enabled_enr_modules()
    for module in enabled_enrichment_modules:
        command = f'docker run -d --restart unless-stopped --name secmon_{module} --network secmon_app-network \
                    -v ${{PWD}}:/var/www/html/secmon secmon_{module}'
        if os.system(command) == 0:
            os.system(f'echo -e "\r\033[1A\033[0KCreating secmon_{module} ... {GREEN}done{NORMAL}"')
        else:
            os.system(f'echo -e "\r\033[1A\033[0KCreating secmon_{module} ... {RED}failed{NORMAL}"')


# Method for starting stopped containers
def start_secmon_containers():
    print(YELLOW, '\nStarting secmon modules:', NORMAL)

    enabled_enrichment_modules = get_enabled_enr_modules()
    for module in enabled_enrichment_modules:
        if os.system(f'docker container inspect secmon_{module} > /dev/null 2>&1') == 0:
            if os.system(f'docker start secmon_{module}') == 0:
                os.system(f'echo -e "\r\033[1A\033[0KStarting secmon_{module} ... {GREEN}done{NORMAL}"')
            else:
                os.system(f'echo -e "\r\033[1A\033[0KStarting secmon_{module} ... {RED}failed{NORMAL}"')
    os.system('docker compose start')


# Method for stopping running containers
def stop_secmon_containers():
    print(YELLOW, '\nStopping secmon modules:', NORMAL)

    for module in all_enrichment_modules:
        if os.system(f'docker container inspect secmon_{module} > /dev/null 2>&1') == 0:
            if os.system(f'docker stop secmon_{module}') == 0:
                os.system(f'echo -e "\r\033[1A\033[0KStopping secmon_{module} ... {GREEN}done{NORMAL}"')
            else:
                os.system(f'echo -e "\r\033[1A\033[0KStopping secmon_{module} ... {RED}failed{NORMAL}"')
    os.system('docker compose stop')


# Method for removing stopped containers
def remove_secmon_containers():
    print(YELLOW, '\nRemoving secmon modules:', NORMAL)

    for module in all_enrichment_modules:
        if os.system(f'docker container inspect secmon_{module} > /dev/null 2>&1') == 0:
            if os.system(f'docker rm secmon_{module}') == 0:
                os.system(f'echo -e "\r\033[1A\033[0KRemoving secmon_{module} ... {GREEN}done{NORMAL}"')
            else:
                os.system(f'echo -e "\r\033[1A\033[0KRemoving secmon_{module} ... {RED}failed{NORMAL}"')
    os.system('docker compose down')


def path_validation(path, input_data):
    return path in input_data


def log_input_device_name_validation(name, input_data, index):
    if not (name in input_data):
        return False

    tmp = input_data.split("/")
    if tmp[index] != name:
        return False
    else:
        return True


# Create temp config for deployment
def create_temp_config():
    # Read configuration file
    config = configparser.ConfigParser()
    config.read(SECMON_MAIN_CONF)

    # Validate secmon_config.ini
    if not validate(config):
        sys.exit()

    # Write data to temp config for system services
    port = 9000
    aggregator_conf_file = open(SECMON_AGGREGATOR_CONF, "w+")

    aggregator_conf_file.write(f"Log_input: {config.get('DEVICE', 'log_input')}\nName: {config.get('DEVICE', 'name')}\n")
    aggregator_conf_file.write(f"Nor_input_NP: {config.get('NORMALIZATION', 'input_NP')}\nNor_output_NP: {config.get('NORMALIZATION', 'output_NP')}\n")
    aggregator_conf_file.write(f"Cor_input_NP: {config.get('CORRELATION', 'input_NP')}\nCor_output_NP: {config.get('CORRELATION', 'output_NP')}\n")

    # Write 0MQ port for aggregator
    aggregator_conf_file.write("Aggregator: %d\n" % port)

    # Write 0MQ port for normalizer
    aggregator_conf_file.write("Normalizer: %d\n" % port)

    # Write 0MQ port for geoip and 
    if config.get('ENRICHMENT', 'geoip').lower() == "true":
        aggregator_conf_file.write("Geoip: %d\n" % port)

    # Write 0MQ port for network_model
    if config.get('ENRICHMENT', 'network_model').lower() == "true":
        aggregator_conf_file.write("Network_model: %d\n" % port)

    # !!! ADD HERE ANY NEW ENRICHMENT MODULE 0MQ port CONFIG !!!

    # if config.get('ENRICHMENT', 'rep_ip').lower() == "true":
    #     #write 0MQ port for rep_ip
    #     aggregator_conf_file.write("Rep_ip: %d\n" % port)

    aggregator_conf_file.close()


# Validate config
def validate(config):
    error_msg = "Validation unsuccessful, found these errors: "
    error = 0

    # log_input path validation
    if not path_validation("/var/log/", config.get('DEVICE', 'log_input')):
        error_msg += '\n' + "Log input must contain /var/log/ path! Please change the path."
        error = 1

    # device name in log_input path validation
    if not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('DEVICE', 'log_input'), 3):
        error_msg += '\n' + ("Source directory of log input path must have a same name as device name! Please rename \
                             source directory on log input path.")
        error = 1

    # normalization input named pipe path validation
    if not path_validation("/var/log/", config.get('NORMALIZATION', 'input_NP')):
        error_msg += '\n' + ("Path of normalization INPUT naped pipe must contain /var/log/ path! Please change the \
                             path.")
        error = 1

    # device name in normalization input named pipe validation
    if not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('NORMALIZATION', 'input_NP'), 3):
        error_msg += '\n' + ("Source directory of normalization INPUT named pipe must have a same name as device name! \
                             Please rename source directory on log input path.")
        error = 1

    # normalization output named pipe path validation
    if not path_validation("/var/log/", config.get('NORMALIZATION', 'output_NP')):
        error_msg += '\n' + ("Path of normalization OUTPUT naped pipe must contain /var/log/ path! Please change the \
                             path.")
        error = 1

    # device name in normalization output named pipe validation
    if not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('NORMALIZATION', 'output_NP'), 3):
        error_msg += '\n' + ("Source directory of normalization OUTPUT named pipe must have a same name as device name! \
                             Please rename source directory on log input path.")
        error = 1

    # correlation input named pipe path validation
    if not path_validation("/var/www/html/", config.get('CORRELATION', 'input_NP')):
        error_msg += '\n' + ("Path of correlation INPUT named pipe must contain /var/www/html/ path! Please change the \
                             path.")
        error = 1

    # device name in correlation input named pipe validation
    if not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('CORRELATION', 'input_NP'), 4):
        error_msg += '\n' + ("Source directory of correlation INPUT named pipe must have a same name as device name! \
                             Please rename source directory on log input path.")
        error = 1

    # correlation output named pipe path validation
    if not path_validation("/var/www/html/", config.get('CORRELATION', 'output_NP')):
        error_msg += '\n' + ("Path of correlation OUTPUT named pipe must contain /var/www/html/ path! Please change \
                             the path.")
        error = 1

    # device name in correlation output named pipe validation
    if not log_input_device_name_validation(config.get('DEVICE', 'name'), config.get('CORRELATION', 'output_NP'), 4):
        error_msg += '\n' + ("Source directory of correlation OUTPUT named pipe must have a same name as device name! \
                             Please rename source directory on log input path.")
        error = 1

    if error == 1:
        print(error_msg)
        return False
    else:
        return True


# Script main entry point

if len(sys.argv) < 2 or sys.argv[1] == "help":
    print_help()
    sys.exit()

all_enrichment_modules = ['geoip', 'network_model', 'correlator']

# Start stopped containers
if sys.argv[1] == "start":
    start_secmon_containers()
    sys.exit()

# Stop running containers
if sys.argv[1] == "stop":
    stop_secmon_containers()
    sys.exit()

# Stop and remove all secmon containers
if sys.argv[1] == "remove":
    stop_secmon_containers()
    remove_secmon_containers()
    os.system('docker compose down')
    sys.exit()

# Restart running containers
if sys.argv[1] == "restart":
    print(YELLOW, '\nRestarting SecMon modules:', NORMAL)
    create_temp_config()
    stop_secmon_containers()
    start_secmon_containers()
    sys.exit()

# Manually run secmon configuration
if sys.argv[1] == "config":
    if os.system('./secmon_preconfig.sh') != 0:
        print(RED, '\nError occurred during secmon_preconfig.sh execution, SecMon configuration process was unsuccessful.', NORMAL)
    sys.exit()

# Manually update rules for repository configured in secmon_config.ini
if sys.argv[1] == "get-rules":
    print(YELLOW, '\nStarting download of default SecMon rules:', NORMAL)
    
    if os.system(f'./commands/rules_downloader.py os') != 0:
        print(RED, 'Error occurred during rules download, retrieval of rules from repository was unsuccessful.', NORMAL)
        sys.exit()
    
    os.system('chmod -R 777 ./rules/*')
    print(GREEN, 'Download successful', NORMAL)
    sys.exit()

if sys.argv[1] == "deploy":
    if not os.path.isfile('./config/.lock'):
        os.system('sudo bash ./secmon_preconfig.sh')
    else:
        print(YELLOW, "Initial configuration already executed. Skipping step.", NORMAL)

    create_temp_config()

    answer = input("Deploying SecMon will remove all existing SecMon containers and existing SecMon database.\n"
                   "This process also includes setting up different config files and creating new SecMon containers.\n"
                   "Do you want to still deploy SecMon? [y/N] ")
    if answer.lower() == "n":
        sys.exit()
    elif answer.lower() == "y":
        # Stop and remove enrichment modules
        stop_secmon_containers()
        remove_secmon_containers()

        # Auto execute 'secmon_deploy.sh'
        if os.system('sudo bash ./secmon_deploy.sh') != 0:
            print(RED, '\nError occurred during script secmon_deploy.sh execution, SecMon deploying process was unsuccessful.', NORMAL)
            sys.exit()

        os.system('docker compose -p secmon up -d')
        create_enrichment_modules()

        # Check the status of the database and wait until it is ready to receive connections
        os.system( 'docker logs secmon_db 2>&1 | grep -q "listening on IPv4 address \\"0.0.0.0\\", port 5432" && echo \
            "Database is ready to receive connections" || echo "Database is not ready to receive connections..."')
        time.sleep(1)
        while os.system('docker logs secmon_db 2>&1 | grep -q "listening on IPv4 address \\"0.0.0.0\\", port 5432"') != 0:
            print('Waiting for database to be ready to receive connections...')
            time.sleep(5)
        os.system('docker exec -it secmon_app ./yii migrate --interactive=0') # Run database migration
        os.system('docker exec -it secmon_app chgrp -R www-data .') # Ensure the web-app files are accessible to web server

        # Initialize admin user 
        # TODO: what this ? why needed ? what do ?
        os.system(f'echo -n "Initializing SecMon admin user ... {GREEN}"')
        os.system('curl 127.0.0.1:8080/secmon/web/user/init')

        print(MAGENTA, "\nDeployment successful. SecMon is now live.", NORMAL)
        sys.exit()
    else:
        sys.exit()

print_help()
