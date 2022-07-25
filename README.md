### Install guide
A prerequisite for installing SecMon is an updated machine with OS CentOS 7 (other Linux distributions are not tested), with Internet access and with the installed programs [Git](https://github.com/), [Docker Engine](https://docs.docker.com/engine/install/) and [Docker Compose](https://docs.docker.com/compose/install/). The functionality of the Docker Engine can be verified with the `sudo docker run hello-world` command. Docker Compose functionality can be specified with `sudo docker-compose --version'. If the commands do not run correctly, this problem must be resolved or the installation will not be successful.

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
