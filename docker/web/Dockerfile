FROM php:7.1-alpine

#change uid of www-data
RUN echo http://dl-2.alpinelinux.org/alpine/edge/community/ >> /etc/apk/repositories
ARG PUID
RUN apk --no-cache add shadow && usermod -u ${PUID} www-data

RUN docker-php-ext-install pdo pdo_mysql

COPY ./php.ini /usr/local/etc/php/php.ini

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

ARG BUILD_ENV
RUN echo "$BUILD_ENV"
COPY ./init_${BUILD_ENV}.sh /init.sh
COPY ./wait-for-mysql.sh /wait-for-mysql.sh
RUN chown -R www-data:www-data /home/www-data

RUN if [ -d /var/www ]; then \
    chown -R www-data:www-data /var/www/var \
;else \
    mkdir /var/www && \
    chown www-data:www-data /var/www \
;fi

USER www-data
WORKDIR /var/www

ENV PORT 8080
ENTRYPOINT /init.sh
