FROM php:8.0-apache

RUN apt-get update && apt-get install -y zip unzip && \
    docker-php-ext-install pdo pdo_mysql sockets

COPY --from=composer:2.0.13 /usr/bin/composer /usr/bin/composer
COPY --from=node:14.16.1 /usr/local/bin /usr/local/bin
COPY --from=node:14.16.1 /usr/local/lib /usr/local/lib

EXPOSE 8080
WORKDIR /var/www/

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN echo "Listen 8080" >> /etc/apache2/ports.conf && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite