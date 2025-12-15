# CHANGE 1: Use 'bullseye' (Debian 11) instead of latest to avoid the conflict
FROM php:8.2-apache-bullseye

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

# 4. Download and install Composer globally
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

# 5. Copy ONLY your composer files from 'src'
COPY src/composer.json src/composer.lock ./

# 6. Install all your libraries
RUN composer install --no-dev --optimize-autoloader

# 7. Copy the rest of your PHP code
COPY ./src /var/www/html/

# 8. Create AND set permissions for your SINGLE volume mount point
RUN mkdir -p /var/www/html/permanent-data && \
    chmod -R 777 /var/www/html/permanent-data

# CHANGE 2: Run the Fix at the VERY END so nothing undoes it.
# We remove BOTH event and worker modules to be 100% sure.
RUN rm -f /etc/apache2/mods-enabled/mpm_event.conf \
    && rm -f /etc/apache2/mods-enabled/mpm_event.load \
    && rm -f /etc/apache2/mods-enabled/mpm_worker.conf \
    && rm -f /etc/apache2/mods-enabled/mpm_worker.load \
    && a2enmod mpm_prefork