#!/bin/bash

sudo docker exec -e PHP_IDE_CONFIG="serverName=CMDHost" -e XDEBUG_CONFIG="remote_enable=1 remote_port=9001 remote_host=192.168.1.4 remote_autostart=1" lurncentral_php-fpm_1 php "$@"
