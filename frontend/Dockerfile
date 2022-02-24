FROM harbor-1.divante.pl/proxy/library/nginx:1.17.8-alpine
MAINTAINER ŁB <ŁB@divante.pl>

# Copy Nginx configuration file
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copy built sourcecode
COPY public /var/www/frontend

# Copy and enable maintenance commands
COPY build/maintenance-start /bin/maintenance-start
COPY build/maintenance-stop /bin/maintenance-stop
RUN chmod +x /bin/maintenance-start
RUN chmod +x /bin/maintenance-stop

WORKDIR /var/www/frontend