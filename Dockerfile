# Dockerfile for PHP Hive server

FROM php:8.3

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update
RUN apt install unzip

WORKDIR /www/html

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY ./composer.* .

RUN composer install

COPY . .

RUN composer dump-autoload --optimize

EXPOSE 8000

WORKDIR /www/html/public
CMD php -S 0.0.0.0:8000