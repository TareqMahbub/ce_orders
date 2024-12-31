FROM nginx:1.27.3

WORKDIR /etc/nginx/conf.d

COPY ./docker/nginx/nginx.conf .

RUN mv nginx.conf default.conf

WORKDIR /var/www/html

