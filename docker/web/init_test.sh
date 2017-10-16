#!/usr/bin/env sh

composer install

/wait-for-mysql.sh
[[ $? -ne 0 ]] && exit $? # Exit if non-zero exit code

bin/load-data
php bin/console server:run 0.0.0.0:${PORT}
