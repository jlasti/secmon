#!/usr/bin/env python3
# encoding: utf-8

import sys
import os
import time
import yaml

RED = '\033[0;31m'
GREEN = '\033[0;32m'
YELLOW = '\033[0;33m'
NORMAL = '\033[0m'
MAGENTA = '\033[0;35m'

SECMON_MAIN_CONF = './config/secmon_config.yaml'
SECMON_AGGREGATOR_CONF = './config/aggregator_config.ini'

def print_help():
    print("Available parameters are:\n")
    print("\"help\" - to list all available parameters")
    print("\"deploy\" - to deploy SecMon")
    print("\"start\" - to start stopped SecMon containers")
    print("\"stop\" - to stop running SecMon containers")
    print("\"restart\" - to restart all SecMon containers")
    print("\"remove\" - to remove all SecMon containers with database")
    print("\"config\" - to run initial SecMon configuration")
    print("\"update-rules\" - to manually update default rules\n")

#run specific enrichment module
def run_enrichment_module(module):
    command = f'docker run -d {" ".join(module["args"])} --restart unless-stopped --name secmon_{module["name"].lower()} --network secmon_app-network -v ${{PWD}}:/var/www/html/secmon secmon_{module["name"].lower()}'
    if os.system(command) == 0:
        os.system(f'echo -e "\r\033[1A\033[0KCreating secmon_{module["name"].lower()} ... {GREEN}done{NORMAL}"')
    else:
        os.system(f'echo -e "\r\033[1A\033[0KCreating secmon_{module["name"].lower()} ... {RED}failed{NORMAL}"')

def run_correlator_module():
    command = f'docker run -d --restart unless-stopped --name secmon_correlator --network secmon_app-network -v ${{PWD}}:/var/www/html/secmon secmon_correlator'
    if os.system(command) == 0:
        os.system(f'echo -e "\r\033[1A\033[0KCreating secmon_correlator ... {GREEN}done{NORMAL}"')
    else:
        os.system(f'echo -e "\r\033[1A\033[0KCreating secmon_correlator ... {RED}failed{NORMAL}"')

# Method for starting stopped containers
def start_secmon_containers(enabled_enrichment_modules):
    print(YELLOW,'\nStarting secmon modules:', NORMAL)
    os.system('docker compose -p secmon start')

    for module in enabled_enrichment_modules:
        if os.system(f'docker container inspect secmon_{module["name"].lower()} > /dev/null 2>&1') == 0:
            if os.system(f'docker start secmon_{module["name"].lower()}') == 0:
                os.system(f'echo -e "\r\033[1A\033[0KStarting secmon_{module["name"].lower()} ... {GREEN}done{NORMAL}"')
            else:
                os.system(f'echo -e "\r\033[1A\033[0KStarting secmon_{module["name"].lower()} ... {RED}failed{NORMAL}"')

#method for restarting running/stopped containers
def restart_secmon_containers(all_modules, enabled_enrichment_modules):
    stop_secmon_containers(all_modules)
    remove_secmon_containers(all_modules)

    os.system('docker compose -p secmon restart')

    print(YELLOW,'\nCreating SecMon enrichment modules:', NORMAL)
    for module in enabled_enrichment_modules:
        run_enrichment_module(module)

# Method for stopping running containers
def stop_secmon_containers(all_modules):
    print(YELLOW, '\nStopping secmon modules:', NORMAL)
    for module in all_modules:
        if os.system(f'docker container inspect secmon_{module["name"].lower()} > /dev/null 2>&1') == 0:
            if os.system(f'docker stop secmon_{module["name"].lower()}') == 0:
                os.system(f'echo -e "\r\033[1A\033[0KStopping secmon_{module["name"].lower()} ... {GREEN}done{NORMAL}"')
            else:
                os.system(f'echo -e "\r\033[1A\033[0KStopping secmon_{module["name"].lower()} ... {RED}failed{NORMAL}"')
    os.system('docker compose -p secmon stop')

