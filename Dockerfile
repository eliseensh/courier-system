# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# -------------------------------
# Install system dependencies
# -------------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    sqlite3 \
    libsqlite3-dev \
    ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite zip mbstring gd \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# -------------------------------
# Install Composer
# -------------------------------
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# -------------------------------
# Copy Laravel application
# -------------------------------
COPY . /var/www/html

# -------------------------------
# Ensure correct permissions for Laravel directories
# -------------------------------
RUN mkdir -p storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache \
             database \
    && touch database/database.sqlite \
    && chmod -R 775 storage bootstrap/cache database \
    && chown -R www-data:www-data storage bootstrap/cache database

# -------------------------------
# Set Apache DocumentRoot to Laravel's public directory
# -------------------------------
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && chown -R www-data:www-data /var/www/html/public

# -------------------------------
# Install PHP dependencies WITHOUT running scripts
# -------------------------------
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# -------------------------------
# Expose port 80
# -------------------------------
EXPOSE 80

# -------------------------------
# Start Laravel + Apache at runtime (with migrations and logging)
# -------------------------------
ENTRYPOINT ["sh", "-c", "php artisan key:generate && php artisan migrate --force && php artisan config:cache && php artisan route:cache && apache2-foreground"]