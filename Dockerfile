# официальный образ Apache с PHP
FROM php:7.4-apache

#mysqli для PHP
RUN docker-php-ext-install mysqli

#  какие порты слушать во время выполнения
EXPOSE 80

