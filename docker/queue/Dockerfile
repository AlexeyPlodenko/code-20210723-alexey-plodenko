FROM php:8.0-cli

RUN apt-get update \
    && docker-php-ext-install mysqli pdo pdo_mysql

ENV PLATFORM="docker"
RUN export PLATFORM="docker"

WORKDIR /var/www
CMD [ "php", "/var/www/artisan", "queue:listen" ]
