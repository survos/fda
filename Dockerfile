# Inherit from Heroku's stack
FROM heroku/heroku:16

# Internally, we arbitrarily use port 3000
ENV PORT 3000

# Which versions?
ENV PHP_VERSION 7.0.10
ENV HTTPD_VERSION 2.4.20
ENV NGINX_VERSION 1.8.1
ENV COMPOSER_VERSION 1.2.1

# Install mysql-client tools for ease of development with WP-CLI
RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get install --yes mysql-client

# Create some needed directories
RUN mkdir -p /app/.heroku/php /app/.profile.d
WORKDIR /app/user

# so we can run PHP in here
ENV PATH /app/.heroku/php/bin:/app/.heroku/php/sbin:$PATH

# Install Apache
RUN curl --silent --location https://lang-php.s3.amazonaws.com/dist-cedar-16-stable/apache-$HTTPD_VERSION.tar.gz | tar xz -C /app/.heroku/php
# Config
RUN curl --silent --location https://raw.githubusercontent.com/heroku/heroku-buildpack-php/5a770b914549cf2a897cbbaf379eb5adf410d464/conf/apache2/httpd.conf.default > /app/.heroku/php/etc/apache2/httpd.conf
# FPM socket permissions workaround when run as root
RUN echo "\n\
Group root\n\
" >> /app/.heroku/php/etc/apache2/httpd.conf

# Install Nginx
RUN curl --silent --location https://lang-php.s3.amazonaws.com/dist-cedar-16-stable/nginx-$NGINX_VERSION.tar.gz | tar xz -C /app/.heroku/php
# Config
RUN curl --silent --location https://raw.githubusercontent.com/heroku/heroku-buildpack-php/5a770b914549cf2a897cbbaf379eb5adf410d464/conf/nginx/nginx.conf.default > /app/.heroku/php/etc/nginx/nginx.conf
# FPM socket permissions workaround when run as root
RUN echo "\n\
user nobody root;\n\
" >> /app/.heroku/php/etc/nginx/nginx.conf

# Install PHP
RUN curl --silent --location https://lang-php.s3.amazonaws.com/dist-cedar-16-stable/php-$PHP_VERSION.tar.gz | tar xz -C /app/.heroku/php
# Config
RUN mkdir -p /app/.heroku/php/etc/php/conf.d
RUN curl --silent --location https://raw.githubusercontent.com/heroku/heroku-buildpack-php/master/support/build/_conf/php/php.ini > /app/.heroku/php/etc/php/php.ini

# Install Redis extension for PHP 7
RUN curl --silent --location https://lang-php.s3.amazonaws.com/dist-cedar-16-stable/extensions/no-debug-non-zts-20151012/redis-3.0.0.tar.gz | tar xz -C /app/.heroku/php

# Enable all optional exts
RUN echo "\n\
memory_limit = 512M \n\
user_ini.cache_ttl = 30 \n\
opcache.enable_cli = 1 \n\
opcache.validate_timestamps = 1 \n\
opcache.revalidate_freq = 0 \n\
opcache.fast_shutdown = 0 \n\
extension=bcmath.so \n\
extension=calendar.so \n\
extension=exif.so \n\
extension=ftp.so \n\
extension=gd.so \n\
extension=gettext.so \n\
extension=intl.so \n\
extension=mbstring.so \n\
extension=pcntl.so \n\
extension=redis.so \n\
extension=shmop.so \n\
extension=soap.so \n\
extension=sqlite3.so \n\
extension=pdo_sqlite.so \n\
extension=xmlrpc.so \n\
extension=xsl.so\n\
" >> /app/.heroku/php/etc/php/php.ini

# Enable timestamps validation for opcache for development
RUN sed -i /opcache.validate_timestamps/d /app/.heroku/php/etc/php/conf.d/010-ext-zend_opcache.ini

# Install Composer
RUN curl --silent --location https://lang-php.s3.amazonaws.com/dist-cedar-16-stable/composer-$COMPOSER_VERSION.tar.gz | tar xz -C /app/.heroku/php

RUN groupadd -g 1000 ubuntu && \
    useradd -u 1000 -g ubuntu -m ubuntu

#https://github.com/moby/moby/issues/6119
COPY . /app/user/
USER root
RUN chown -R ubuntu:ubuntu /app/user/var/

USER ubuntu

#RUN composer install #chown /vendor is painfully long
RUN bin/load-data

# Export heroku bin
ENV PATH /app/user/bin:$PATH

CMD php bin/console server:run 0.0.0.0:$PORT
