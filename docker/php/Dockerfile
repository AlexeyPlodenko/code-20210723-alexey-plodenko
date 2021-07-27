FROM php:8.0-apache
RUN a2enmod rewrite \
    && apt-get update \
    && docker-php-ext-install mysqli pdo pdo_mysql
ENV PLATFORM="docker"
RUN export PLATFORM="docker"
