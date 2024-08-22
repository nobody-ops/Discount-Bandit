#!/bin/sh

cp .env.example .env

php artisan discount:fill-supervisor-workers

php artisan storage:link
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear

php artisan key:generate --force

printenv > /etc/environment

php artisan migrate --seed --force

php artisan icons:cache
php artisan config:cache
php artisan make:filament-user --name=$DEFAULT_USER --email=$DEFAULT_EMAIL --password=$DEFAULT_PASSWORD

php artisan octane:install --server=frankenphp

Xvfb :99 -screen 0 2000x2000x24 & export DISPLAY=:99


supervisord -c  /etc/supervisor/conf.d/supervisord.conf