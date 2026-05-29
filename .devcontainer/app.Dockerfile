FROM mcr.microsoft.com/devcontainers/php:dev-8.3-apache-bullseye

COPY ./config/apache/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
# COPY ./certs /etc/nginx/certs

# RUN chmod 644 /etc/nginx/certs/*
# RUN mkdir conf.d && cd conf.d/
# COPY default.conf /etc/nginx/conf.d/

RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite