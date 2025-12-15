# Start from the official PHP-Apache image
FROM php:8.2-apache

# 1. Set the working directory
WORKDIR /var/www/html

# 2. Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# 3. Install PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli

# 4. FIX: Resolve the "More than one MPM loaded" error
#    We disable the default 'mpm_event' and force 'mpm_prefork' (required for PHP).
#    (Removed 'rewrite' as requested)
RUN a2dismod mpm_event && a2enmod mpm_prefork

# 5. Download and install Composer globally
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

# 6. Copy ONLY your composer files from 'src'
COPY src/composer.json src/composer.lock ./

# 7. Install all your libraries
RUN composer install --no-dev --optimize-autoloader

# 8. Copy the rest of your PHP code
COPY ./src /var/www/html/

# 9. Create AND set permissions for your SINGLE volume mount point
RUN mkdir -p /var/www/html/permanent-data && \
    chmod -R 777 /var/www/html/permanent-data