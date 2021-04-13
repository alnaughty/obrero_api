#!/bin/sh

cd /var/www/app

source ./dotenv
.env set APP_ENV=${APP_ENV}
.env set APP_URL=${APP_URL}
.env set DB_HOST=${DB_HOST}
.env set DB_PORT=${DB_PORT}
.env set DB_DATABASE=${DB_DATABASE}
.env set DB_USERNAME=${DB_USERNAME}
.env set DB_PASSWORD=${DB_PASSWORD}
.env set MAIL_DRIVER=${MAIL_DRIVER}
.env set MAIL_HOST=${MAIL_HOST}
.env set MAIL_PORT=${MAIL_PORT}
.env set MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS}
.env set MAIL_FROM_NAME=${MAIL_FROM_NAME}
.env set MAIL_USERNAME=${MAIL_USERNAME}
.env set MAIL_PASSWORD=${MAIL_PASSWORD}

exec /usr/bin/supervisord -n