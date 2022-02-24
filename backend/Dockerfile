FROM harbor-1.divante.pl/adventure/php_prod:1.2.1
MAINTAINER ŁB <lb@divante.pl>

RUN apt-get  --allow-releaseinfo-change update && apt-get install -y nginx
RUN chown -R www-data:www-data /var/www

# Copy source code files
WORKDIR /var/www/backend
COPY app app
COPY bin bin
COPY src src
COPY web web
COPY composer.json composer.json
COPY composer.lock composer.lock
COPY deploy-ci.sh deploy-ci.sh
RUN chmod +x bin/maintenance-start
RUN chmod +x bin/maintenance-stop

RUN mkdir -p var/jwt
RUN openssl genrsa -out var/jwt/private.pem -passout pass:divante -aes256 4096
RUN openssl rsa -pubout -in var/jwt/private.pem -passin pass:divante -out var/jwt/public.pem

# Run package installation
RUN composer clear-cache
RUN composer install --no-dev
RUN chmod -R 777 var

COPY nginx.conf /etc/nginx/sites-enabled/default
COPY entrypoint.sh /etc/entrypoint.sh
RUN chmod +x /etc/entrypoint.sh

EXPOSE 80

RUN usermod -u 1000 www-data

ENTRYPOINT ["/etc/entrypoint.sh"]
