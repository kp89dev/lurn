#!/bin/bash

if [ "$DEPLOYMENT_GROUP_NAME" == "LurnNationStaging" ]; then
    rm /var/www/lurn.com/release/.env
    cp /var/www/lurn.com/shared_files/.env /var/www/lurn.com/release/.env
else
    cp -R /var/www/lurn.com/release/deploy/nginx/* /etc/nginx/
    cp -R /var/www/lurn.com/release/deploy/php/* /etc/php/7.1/fpm
fi

php /var/www/lurn.com/release/artisan migrate --force --no-interaction
ln -nfs /var/www/lurn.com/release /var/www/lurn.com/live
chown -R www-data:www-data /var/www/lurn.com/live


sudo service nginx reload
sudo service php7.1-fpm restart
php /var/www/lurn.com/live/artisan queue:restart
