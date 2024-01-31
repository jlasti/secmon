

# SecMon User Guide

## How to Install

Prerequisite for installing SecMon system is OS CentOS7/CentOS Stream 8/Rocky Linux 9/Ubuntu 22.04 (tested Linux distribution) with user ***secmon*** (under which we will deploy SecMon system), internet access and installed programs [Docker Engine](https://docs.docker.com/engine/install/) and [Docker Compose](https://docs.docker.com/compose/install/) v2.3.3. 

Tutorial: [Docker installation on Rocky Linux 9](./help/docker_installation_RL9.md)

The functionality of the Docker Engine can be verified with the command `docker run hello-world`. Docker Compose functionality can be verified with `docker compose version`. If the commands do not run correctly, this problem must be resolved or the installation will not be successful.

---

### CentOS 7

```bash
# System Update
sudo yum clean all
sudo yum -y update

# Install git, firewall & rsyslog
sudo yum install -y git firewalld rsyslog

# Install python packages
sudo yum install -y https://repo.ius.io/ius-release-el7.rpm
sudo yum install -y python36u python36u-libs python36u-devel python36u-pip
sudo pip3.6 install -U configparser

# Setting up firewall
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --permanent --add-port=8443/tcp
sudo firewall-cmd --permanent --add-port=514/tcp
sudo firewall-cmd --reload

# Download SecMon repository
git clone https://github.com/jlasti/secmon.git secmon

# Start deployment process with configuration
cd secmon
python3 secmon_manager.py deploy

# Create password for database user 'secmon' during installation

# Default login credentials user:secmon, password:password
# !!! Change password after first login !!!
https://<host_machine_IP_address>:8443/secmon/web
```

After successful installation configure logs forwarding on clients using [rsyslog service](./README.md#how-to-configure-clients-for-logs-forwarding).

---

### CentOS 8

```bash
# System Update
sudo yum clean all
sudo yum -y update

# Install git, firewall & rsyslog
sudo yum install -y git firewalld rsyslog

# Install python packages
sudo pip3.6 install -U configparser

# Setting up firewall
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --permanent --add-port=8443/tcp
sudo firewall-cmd --permanent --add-port=514/tcp
sudo firewall-cmd --reload

#Download SecMon repository
git clone https://github.com/jlasti/secmon.git secmon

# Start deployment process with configuration
cd secmon
python3 secmon_manager.py deploy

# Create password for database user 'secmon' during installation

# Default login credentials user:secmon, password:password
# !!! Change password after first login !!!
https://<host_machine_IP_address>:8443/secmon/web
```

After successful installation configure logs forwarding on clients using [rsyslog service](./README.md#how-to-configure-clients-for-logs-forwarding).

---

### Rocky 9

```bash
# System Update
sudo yum clean all
sudo yum -y update

# Install git, firewall & rsyslog
sudo yum install -y git firewalld rsyslog

# Install python packages
sudo yum install python3-pip
sudo pip install -U configparser

# Setting up firewall
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --permanent --add-port=8443/tcp
sudo firewall-cmd --permanent --add-port=514/tcp
sudo firewall-cmd --reload

# Download SecMon repository
git clone https://github.com/jlasti/secmon.git secmon

# Start deployment process with configuration
cd secmon
python3 secmon_manager.py deploy

# Create password for database user 'secmon' during installation

# Default login credentials user:secmon, password:password
# !!! Change password after first login !!!
https://<host_machine_IP_address>:8443/secmon/web
```

After successful installation configure logs forwarding on clients using [rsyslog service](./README.md#how-to-configure-clients-for-logs-forwarding).

---

### Ubuntu 22.04

```bash
# System Update
sudo apt clean all
sudo apt -y update

# Install git, firewall & rsyslog
sudo apt install -y git ufw rsyslog

# Install python packages
sudo apt-get install -y make build-essential libssl-dev zlib1g-dev \
libbz2-dev libreadline-dev libsqlite3-dev wget curl llvm libncurses5-dev \
libncursesw5-dev xz-utils tk-dev libffi-dev liblzma-dev \
libgdbm-dev libnss3-dev libedit-dev libc6-dev
wget https://www.python.org/ftp/python/3.6.15/Python-3.6.15.tgz
sudo tar -xzf Python-3.6.15.tgz
cd Python-3.6.15
sudo ./configure --enable-optimizations  -with-lto  --with-pydebug
sudo make altinstall

# Setting up firewall
sudo ufw allow 8080/tcp
sudo ufw allow 8443/tcp
sudo ufw allow 514/tcp

# Download SecMon repository
git clone https://github.com/jlasti/secmon.git secmon

# Start deployment process with configuration
cd secmon
python3 secmon_manager.py deploy

# Create password for database user 'secmon' during installation

# Default login credentials user:secmon, password:password
# !!! Change password after first login !!!
https://<host_machine_IP_address>:8443/secmon/web
```

After successful installation configure logs forwarding on clients using [rsyslog service](./README.md#how-to-configure-clients-for-logs-forwarding).

---

## How to configure clients for logs forwarding
To redirect logs from client machine to the SecMon add the following line at the end of the `/etc/rsyslog.conf` file, where `<secmon_machine_IP_address>` is the IP address of the remote server (SecMon), you will be writing your logs to:
```bash
*.* @@<secmon_machine_IP_address>:514
```
Save your changes and restart the `rsyslog` service on the client with the command:
```bash
sudo systemctl restart rsyslog
```

## How to Use
### SecMon Manager
SecMon manager (*secmon_manager.py*) is a python script  located in root directory of SecMon repository. It is used for managing SecMon services as docker containers.
```bash
# Show list of all available parameters
python3 secmon_manager.py help

# Stop running SecMon system
python3 secmon_manager.py stop

# Start stopped SecMon system
python3 secmon_manager.py start

# Restart running/stopped SecMon system
python3 secmon_manager.py restart

# Remove SecMon containers
python3 secmon_manager.py remove

# Manually run configuration script
python3 secmon_manager.py config

# Deploy SecMon system on a host machine
python3 secmon_manager.py deploy

# Update standard rules set
python3 secmon_manager.py get-rules
```
## Configuration
#### Turn on/off enrichment module
Set value `true` /`false` in the file `./config/secmon_config.ini` for a particular enrichment module which you want to turn on/off:
```ini
[ENRICHMENT]
correlation = true
geoip = true
network_model = true
```
After any changes in configuration or rule states, restart the SecMon system with the command:
```bash
python3 secmon_manager.py restart
```

## Development
SecMon UI is written in php Yii2 framework. More information about this framework can be found [here](https://yii2-framework.readthedocs.io/en/latest/) or [here](https://www.yiiframework.com/doc/guide/2.0/en) ;)

### Directory structure
SecMon root directory contains a few important directories:
- **assets** - used for Yii assets/dependencies registration
- **commands** - main scripts for SecMon services which could be run in Docker containers
- **config** - storage of SecMon config files after deployment
- **controllers** - standard MVC directory
- **deployment** - necessary files for SecMon deployment (system config files, docker-compose.yml and GeoIP database)
    - config_files - configuration files for system services
    - config_templates - configuration files which are modified during deployment
    - dockerfiles - custom Dockerfiles for creating docker images of SecMon services
- **help** - tutorials and how to's
- **migrations** - migrations for database 
- **models** - standard MVC directory
- **pids** - SEC PIDs in normalizator and correlator
- **reporting** - SecMon security testing script
- ~~rules - contains normalization and correlation rules~~ (moved [here](https://github.com/mlomencik/secmon-rules))
- **services** - standard MVC+S directory
- **test** - unit test directory
- **views** - standard MVC directory
- **web** - root directory of the web application
- **widgets** - common UI components or functionalities

### Docker commands
Run command inside container:
- `docker exec <container_name> <command>`
- `docker exec -it secmon_app ls`

Run `bash` inside container:
- `docker exec -it <container_name> bash`
- `docker exec -it secmon_app bash`

Run `composer update`/`install`:
- `docker exec secmon_app composer update`
- `docker exec secmon_app composer install`

Database migrations: [official guide](https://www.yiiframework.com/doc/guide/2.0/en/db-migrations)
- `docker exec -it secmon_app <command>`

Run migration:
- `docker exec -it secmon_app ./yii migrate`

Refreshing migration:
- `docker exec -it secmon_app ./yii migrate/fresh`

Create new migration:
- `docker exec -it secmon_app ./yii migrate/create <name>`
- `docker exec -it secmon_app ./yii migrate/create security_events_table`

Run `psql`:
- `docker exec -it secmon_db psql -U secmon`

### System Update

#### Local changes:
- Changes in database: `docker exec -it secmon_app ./yii migrate`
- Changes in `composer.json` file: `docker exec -it secmon_app composer update`
- Changes in `./commands` directory/New enrichment module/New normalization or correlation rules: `python3 secmon_manager.py deploy`

#### Remote changes:
- `git pull`
- Changes in database: `docker exec -it secmon_app ./yii migrate`
- Changes in `composer.json` file: `docker exec -it secmon_app composer update`
- Changes in `./commands` directory/New enrichment module/New normalization or correlation rules: `python3 secmon_manager.py deploy`

### Debug
SecMon logs are located in file `/var/log/docker/secmon.log`

---