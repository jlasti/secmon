### Install guide

Prerequisite for installing SecMon is OS CentOS7/CentOS Stream 8/Ubuntu 22.04 (tested Linux distribution) with Internet access and with the installed programs [Docker Engine](https://docs.docker.com/engine/install/) and [Docker Compose](https://docs.docker.com/compose/install/) v2.3.3. The functionality of the Docker Engine can be verified with the command `sudo docker run hello-world`. Docker Compose functionality can be verified with `sudo docker-compose --version'. If the commands do not run correctly, this problem must be resolved or the installation will not be successful.

```
##### System Update #####

# CentOS 7/CentOS Stream 8
sudo yum clean all
sudo yum -y update

# Ubuntu 22.04
sudo apt clean all
sudo apt -y update

##### Install git, firewall & rsyslog #####

# CentOS 7/CentOS Stream 8
sudo yum install -y git firewalld rsyslog

# Ubuntu 22.04
sudo apt install -y git ufw rsyslog

##### Install python packages #####

# CentOS 7
sudo yum install -y https://repo.ius.io/ius-release-el7.rpm
sudo yum install -y python36u python36u-libs python36u-devel python36u-pip
sudo pip3.6 install -U configparser

# CentOS Stream 8
sudo pip3.6 install -U configparser

# Ubuntu 22.04
sudo apt-get install -y make build-essential libssl-dev zlib1g-dev \
libbz2-dev libreadline-dev libsqlite3-dev wget curl llvm libncurses5-dev \
libncursesw5-dev xz-utils tk-dev libffi-dev liblzma-dev \
libgdbm-dev libnss3-dev libedit-dev libc6-dev
wget https://www.python.org/ftp/python/3.6.15/Python-3.6.15.tgz
sudo tar -xzf Python-3.6.15.tgz
cd Python-3.6.15
sudo ./configure --enable-optimizations  -with-lto  --with-pydebug
sudo make altinstall

##### Setting up firewall #####

# CentOS 7/CentOS Stream 8
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --permanent --add-port=443/tcp
sudo firewall-cmd --permanent --add-port=514/tcp
sudo firewall-cmd --reload

# Ubuntu 22.04
sudo ufw allow 8080/tcp
sudo ufw allow 443/tcp
sudo ufw allow 514/tcp

#Download SecMon repository
git clone https://github.com/Miropanak/dockerized_secmon.git secmon

#Start preconfig script
cd secmon
./secmon_preconfig.sh

#Start deploying process
python3 secmon_manage.py deploy

#Crete password for database user 'secmon' during installation

#Default login credentials user:secmon, password:password
#After first login change password!!!
<IP_addres_host_machine>:8080/secmon/web
```
