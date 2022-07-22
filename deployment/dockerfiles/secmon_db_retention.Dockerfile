FROM python:3.9-alpine

# Install python
RUN pip3 install --upgrade pip
RUN pip3 install psycopg2-binary
RUN pip3 install -U configparser

ENTRYPOINT ["sh", "-c", "python3.9 /home/secmon/commands/db_retention.py"]