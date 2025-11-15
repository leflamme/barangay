# Start from the official PHP-Apache image
FROM php:8.2-apache

# 1. Set the working directory
WORKDIR /var/www/html

# 2. Install system dependencies
#    We need 'git' and 'unzip' for Composer to work
#    We need 'curl' to download the Composer installer
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# 3. Install PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli

# 4. Enable pretty URLs
RUN a2enmod rewrite

# 5. Download and install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 6. Copy ONLY your composer files from 'src'
#    This is for build caching. This step won't re-run unless composer.json changes.
COPY src/composer.json src/composer.lock ./

# 7. Install all your libraries (This will now work)
#    This creates the 'vendor' folder on the server
RUN composer install --no-dev --optimize-autoloader

# 8. Copy the rest of your PHP code
COPY ./src /var/www/html/

# 9. Create AND set permissions for your SINGLE volume mount point
#    This creates the 'permanent-data' folder. The volume will be mounted here.
#    The subfolders (backup, images) will be created by your PHP scripts.
RUN mkdir -p /var/www/html/permanent-data && \
    chmod -R 777 /var/www/html/permanent-data