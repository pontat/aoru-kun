FROM node:14.16.1 as nodeBuild
WORKDIR /app
COPY ./src /app
RUN npm install && npm run prod

FROM php:8.0-apache

RUN apt-get update && apt-get install -y zip unzip && \
    docker-php-ext-install pdo pdo_mysql sockets

COPY --from=composer:2.0.13 /usr/bin/composer /usr/bin/composer

EXPOSE 8080
WORKDIR /var/www/
COPY ./src /var/www/
COPY --from=nodeBuild /app/public /var/www/public/

RUN composer install --optimize-autoloader --no-dev
RUN php artisan key:generate  \
    && php artisan config:cache \
    && php artisan view:cache

COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY /src/.env.example /var/www/.env
RUN chmod 777 -R /var/www/storage/ && \
    echo "Listen 8080" >> /etc/apache2/ports.conf && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite