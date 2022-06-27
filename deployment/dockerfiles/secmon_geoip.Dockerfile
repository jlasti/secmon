FROM secmon_base

RUN mkdir -p /usr/local/share/GeoIP
COPY ./GeoLite2-City.mmdb /usr/local/share/GeoIP/GeoLite2-City.mmdb

# Set working directory
WORKDIR /var/www/html/secmon

ENTRYPOINT ["sh", "-c", "./yii geoip"]
