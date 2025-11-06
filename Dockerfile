// ...existing code...
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies, configure GD, enable apache rewrite
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    ca-certificates \
    gnupg \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip mbstring gd \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Install Composer (verify signature recommended)
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Copy only composer files first to leverage Docker cache
COPY composer.json composer.lock* /var/www/html/

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copy the rest of the application
COPY . /var/www/html

# Ensure public/ is Apache DocumentRoot (optional: update Apache config instead)
# If you want to change the Apache DocumentRoot, add a virtual host or set env var.
# Example: setenv APACHE_DOCUMENT_ROOT /var/www/html/public (requires enabling in apache conf)

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"] 