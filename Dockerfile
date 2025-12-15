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

# --- THE ULTIMATE FIX ---
# Instead of trusting the build, we fix it when the container STARTS.
# This runs every single time the app boots up.
# 1. Force delete mpm_event and mpm_worker files (using wildcard *)
# 2. Enable mpm_prefork
# 3. Start Apache normally
CMD ["/bin/bash", "-c", "rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && a2enmod mpm_prefork && apache2-foreground"]