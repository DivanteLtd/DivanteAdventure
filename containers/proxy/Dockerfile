FROM harbor-1.divante.pl/proxy/library/nginx:latest
MAINTAINER ŁB <ŁB@divante.pl>
RUN usermod -u 1000 www-data
WORKDIR /etc/nginx
RUN apt-get  --allow-releaseinfo-change update && apt-get install -y \
    openssl
RUN mkdir -p certs \
    && openssl req -subj '/CN=divante.pl' -x509 -newkey rsa:4096 -nodes -keyout certs/key.pem -out certs/cert.pem -days 365
