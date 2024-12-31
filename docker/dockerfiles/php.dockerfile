FROM php:8.2.27-fpm-alpine3.20

# WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html
COPY . .

RUN chown -R www-data:www-data /var/www/html/public
RUN chown -R www-data:www-data /var/www/html/storage
