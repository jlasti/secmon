### Install guide

Prerequisite for installing SecMon is an updated machine with OS CentOS Stream 8 (other suitble Linux distributions are CentOS 7, Ubuntu 22.04), with Internet access and with the installed programs [Git](https://github.com/), [Docker Engine](https://docs.docker.com/engine/install/) and [Docker Compose](https://docs.docker.com/compose/install/) v2.3.3. The functionality of the Docker Engine can be verified with the `sudo docker run hello-world` command. Docker Compose functionality can be specified with `sudo docker-compose --version'. If the commands do not run correctly, this problem must be resolved or the installation will not be successful.

## CentOS Stream 8

```
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
<IP_addres_CentOS_machine>:8080/secmon/web
```

## CentOS 7

```
#Download SecMon repository
git clone https://github.com/Miropanak/dockerized_secmon.git secmon

#Uncomment commands for installation python packages in secmon_preconfig.sh script
#->sudo yum install -y https://repo.ius.io/ius-release-el7.rpm
#->sudo yum install -y python36u python36u-libs python36u-devel python36u-pip

#Start preconfig script
cd secmon
./secmon_preconfig.sh

#Start deploying process
python3 secmon_manage.py deploy

#Crete password for database user 'secmon' during installation

#Default login credentials user:secmon, password:password
#After first login change password!!!
<IP_addres_CentOS_machine>:8080/secmon/web
```

## Ubuntu

```
#Download SecMon repository
git clone https://github.com/Miropanak/dockerized_secmon.git secmon

#Change directory to secmon
cd secmon

#Manual execution of individual steps of secmon_preconfig.sh script with modifications depending on the Linux distribution

#Start deploying process
python3 secmon_manage.py deploy

#Crete password for database user 'secmon' during installation

#Default login credentials user:secmon, password:password
#After first login change password!!!
<IP_addres_CentOS_machine>:8080/secmon/web
```
