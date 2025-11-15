# Start from the official PHP-Apache image
FROM php:8.2-apache

# 1. Set the working directory
WORKDIR /var/www/html

# 2. Install extensions (This will be cached)
RUN docker-php-ext-install pdo_mysql mysqli

# 3. Enable pretty URLs
RUN a2enmod rewrite

# 4. Copy ONLY your composer files from 'src'
COPY src/composer.json src/composer.lock ./

# 5. THIS IS THE FIX: Install all your libraries (Twilio, PHPMailer, etc.)
# This creates the 'vendor' folder on the server
RUN composer install --no-dev --optimize-autoloader

# 6. Copy the rest of your PHP code
COPY ./src /var/www/html/