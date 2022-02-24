FROM harbor-1.divante.pl/proxy/divante/adventure-php
MAINTAINER ŁB <ŁB@divante.pl>

# Installing xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN apt-get update  --allow-releaseinfo-change

# Add support for mailcatcher
RUN apt-get install -y msmtp

COPY ./config/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./config/sendmail.ini /usr/local/etc/php/conf.d/sendmail.ini
COPY ./config/smtp.conf /etc/msmtprc

