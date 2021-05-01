FROM php:8.0-fpm

COPY --from=composer:2.0.13 /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y zip unzip && \
    docker-php-ext-install pdo pdo_mysql