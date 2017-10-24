#!/usr/bin/env sh

/wait-for-mysql.sh
[[ $? -ne 0 ]] && exit $? # Exit if non-zero exit code

bin/load-data
php -S 0.0.0.0:8080 /var/www/web/app_dev.php
