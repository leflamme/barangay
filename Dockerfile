# Start from the official PHP-Apache image
FROM php:8.2-apache

# 1. Install extensions (This will be cached)
RUN docker-php-ext-install pdo_mysql mysqli

# 2. Enable pretty URLs
RUN a2enmod rewrite

# 3. Copy your PHP code
COPY ./src /var/www/html/