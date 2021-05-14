# Technická dokuemntácia

Technická dokumentácia sa skladá z 3 časatí:
* Konfiguračné a inicializačné súbory
* Inštalačné súbory
* Používateľská príručka

## Konfiguračné a inicializačné súbory
Súbory spomenuté v nasledujúcom zozname boli potrebné na vytvorenie SecMon Docker obrazu, ktorý sa následne nahral na svoj Docker Hub repozitár. Všetky súbory boli skopírované, modifikované či vytvorené podľa inštalačných manuálov k nástroju SecMon:
* GeoLite2-City.mmdb
* rsyslog.conf
* secmon.conf
* secmon
* secmon-correlator.service
* secmon-middleware.service
* secmon-normalizer.service
* secmon-correlator.sh
* secmon-middleware.sh
* secmon-normalizer.sh

## Inštalačné súbory SecMon-u
V tejto časti technickej dokumentácie sa nachádzajú mnou implementované súbory, pomocou ktorých sa vykonáva proces nasadzovania SecMon-u:
* Dockerfile
* docker-compose.yml
* start.sh


### Dockerfile
Používa na vytvorenie SecMon Docker obrazu.

```Dockerfile
#Set up base image
FROM centos

#Enable systemd
RUN (cd /lib/systemd/system/sysinit.target.wants/; for i in *;
do [ $i == systemd-tmpfiles-setup.service ] || rm -f $i; done); \
rm -f /lib/systemd/system/multi-user.target.wants/*;\
rm -f /etc/systemd/system/*.wants/*;\
rm -f /lib/systemd/system/local-fs.target.wants/*; \
rm -f /lib/systemd/system/sockets.target.wants/*udev*; \
rm -f /lib/systemd/system/sockets.target.wants/*initctl*; \
rm -f /lib/systemd/system/basic.target.wants/*;\
rm -f /lib/systemd/system/anaconda.target.wants/*;

#Docker image update
RUN yum update -y

#Usefull packages installation
RUN yum -y install vim git zip unzip curl rsyslog glibc-langpack-en

#Php & apache installation
RUN yum clean all \
 && yum -y update \
 && rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-lat
 est-8.noarch.rpm \
 && rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.r
 pm \
 && yum -y install httpd php php-mbstring php-pdo php-dom php-posix
 php-json php-pgsql sec

#Python installation
RUN yum install -y python3 python3-devel python3-setuptools
RUN easy_install-3.6 pip
RUN pip3.6 install numpy pandas sklearn psycopg2-binary minisom \
 && pip3.6 install -U configparser \
 && alias python="/usr/bin/python3.6"

#Composer installation
RUN curl -sS https://getcomposer.org/installer | php -- 
--install-dir=/usr/local/bin --filename=composer

#Apache configuration
ADD httpd.conf /etc/httpd/conf/httpd.conf
ADD secmon.conf /etc/httpd/conf.d/secmon.conf
RUN systemctl enable httpd

#Rsyslog configuration
RUN mkdir /var/log/secmon \
 && chmod 777 /var/log/secmon
ADD rsyslog.conf /etc/rsyslog.conf
RUN systemctl enable rsyslog

#Logrotate configuration
ADD secmon /etc/logrotate.d/secmon

#Adding Geolite database
RUN mkdir -p /usr/local/share/GeoIP
ADD GeoLite2-City.mmdb /usr/local/share/GeoIP/

#Adding SecMon services
ADD secmon-middleware.sh /usr/bin/secmon-middleware.sh
ADD secmon-normalizer.sh /usr/bin/secmon-normalizer.sh
ADD secmon-correlator.sh /usr/bin/secmon-correlator.sh

#SecMon services configuration
RUN chmod +x /usr/bin/secmon-middleware.sh \
&& chmod +x /usr/bin/secmon-normalizer.sh \
&& chmod +x /usr/bin/secmon-correlator.sh
ADD secmon-middleware.service /etc/systemd/system/secmon-middleware.
service
ADD secmon-normalizer.service /etc/systemd/system/secmon-normalizer.
service
ADD secmon-correlator.service /etc/systemd/system/secmon-correlator.
service

#Enable SecMon services to start automatically on system boot
RUN systemctl enable secmon-middleware.service \
&& systemctl enable secmon-normalizer.service \
&& systemctl enable secmon-correlator.service

#Set up working directory
WORKDIR /var/www/html/secmon/

#Set up startup command
CMD ["/usr/sbin/init"]
```


### docker-compose.yml
Je zodpovedný za vytvorenie a spustenie obrazov \textit{app} a \textit{db} do podoby bežiacich kontajnerov.

```docker-compose
version: "3"
services:

  app:
    image: miropanak/secmon:v1
    container_name: app
    restart: always
    cap_add:
    - SYS_ADMIN
    volumes:
    - .:/var/www/html/secmon
    - /sys/fs/cgroup/:/sys/fs/cgroup:ro 
    ports:
    - 514:514 
    - 8080:80
    links:
      - db
    environment:
      - POSTGRES_HOST=db
      - POSTGRES_PASSWORD=<password>
      - POSTGRES_USER=secmon
    depends_on:
    - db

  db:
    image: postgres
    container_name: db
    restart: always
    ports:
      - 5432:5432
    environment:
      - POSTGRES_PASSWORD=<password>
      - POSTGRES_USER=secmon
    volumes:
      - db_data:/var/lib/pgsql/data/
      
volumes:
  db_data:
```

### start.sh
Spúšťa a manažuje celý proces nasadzovania SecMon-u.

