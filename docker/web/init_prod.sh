#!/usr/bin/env sh

target=/var/www

if test "$(ls -A "/var/www")"; then
    echo "ERROR: directory $target is not empty"
    exit 1
    #rm -rf /var/www/*
fi

cp -R /source/* $target
bin/load-data
php bin/console server:run 0.0.0.0:${PORT}
