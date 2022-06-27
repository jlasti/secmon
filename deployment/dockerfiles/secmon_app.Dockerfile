FROM yiisoftware/yii2-php:7.4-apache

# Update system
RUN apt-get update

# Install useful packages (autoconf balik asi pre php)
RUN apt-get install -y git zip curl gcc build-essential dialog apt-utils iputils-ping libpq-dev libdbus-1-dev libdbus-glib-1-dev libzmq3-dev nmap \
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

COPY ./docker-compose/000-default.conf /etc/apache2/sites-available/