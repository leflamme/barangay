# Start from the official PHP-Apache image
FROM php:8.2-apache

# Install BOTH pdo_mysql and mysqli extensions
RUN docker-php-ext-install pdo_mysql mysqli

# Enable pretty URLs (optional, but good practice)
RUN a2enmod rewrite

# Copy your PHP code into the container
COPY ./src /var/www/html/