```sh
#!/bin/bash

#copy configuration and installation files
cp docker-compose/db.php config/
cp docker-compose/anomaly_config.ini config/
cp docker-compose/docker-compose.yml .

#Set up color
RED='\033[0;31m'
GREEN='\033[0;32m'
NORMAL='\033[0m'

#Password creating
echo Create password for database user \'secmon\'
while true; do
    read -s -p "Enter Password: " password1
    echo
	
    if [ "${password1,,}" = "password" ];
        then echo -e "${RED}Entered password is forbidden, try 
        again...${NORMAL}"; continue;
    fi
	
    if [ ${#password1} -lt 8 ];
        then echo -e "${RED}Entered password is shorten than 8
        characters, try again...${NORMAL}"; continue;
    fi

    read -s -p "Re-enter Password: " password2
    echo
    if [ "${password1}" != "$password2" ]
        then echo -e "${RED}Sorry, passwords do not match, try 
        again...${NORMAL}"; continue;
        else break;
    fi
done
echo -e "${GREEN}Password succesfully created${NORMAL}"

sed -i "s/<password>/$password1/g" config/db.php
sed -i "s/<password>/$password1/g" config/anomaly_config.ini
sed -i "s/<password>/$password1/g" docker-compose.yml

docker-compose down
docker-compose build --no-cache
docker-compose up -d
docker exec -it app composer update
docker exec -it app ./yii migrate --interactive=0
sudo chown -R $USER:apache .
docker-compose restart
echo -e "${GREEN}Installation successfully completed${NORMAL}"
```

## Používateľská príručka
Používateľský príručka pozostáva z dvoch častí Docker príkazov a inštalačného manuálu nástroja SecMon.

### Docker príkazy
Táto sekcia obsahuje [základné príkazy](https://design.jboss.org/redhatdeveloper/marketing/docker_cheatsheet/cheatsheet/images/docker_cheatsheet_r3v2.pdf) na obsluhu Docker obrazov a kontajnerov.

---

#### Príkazy na prácu s kontajnermi:

docker ps - zobrazenie zoznamu aktívnych kontajnerov

docker ps -a - zobrazenie zoznamu všetkých kontajnerov

docker inspect <containername|containerid> - zobrazenie informácii o kontajneri

docker rm <containername|containerid> - vymazanie neaktívneho kontajnera

docker rm -f <containername|containerid> - vynútené zastavenie kontajnera a potom jeho vymazanie

docker run <imagename|imageid> -d - vytvorenie kontajneru z obrazu na pozadí

docker start <containername|containerid> - zapnutie ukončeného kontajnera

docker stop <containername|containerid> - ukončenie bežiaceho  kontajnera

docker restart <containername|containerid> - reštartovanie bežiaceho kontajnera

docker exec -it <containername|containerid> <command> - spustenie príkazu vo vnútri kontajnera

#### Príklady:

docker exec -it app bash - spustie terminálu vo vnútri it{app kontajnera

docker exec -it app composer install

---

#### Príkazy na prácu s obrazmi:

docker images - zobrazí zoznam všetkých obrazov

docker inspect <imagename|imageid> - zobrazenie informácie o obraze

docker rmi <imagename|imageid> - vymazanie obrazu

docker tag <oldimagename>[:oldtag] <newimagename>[:newtag] - zmena názvu alebo tagu obrazu

docker build <pathtoDockerfile> - vytvorenie obrazu pomocou súboru Dockerfile

docker build -t <imagename>[:tag] <pathtoDockerfile> - vytvorenie obrazu s menom a tagom pomocou súboru Dockerfile

docker commit>

docker search <imagename> - vyhľadanie obrazu na Docker Hub-e

docker pull [repositoryname/]<imagename>[:tag] - stiahnutie obrazu z Docker Hub-u

docker push [repositoryname/]<imagename>[:tag] - nahratie obrazu na Docker Hub

#### Príklady:

docker search secmon

docker tag secmon:v1 miropanak/secmon:v1

docker build -t secmon:v1 /home/secmon/secmonimagefiles

docker push miropanak/secmon:v1

docker pull httpd

docker pull miropanak/secmon:v1

---

#### Príkazy docker-compose:

Pri príkazoch typu docker-compose, je potrebné nachádzať sa v priečinku so súborom it{docker-compose.yml, s ktorým chceme pracovať.

docker-compose ps - zobrazenie bežiacich kontajnerov

docker-compose images - zobrazenie vytvorených obrazov

docker-compose build - vytvorenie obrazov zo služieb definovaných v it{docker-compose.yml

docker-compose build ---no-cache - vytvorenie obrazov zo služieb definovaných v it{docker-compose.yml, bez použitia cache

docker-compose up -d - vytvorenie a spustenie kontajnerov na pozadí

docker-compose start - spustenie zastavených kontajnerov

docker-compose stop - zastavenie bežiacich kontajnerov bez ich vymazania

docker-compose pause - pozastavenie kontajnerov

docker-compose unpause - zrušenie pozastavenia kontajnerov

docker-compose restart - reštartovanie bežiacich kontajnerov

docker-compose down - zastavenie a odstránenie kontajnerov, ich sietí, obrazov a zväzkov

---

#### Všeobecné príkazy:

docker login - prihlásenie sa do Docker účtu

docker ---version - vypísanie verzie programu Docker Engine

docker-compose version - vypísanie verzie programu Docker Compose

---

### Inštalačný manuál
Prerekvizita na inštaláciu SecMon-u je aktualizovaný stroj s OS CentOS 8, s prístupom na internet a s nainštalovanými programami [Docker Engine](https://docs.docker.com/engine/install/) a [Docker Compose](https://docs.docker.com/compose/install/)

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
./start.sh

#Crete password for database user 'secmon' during installation

#Inicialization secmon admin user
#Type to your browser
<IP_addres_CentOS_machine>:8080/secmon/web/user/init

#Default login credentials user:secmon, password:password
#After first login change password!!!
<IP_addres_CentOS_machine>:8080/secmon/web
```
