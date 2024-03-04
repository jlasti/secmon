FROM yiisoftware/yii2-php:8.2-apache

ARG DEBIAN_FRONTEND=noninteractive

# Update system
RUN apt-get update

# Install useful packages
RUN apt-get install -y git zip curl gcc build-essential net-tools apt-utils libpq-dev libdbus-1-dev libdbus-glib-1-dev nmap \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Install python
RUN apt-get install -y python3 python3-dev python3-pip
RUN mv /usr/lib/python3.11/EXTERNALLY-MANAGED /usr/lib/python3.11/EXTERNALLY-MANAGED.old
RUN pip3 install numpy pandas psycopg2-binary minisom python-libnmap

RUN pip3 install -U configparser
RUN alias python="/usr/bin/python3"

# Cleanup
RUN apt-get -y autoremove --purge
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN pip3 cache purge

WORKDIR /var/www/html/secmon

# Copy apache config files
COPY deployment/config_files/000-default.conf /etc/apache2/sites-available/
COPY deployment/config_files/default-ssl.conf /etc/apache2/sites-available/

# Copy SSL certificates
COPY deployment/certificates/* /etc/ssl/

# Enable SSL
RUN a2ensite default-ssl
RUN a2enmod ssl

# Restart Apache
RUN service apache2 restart