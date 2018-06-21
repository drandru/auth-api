FROM centos:latest

ARG php_display_errors=On
ARG php_opcache_enabled=Off
ARG xdebug_enabled=1
ARG SYMFONY_ENV=dev
ARG SYMFONY_DEBUG=1

# fix timezone
ENV TIMEZONE Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TIMEZONE /etc/localtime && echo $TIMEZONE > /etc/timezone

WORKDIR /app/backend-api

RUN yum install -y https://dl.fedoraproject.org/pub/epel/7/x86_64/Packages/e/epel-release-7-11.noarch.rpm http://rpms.famillecollet.com/enterprise/7/remi/x86_64/remi-release-7.4-2.el7.remi.noarch.rpm
RUN rpm --import /etc/pki/rpm-gpg/RPM-GPG-KEY-EPEL-7 /etc/pki/rpm-gpg/RPM-GPG-KEY-remi /etc/pki/rpm-gpg/RPM-GPG-KEY-CentOS-7
RUN yum-config-manager --enable remi,remi-php71
RUN yum update -y && yum clean all
RUN yum install -y python-setuptools git wget php php-fpm php-xml php-intl php-cli php-pdo php-pgsql php-process php-mbstring php-bcmath php-pecl-zip php-pecl-zendopcache && yum clean all
RUN echo "date.timezone=${TIMEZONE}" > /etc/php.d/00-docker-php-date-timezone.ini
RUN easy_install supervisor

COPY .deploy/docker/local/api-supervisor.service.ini /etc/supervisor/conf.d/service.conf

# Configure and secure PHP
RUN sed -i "s/soap.wsdl_cache_dir\s*=/;soap.wsdl_cache_dir =/" /etc/php.ini && \
    sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php-fpm.conf && \
    sed -i "s/error_log = .*/error_log = \/proc\/self\/fd\/2/" /etc/php-fpm.conf && \
    sed -i "s/memory_limit = 128/memory_limit = 1280/" /etc/php.ini && \
    sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php.ini && \
    sed -i "s/short_open_tag = Off/short_open_tag = On/" /etc/php.ini && \
    sed -i "s/display_errors = Off/display_errors = $php_display_errors/" /etc/php.ini && \
    sed -i '/^listen = /clisten = 0.0.0.0:9000' /etc/php-fpm.d/www.conf && \
    sed -i '/^listen.allowed_clients/c;listen.allowed_clients =' /etc/php-fpm.d/www.conf && \
    sed -i '/^;catch_workers_output/ccatch_workers_output = yes' /etc/php-fpm.d/www.conf && \
    sed -i "s/php_admin_flag\[log_errors\] = .*/;php_admin_flag[log_errors] =/" /etc/php-fpm.d/www.conf && \
    sed -i "s/php_admin_value\[error_log\] =.*/;php_admin_value[error_log] =/" /etc/php-fpm.d/www.conf && \
    sed -i "s/php_admin_value\[error_log\] =.*/;php_admin_value[error_log] =/" /etc/php-fpm.d/www.conf && \
    sed -i '/^;clear_env/cclear_env = no' /etc/php-fpm.d/www.conf && \
    sed -i "s/php_value\[soap.wsdl_cache_dir\]\s*=/;php_value[soap.wsdl_cache_dir] =/" /etc/php-fpm.d/www.conf && \
    sed -i "s/opcache.enable=1/opcache.enable=$php_opcache_enabled/" /etc/php.d/10-opcache.ini && \
    echo "access.log = /proc/self/fd/2" >> /etc/php-fpm.d/www.conf

RUN if [ "$xdebug_enabled" = 1 ] ; then \
    yum install -y php-pecl-xdebug && \
    echo "xdebug.var_display_max_depth = -1" >> /etc/php.ini && \
    echo "xdebug.var_display_max_children = -1" >> /etc/php.ini && \
    echo "xdebug.var_display_max_data = -1" >> /etc/php.ini && \
    echo "xdebug.remote_enable=on" >> /etc/php.ini && \
    echo "xdebug.remote_connect_back=1" >>  /etc/php.ini && \
    echo "xdebug.remote_autostart=on" >>  /etc/php.ini && \
    echo "xdebug.remote_handler=dbgp" >> /etc/php.ini && \
    echo "xdebug.remote_autostart=1" >> /etc/php.ini && \
    echo "xdebug.remote_mode=req" >> /etc/php.ini && \
    echo "xdebug.remote_port=9009" >> /etc/php.ini && \
    echo "xdebug.idekey = \"PHPSTORM\"" >> /etc/php.ini && \
    echo "xdebug.profiler_enable=on" >> /etc/php.ini && \
    echo "xdebug.profiler_output_dir=\"/app/backend-api/var/logs/xdebug\"" >> /etc/php.ini && \
    echo "xdebug.profiler_append=on" >> /etc/php.ini; \
fi

# install composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
ENV COMPOSER_VERSION 1.5.2
RUN curl -s -f -L -o /tmp/installer.php https://raw.githubusercontent.com/composer/getcomposer.org/da290238de6d63faace0343efbdd5aa9354332c5/web/installer \
 && php -r " \
    \$signature = '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410'; \
    \$hash = hash('SHA384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
        unlink('/tmp/installer.php'); \
        echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
        exit(1); \
    }" \
 && php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION} \
 && composer --ansi --version --no-interaction \
 && rm -rf /tmp/* /tmp/.htaccess

# install phpUnit
RUN wget https://phar.phpunit.de/phpunit-5.7.phar && chmod +x phpunit-5.7.phar && mv phpunit-5.7.phar /usr/local/bin/phpunit

EXPOSE 9000

# Prepare fixuid
RUN groupadd -g 1000 docker && \
    useradd -u 1000 -g docker -d /home/docker -s /bin/sh docker

# Install fixuid
RUN USER=docker && \
    GROUP=docker && \
    curl -SsL https://github.com/boxboat/fixuid/releases/download/v0.3/fixuid-0.3-linux-amd64.tar.gz | tar -C /usr/local/bin -xzf - && \
    chown root:root /usr/local/bin/fixuid && \
    chmod 4755 /usr/local/bin/fixuid && \
    mkdir -p /etc/fixuid && \
    printf "user: $USER\ngroup: $GROUP\npaths: ['/app/backend-api', '/var/lib/php/session']\n" > /etc/fixuid/config.yml

# Fix php sessions & php
RUN rm -rf /var/lib/php/session && \
    mkdir -p /var/lib/php/session && \
    mkdir -p /run/php-fpm && \
    chown -R docker:docker /var/lib/php/session /run/php-fpm

USER docker:docker
ENTRYPOINT ["fixuid"]

COPY .deploy/docker/local/backend-api-entrypoint.sh /entrypoint.sh
CMD ["/entrypoint.sh"]
