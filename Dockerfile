# Keep using Bullseye (Debian 11) for stability
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

# 5. Copy ONLY your composer files
COPY src/composer.json src/composer.lock ./

# 6. Install all your libraries
RUN composer install --no-dev --optimize-autoloader

# 7. Copy the rest of your PHP code
COPY ./src /var/www/html/

# 8. Create AND set permissions for your volume
RUN mkdir -p /var/www/html/permanent-data && \
    chmod -R 777 /var/www/html/permanent-data

# --- THE FINAL FIX ---
# We added 'rewrite' to the a2enmod command below.
CMD ["/bin/bash", "-c", "rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && a2enmod mpm_prefork rewrite && apache2-foreground"]