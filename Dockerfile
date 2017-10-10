FROM php:7.1.10-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

COPY docker/web/php.ini /usr/local/etc/php/php.ini

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /var/www

# Copy the application files to the container
ADD . /var/www

# Can be removed once https://github.com/moby/moby/pull/34263 is released
RUN chown -R www-data:www-data /var/www/var /home/www-data

# Run composer as www-data
# Can be moved before application files are added to the container once
# the issue mentioned above is fixed and released
USER www-data

#RUN composer install  --no-interaction --optimize-autoloader --no-dev --prefer-dist && \
#    rm -rf /home/www-data/.composer/cache

#USER root


#RUN composer install #chown /vendor is painfully long
#RUN bin/load-data

# Export heroku bin
#ENV PATH /app/user/bin:$PATH