# Method for removing stopped containers
def remove_secmon_containers(all_modules):
    print(YELLOW, '\nRemoving secmon modules:', NORMAL)
    for module in all_modules:
        if os.system(f'docker container inspect secmon_{module["name"].lower()} > /dev/null 2>&1') == 0:
            if os.system(f'docker rm secmon_{module["name"].lower()}') == 0:
                os.system(f'echo -e "\r\033[1A\033[0KRemoving secmon_{module["name"].lower()} ... {GREEN}done{NORMAL}"')
            else:
                os.system(f'echo -e "\r\033[1A\033[0KRemoving secmon_{module["name"].lower()} ... {RED}failed{NORMAL}"')

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
def create_temp_config(secmon_config):
    # Validate secmon_config.yaml
    if not validate(secmon_config):
        sys.exit()

    # Write data to temp config for system services
    port = 9000
    aggregator_conf_file = open(SECMON_AGGREGATOR_CONF, "w+")
    aggregator_conf_file.write("Log_input: %s\nName: %s\n" % (secmon_config['DEVICE']['log_input'], secmon_config['DEVICE']['name']))

    aggregator_conf_file.write("Nor_input_NP: %s\nNor_output_NP: %s\n" % (secmon_config['NORMALIZATION']['input_NP'], secmon_config['NORMALIZATION']['output_NP']))
    aggregator_conf_file.write("Cor_input_NP: %s\nCor_output_NP: %s\n" % (secmon_config['CORRELATION']['input_NP'], secmon_config['CORRELATION']['output_NP']))

    # Write 0MQ port for aggregator
    aggregator_conf_file.write("Aggregator: %d\n" % port)

    # Write 0MQ port for normalizer
    aggregator_conf_file.write("Normalizer: %d\n" % port)

    # Write 0MQ ports for every enrichment module in the config
    for module in secmon_config["ENRICHMENT"]:
        if module["enabled"] and module["name"] != "correlator":
            aggregator_conf_file.write(f'{module["name"]}: {port}\n')
        print(module["name"])

    aggregator_conf_file.close()


# Validate config
def validate(config):
    error_msg = "Validation unsuccessful, found these errors: "
    error = 0

    # log_input path validation
    if not path_validation("/var/log/", config['DEVICE']['log_input']):
        error_msg += '\n' + "Log input must contain /var/log/ path! Please change the path."
        error = 1

    # device name in log_input path validation
    if not log_input_device_name_validation(config['DEVICE']['name'], config['DEVICE']['log_input'], 3):
        error_msg += '\n' + ("Source directory of log input path must have a same name as device name! Please rename \
                             source directory on log input path.")
        error = 1

    # normalization input named pipe path validation
    if not path_validation("/var/log/", config['NORMALIZATION']['input_NP']):
        error_msg += '\n' + ("Path of normalization INPUT naped pipe must contain /var/log/ path! Please change the \
                             path.")
        error = 1

    # device name in normalization input named pipe validation
    if not log_input_device_name_validation(config['DEVICE']['name'], config['NORMALIZATION']['input_NP'], 3):
        error_msg += '\n' + ("Source directory of normalization INPUT named pipe must have a same name as device name! \
                             Please rename source directory on log input path.")
        error = 1

    # normalization output named pipe path validation
    if not path_validation("/var/log/", config['NORMALIZATION']['output_NP']):
        error_msg += '\n' + ("Path of normalization OUTPUT naped pipe must contain /var/log/ path! Please change the \
                             path.")
        error = 1

    # device name in normalization output named pipe validation
    if not log_input_device_name_validation(config['DEVICE']['name'], config['NORMALIZATION']['output_NP'], 3):
        error_msg += '\n' + ("Source directory of normalization OUTPUT named pipe must have a same name as device name! \
                             Please rename source directory on log input path.")
        error = 1

    # correlation input named pipe path validation
    if not path_validation("/var/www/html/", config['CORRELATION']['input_NP']):
        error_msg += '\n' + ("Path of correlation INPUT named pipe must contain /var/www/html/ path! Please change the \
                             path.")
        error = 1

    # device name in correlation input named pipe validation
    if not log_input_device_name_validation(config['DEVICE']['name'], config['CORRELATION']['input_NP'], 4):
        error_msg += '\n' + ("Source directory of correlation INPUT named pipe must have a same name as device name! \
                             Please rename source directory on log input path.")
        error = 1

    # correlation output named pipe path validation
    if not path_validation("/var/www/html/", config['CORRELATION']['output_NP']):
        error_msg += '\n' + ("Path of correlation OUTPUT named pipe must contain /var/www/html/ path! Please change \
                             the path.")
        error = 1

    # device name in correlation output named pipe validation
    if not log_input_device_name_validation(config['DEVICE']['name'], config['CORRELATION']['output_NP'], 4):
        error_msg += '\n' + ("Source directory of correlation OUTPUT named pipe must have a same name as device name! \
                             Please rename source directory on log input path.")
        error = 1

    if error == 1:
        print(error_msg)
        return False
    else:
        return True

