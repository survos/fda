FROM php:7.1-alpine

#change uid of www-data
RUN echo http://dl-2.alpinelinux.org/alpine/edge/community/ >> /etc/apk/repositories
ARG PUID
RUN apk --no-cache add shadow && usermod -u ${PUID} www-data

#php
RUN docker-php-ext-install pdo pdo_mysql
COPY ./docker/web/php.ini /usr/local/etc/php/php.ini

#composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

ARG BUILD_ENV
#RUN echo "$BUILD_ENV"
COPY ./docker/web/run_${BUILD_ENV}.sh /run.sh
COPY ./docker/web/wait-for-mysql.sh /wait-for-mysql.sh
COPY ./docker/web/build.sh /build.sh



#RUN chown -R www-data:www-data /home/www-data

COPY . /var/www
RUN chown -R ${PUID} /var/www/var

#RUN chown -R www-data:www-data /home/www-data
#RUN if [ -d /var/www ]; then \
#        chown -R www-data:www-data /var/www/var \
#    ;else \
#        mkdir /var/www && \
#        mkdir /var/www/web && \
#        chown www-data:www-data /var/www \
#    ;fi
#
#RUN ls -alh /var/www

USER www-data
WORKDIR /var/www
RUN /build.sh
#ENV PORT 8080
EXPOSE 8080
ENTRYPOINT /run.sh
#tail -f /dev/null
