FROM php:8.3-fpm-alpine
COPY --from=composer/composer:2-bin /composer /usr/bin/composer
COPY . /app
WORKDIR /app
