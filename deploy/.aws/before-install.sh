#!/bin/bash
if [ -d /var/www/lurn.com/release-previous ]; then
    rm -rf /var/www/lurn.com/release-previous
fi

if [ -d /var/www/lurn.com/release ]; then
    mv /var/www/lurn.com/release /var/www/lurn.com/release-previous
    mv /var/www/lurn.com/release-previous/storage /var/www/lurn.com/release-previous/storage-old
    ln -nfs /var/www/lurn.com/shared_files/storage /var/www/lurn.com/release-previous/storage
    ln -nfs /var/www/lurn.com/release-previous /var/www/lurn.com/live
    chown -R www-data:www-data /var/www/lurn.com/release-previous
    service php7.1-fpm restart
fi

mkdir -vp /var/www/lurn.com/release
