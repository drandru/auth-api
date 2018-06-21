#!/bin/sh

# Stop on any error
set -e

echo "Init app"
if [ ! -f /tmp/initialized ]
then
 # composer run-script symfony-scripts

  if [ ! "${SYMFONY_ENV}" == "dev" ]; then
    bin/console cache:warmup
  fi

  touch /tmp/initialized
fi

echo "Start app"
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/service.conf
