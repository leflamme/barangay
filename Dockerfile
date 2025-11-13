# Start from the official PHP-Apache image
FROM php:8.2-apache

# 1. Install extensions FIRST (for better caching)
RUN docker-php-ext-install pdo_mysql mysqli

# 2. Enable pretty URLs
RUN a2enmod rewrite

# 3. Create AND set permissions for ALL your upload folders
# This is the fix that solves your problem.
# We create a parent 'uploads' folder to mount our volume to.
RUN mkdir -p /var/www/html/uploads/backup && \
    mkdir -p /var/www/html/uploads/assets/dist/img && \
    chmod -R 777 /var/www/html/uploads

# 4. Copy your PHP code LAST
COPY ./src /var/www/html/