ALL_MODULES = []
ENABLED_ENRICHMENT_MODULES = []

def get_config():
    global ENABLED_ENRICHMENT_MODULES
    global ALL_MODULES
    with open(SECMON_MAIN_CONF) as secmon_config_file:
        secmon_config = yaml.safe_load(secmon_config_file)

        for module in secmon_config["ENRICHMENT"]:
            if module["enabled"]:
                ENABLED_ENRICHMENT_MODULES.append(module)
            print(module["name"])
            ALL_MODULES.append(module)
        return secmon_config

if len(sys.argv) < 2 or sys.argv[1] == "help":
    print_help()
    sys.exit()

# Start stopped containers
if sys.argv[1] == "start":
    get_config()
    start_secmon_containers(ENABLED_ENRICHMENT_MODULES)
    sys.exit()

# Stop running containers
if sys.argv[1] == "stop":
    get_config()
    stop_secmon_containers(ALL_MODULES)
    sys.exit()

# Stop and remove all secmon containers
if sys.argv[1] == "remove":
    get_config()
    stop_secmon_containers(ALL_MODULES)
    remove_secmon_containers(ALL_MODULES)
    os.system('docker compose -p secmon down')
    sys.exit()

# Restart running containers
if sys.argv[1] == "restart":
    secmon_config = get_config()
    print(YELLOW, '\nRestarting SecMon modules:', NORMAL)
    create_temp_config(secmon_config)
    restart_secmon_containers(ALL_MODULES, ENABLED_ENRICHMENT_MODULES)
    sys.exit()

# Manually run secmon configuration
if sys.argv[1] == "config":
    if os.system('./secmon_preconfig.sh') != 0:
        print(RED, '\nError occurred during secmon_preconfig.sh execution, SecMon configuration process was unsuccessful.', NORMAL)
    sys.exit()

# Manually update rules for repository configured in secmon_config.ini
if sys.argv[1] == "update-rules":
    print(YELLOW, '\nStarting download of default SecMon rules:', NORMAL)
    
    if os.system(f'./commands/rules_downloader.py os') != 0:
        print(RED, 'Error occurred during rules download, retrieval of rules from repository was unsuccessful.', NORMAL)
        sys.exit()
    
    os.system('chmod -R 777 ./rules/')
    print(GREEN, 'Download successful', NORMAL)
    sys.exit()

if sys.argv[1] == "deploy":
    if not os.path.isfile('./config/.lock'):
        if os.system('sudo bash ./secmon_preconfig.sh') != 0: # set sudo
            print(RED, '\nError occurred during SecMon configuration, SecMon configuration process was unsuccessful.', NORMAL)
            sys.exit()
    else:
        print(YELLOW, "Initial configuration already executed. Skipping step.", NORMAL)

    secmon_config = get_config()
    create_temp_config(secmon_config)

    answer = input("Deploying SecMon will remove all existing SecMon containers and existing SecMon database.\n"
                   "This process also includes setting up different config files and creating new SecMon containers.\n"
                   "Do you want to still deploy SecMon? [y/N] ")
    if answer.lower() == "n":
        sys.exit()
    elif answer.lower() == "y":
        # Stop and remove enrichment modules
        stop_secmon_containers(ALL_MODULES)
        remove_secmon_containers(ALL_MODULES)
        os.system('docker compose -p secmon down')

        # Auto execute 'secmon_deploy.sh'
        if os.system('sudo bash ./secmon_deploy.sh') != 0: # set sudo
            print(RED, '\nError occurred during script secmon_deploy.sh execution, SecMon deploying process was unsuccessful.', NORMAL)
            sys.exit()

        os.system('docker compose -p secmon up -d')

        for module in ENABLED_ENRICHMENT_MODULES:
            run_enrichment_module(module)

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
        os.system(f'echo -n "Initializing SecMon admin user ... {GREEN}"')
        os.system('curl 127.0.0.1:8080/secmon/web/user/init')

        restart_secmon_containers(ALL_MODULES, ENABLED_ENRICHMENT_MODULES)
        print(MAGENTA, "\nDeployment successful. SecMon is now live.", NORMAL)
        sys.exit()
    else:
        sys.exit()

print_help()
