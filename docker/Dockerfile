FROM php:8.0-apache

RUN apt-get update && apt-get install -y zip unzip && \
    docker-php-ext-install pdo pdo_mysql sockets

COPY --from=composer:2.0.13 /usr/bin/composer /usr/bin/composer
COPY --from=node:14.16.1 /usr/local/bin /usr/local/bin
COPY --from=node:14.16.1 /usr/local/lib /usr/local/lib

EXPOSE 8080
WORKDIR /var/www/
COPY ./src /var/www/

RUN composer install
RUN php artisan key:generate  \
    && php artisan config:cache \
    && php artisan view:cache

RUN npm install && npm run watch

COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY /src/.env.example /var/www/.env
RUN chmod 777 -R /var/www/storage/ && \
    echo "Listen 8080" >> /etc/apache2/ports.conf && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite