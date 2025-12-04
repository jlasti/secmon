FROM secmon_base

# Set working directory
WORKDIR /var/www/html/secmon

ENTRYPOINT ["sh", "-c", "./yii cti"]
