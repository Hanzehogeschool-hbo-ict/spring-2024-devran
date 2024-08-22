# Dockerfile for PHP Hive server

FROM php:8.3

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update
RUN apt install unzip

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

WORKDIR /www/html

COPY . .

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY ./composer.* .

RUN composer install

EXPOSE 8000

WORKDIR /www/html/public
CMD php -S 0.0.0.0:8000
