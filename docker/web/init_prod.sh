#!/usr/bin/env sh

target=/var/www
if test "$(ls -A $target)"; then
    echo "WARNING: directory $target is not empty"
else
    cp -R /source/* $target
fi

/wait-for-mysql.sh
[[ $? -ne 0 ]] && exit $? # Exit if non-zero exit code

bin/load-data
php bin/console server:run 0.0.0.0:${PORT}
