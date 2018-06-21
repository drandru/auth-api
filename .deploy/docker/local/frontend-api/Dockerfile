FROM nginx:latest

# fix timezone
ENV TIMEZONE Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TIMEZONE /etc/localtime && echo $TIMEZONE > /etc/timezone

COPY .deploy/docker/local/frontend-api/nginx.conf /etc/nginx/conf.d/default.conf
