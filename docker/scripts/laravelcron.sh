#!/usr/bin/env bash
cd /root/automessage/ && docker-compose exec --user root php php /var/www/html/artisan schedule:run