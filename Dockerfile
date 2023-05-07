FROM php:7.4-apache

COPY . /var/www/html

RUN apt-get update && \
    apt-get install -y libpq-dev && \
    a2enmod rewrite
