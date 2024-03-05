#!/usr/bin/env python3
# encoding: utf-8


#requirements:
# sudo yum install dbus-devel dbus*
#sudo pip3.6 install dbus-python
# yum install zeromq-devel
# cd ~
# git clone git://github.com/mkoppanen/php-zmq.git
# cd php-zmq
# phpize && ./configure
# make && make install
# cd ~/php-zmq
# mv modules/zmq.so /usr/lib64/php/modules
# vim /etc/php.d/20-zmq.ini (extension=zmq.so)           => insert this line without brackets

import configparser
import dbus
import psycopg2
import sys
import os
import fileinput


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
    if(not connect()):
        errorMsg += '\n' + "Unable to connect to the database! Please check database credentials."
        error = 1

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
    path_to_rules = "/var/www/html/secmon/rules/active/" + type
    for file in os.listdir(path_to_rules):
        if file.endswith(".rule"):
            file_to_open = path_to_rules + "/" + file
            for line in fileinput.input(file_to_open, inplace=1):
                if "write" in line and line[0] != "#":
                    index = line.find("$")
                    index2 = line.find("/")
                    line = line.replace(line, line[0:index2] + named_pipe + line[index - 1:])
                sys.stdout.write(line)

    path_to_rules = "/var/www/html/secmon/rules/available/" + type
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

def assign_input_named_pipes(script, named_pipe):
    for line in fileinput.input(script, inplace=1):
        if "--input" in line:
            index = line.find("--input=/")
            index2 = line.find("--bufsize")
            line = line.replace(line, line[0:index] + "--input=" + named_pipe + line[index2 - 1:])
        sys.stdout.write(line)


if len(sys.argv) < 2 or sys.argv[1] == "help":
    print("Available parrameters are:\n")
    print("\"start\" - to start all modules")
    print("\"restart\" - to restart all modules")
    print("\"stop\" - to stop all modules")
    print("\"help\" - to list all available parameters\n")
    sys.exit()

#read configuration file
config = configparser.ConfigParser()
config.read('/var/www/html/secmon/config/middleware_config.ini')

#Initialize dbus for system calls
sysbus = dbus.SystemBus()
systemd1 = sysbus.get_object('org.freedesktop.systemd1', '/org/freedesktop/systemd1')
manager = dbus.Interface(systemd1, 'org.freedesktop.systemd1.Manager')

if sys.argv[1] == "stop":
    manager.StopUnit('secmon-aggregator.service', 'fail')
    manager.StopUnit('worker-normalizer.service', 'fail')
    manager.StopUnit('secmon-geoip.service', 'fail')
    manager.StopUnit('network-model.service', 'fail')
    manager.StopUnit('worker-correlator.service', 'fail')
    manager.StopUnit('secmon-normalizer.service', 'fail')
    manager.StopUnit('secmon-correlator.service', 'fail')

#input data validation
if(not validate(config)):
    sys.exit()


# create directory for log aggregation(update rsyslog config file)
change_log_input_directory(config.get('DEVICE', 'log_input'))

#assignt full paths of named pipes to normalization and correlation rules
assign_output_named_pipes("normalization", config.get('NORMALIZATION', 'output_NP'))
assign_output_named_pipes("correlation", config.get('CORRELATION', 'output_NP'))

#assign input naped pipes for normalizator and correlator
assign_input_named_pipes("/usr/bin/secmon-normalizer.sh", config.get('NORMALIZATION', 'input_NP'))
assign_input_named_pipes("/usr/bin/secmon-correlator.sh", config.get('CORRELATION', 'input_NP'))


#write data to temp file for system services
port = 5557
aggregator_conf_file = open("/var/www/html/secmon/config/aggregator_config.ini", "w+")
aggregator_conf_file.write("Log_input: %s\nName: %s\n" % (config.get('DEVICE', 'log_input'), config.get('DEVICE', 'name')))

aggregator_conf_file.write("Nor_input_NP: %s\nNor_output_NP: %s\n" % (config.get('NORMALIZATION', 'input_NP'), config.get('NORMALIZATION', 'output_NP')))
aggregator_conf_file.write("Cor_input_NP: %s\nCor_output_NP: %s\n" % (config.get('CORRELATION', 'input_NP'), config.get('CORRELATION', 'output_NP')))

#write 0MQ port for aggregator
aggregator_conf_file.write("Aggregator: %d\n" % port)
port += 1
#write 0MQ port for worker-normalizer
aggregator_conf_file.write("Normalizer: %d\n" % port)
port += 1

if config.get('ENRICHMENT', 'geoip').lower() == "true":
    #write 0MQ port for geoip
    aggregator_conf_file.write("Geoip: %d\n" % port)
    port += 1

if config.get('ENRICHMENT', 'network_model').lower() == "true":
    #write 0MQ port for network-model
    aggregator_conf_file.write("Network_model: %d\n" % port)
    port += 1

# if config.get('ENRICHMENT', 'rep_ip').lower() == "true":
#     #write 0MQ port for rep_ip
#     aggregator_conf_file.write("Rep_ip: %d\n" % port)
#     port += 1

aggregator_conf_file.close()


if sys.argv[1] == "start":
    #start system services
    manager.StartUnit('secmon-aggregator.service', 'fail')
    manager.StartUnit('worker-normalizer.service', 'fail')

    if config.get('ENRICHMENT', 'geoip').lower() == "true":
        manager.StartUnit('secmon-geoip.service', 'fail')

    if config.get('ENRICHMENT', 'network_model').lower() == "true":
        manager.StartUnit('network-model.service', 'fail')
    
    manager.StartUnit('worker-correlator.service', 'fail')
    manager.StartUnit('secmon-normalizer.service', 'fail')
    manager.StartUnit('secmon-correlator.service', 'fail')

if sys.argv[1] == "restart":
    manager.RestartUnit('secmon-aggregator.service', 'fail')
    manager.RestartUnit('worker-normalizer.service', 'fail')
    if config.get('ENRICHMENT', 'geoip').lower() == "true":
        manager.RestartUnit('secmon-geoip.service', 'fail')

    if config.get('ENRICHMENT', 'network_model').lower() == "true":
        manager.RestartUnit('network-model.service', 'fail')

    manager.RestartUnit('worker-correlator.service', 'fail')
    manager.RestartUnit('secmon-normalizer.service', 'fail')
    manager.RestartUnit('secmon-correlator.service', 'fail')



