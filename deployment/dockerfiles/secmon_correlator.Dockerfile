FROM secmon_base

ARG DEBIAN_FRONTEND=noninteractive

# Update system
RUN apt-get update

# Install Simple Event Correlator
RUN apt-get install -y sec

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html/secmon

ENTRYPOINT ["sh", "-c", "sec \
                --conf=/var/www/html/secmon/rules/correlation/active/*.rule \
                --input=/var/www/html/secmon/__secOutput \
                --pid=/var/www/html/secmon/pids/correlator.pid \
                --bufsize=1 --detach && ./yii correlator"]

