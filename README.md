### Inštalačný manuál
Prerekvizita na inštaláciu SecMon-u je aktualizovaný stroj s OS CentOS 8, s prístupom na internet a s nainštalovanými programami [Docker Engine](https://docs.docker.com/engine/install/) a [Docker Compose](https://docs.docker.com/compose/install/). Funkčnosť programu Docker Engine sa dá overiť príkazom `sudo docker run hello-world`. Funkčnosť programu Docker Compose sa dá oeriť príkazom `sudo docker-compose --version`. V prípade, že príkazy správne nezbehnú, je potrebné tento problém odstrániť, inak inštalácia neprebehne úspešne.

```
#Update CentOS-u
sudo yum -y update

#Update repository & install useful packages
sudo rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-late
st-8.noarch.rpm
sudo yum -y install vim net-tools bind-utils screen wget curl nc 
firewalld unzip git

#Set up firewall (web & rsyslog)
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --permanent --add-port=514/tcp
sudo firewall-cmd --reload

#Make clone from secmon repository to newly created secmon directory
mkdir secmon
git clone -b master-tmp-PavlakFinal https://github.com/jlasti/secmon
_backend.git secmon 
cd secmon

#Start SecMon deployment process
sudo ./start.sh

#Crete password for database user 'secmon' during installation

#Inicialization secmon admin user
#Type to your browser
<IP_addres_CentOS_machine>:8080/secmon/web/user/init

#Default login credentials user:secmon, password:password
#After first login change password!!!
<IP_addres_CentOS_machine>:8080/secmon/web
```
