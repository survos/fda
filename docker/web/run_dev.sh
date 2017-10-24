#!/usr/bin/env sh

#composer install

/wait-for-mysql.sh
[[ $? -ne 0 ]] && exit $? # Exit if non-zero exit code

bin/load-data
php -S 0.0.0.0:8080 -t /var/www/web /var/www/web/app_dev.php
