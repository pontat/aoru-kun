FROM node:14.16.1 as nodeBuild
WORKDIR /app
COPY ./src /app
RUN npm install && npm run prod

FROM php:8.0-apache

RUN apt-get update && apt-get install -y zip unzip && \
    docker-php-ext-install pdo pdo_mysql sockets

EXPOSE 8080
WORKDIR /var/www/
COPY ./src /var/www/
COPY /src/.env.production /var/www/.env
COPY --from=nodeBuild /app/public /var/www/public/

COPY --from=composer:2.0.13 /usr/bin/composer /usr/bin/composer
RUN composer install --optimize-autoloader --no-dev
RUN php artisan key:generate

COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN chmod 777 -R /var/www/storage/ && \
    echo "Listen 8080" >> /etc/apache2/ports.conf && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite