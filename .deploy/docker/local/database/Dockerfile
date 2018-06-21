FROM postgres:10.4

# fix timezone
ENV TIMEZONE Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TIMEZONE /etc/localtime && echo $TIMEZONE > /etc/timezone

COPY .deploy/docker/local/database/init.d/ /docker-entrypoint-initdb.d
RUN chmod -R 0755 /docker-entrypoint-initdb.d
