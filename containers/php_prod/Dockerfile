FROM harbor-1.divante.pl/proxy/library/php:7.4.0-fpm
MAINTAINER ŁB <ŁB@divante.pl>
RUN usermod -u 1000 www-data
RUN apt-get  --allow-releaseinfo-change update && apt-get install -y \
    git \
    openssl \
    mc

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

RUN chown -R www-data:www-data /var/www

# Install zip module for PHP
RUN apt-get install -y zlib1g zlib1g-dbg zlib1g-dev zlibc
RUN apt-get install -y libzip-dev
RUN docker-php-ext-install zip

# Install GD support
RUN apt-get install -y libpng-dev
RUN docker-php-ext-install gd

# Install Redis module
RUN pecl install -o -f redis
RUN rm -rf /tmp/pear
RUN docker-php-ext-enable redis

# Copying configuration files
COPY memory.ini /usr/local/etc/php/conf.d/memory.ini

# Support for parallel downloads in Composer
RUN composer global require hirak/prestissimo

# ssh and rsync packages
RUN apt-get install -y openssh-client rsync

# Install intl module
RUN apt-get install -y g++ zlib1g-dev libicu-dev
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
