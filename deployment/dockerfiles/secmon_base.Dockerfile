FROM php:8.2-fpm

# Update system
RUN apt-get update

# Install useful packages (autoconf balik asi pre php)
RUN apt-get install -y git zip curl gcc build-essential apt-utils libpq-dev libdbus-1-dev libdbus-glib-1-dev libzmq3-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Install ZeroMQ
RUN git clone https://github.com/zeromq/php-zmq.git \
    && cd php-zmq \
    && phpize && ./configure \
    && make \
    && make install \
    && cd .. \
    && rm -fr php-zmq \
    && echo "extension=zmq.so" > /usr/local/etc/php/conf.d/docker-php-ext-zmq.ini
RUN apt-get remove -y git build-essential

# Cleanup
RUN apt-get -y autoremove --purge
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*