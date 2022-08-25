FROM yiisoftware/yii2-php:7.4-apache

ARG DEBIAN_FRONTEND=noninteractive

# Update system
RUN apt-get update

# Install useful packages
RUN apt-get install -y git zip curl gcc build-essential net-tools apt-utils libpq-dev libdbus-1-dev libdbus-glib-1-dev nmap \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Install python
RUN apt-get install -y python3 python3-dev python3-pip
RUN pip3 install numpy pandas sklearn psycopg2-binary minisom dbus-python python-libnmap
RUN pip3 install -U configparser
RUN alias python="/usr/bin/python3.9"

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html/secmon

# Copy apache config
COPY deployment/config_files/000-default.conf /etc/apache2/sites-available/


