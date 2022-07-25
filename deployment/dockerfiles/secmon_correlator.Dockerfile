FROM secmon_base

ARG DEBIAN_FRONTEND=noninteractive

# Update system
RUN apt-get update

# Install useful packages
RUN apt-get install -y sec

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html/secmon

ENTRYPOINT ["sh", "-c", "sec --conf=/var/www/html/secmon/rules/active/correlation/*.rule --input=/var/www/html/secmon/__secOutput --bufsize=1 --detach && ./yii correlator"]

