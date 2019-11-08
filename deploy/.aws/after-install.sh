#!/bin/bash

ln -nfs /var/www/lurn.com/shared_files/storage /var/www/lurn.com/release/storage
chmod -R a+w /var/www/lurn.com/release/bootstrap/cache
chown -R www-data:www-data /var/www/lurn.com/release
