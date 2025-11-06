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
# Ensure correct permissions
# -------------------------------
RUN mkdir -p storage bootstrap/cache database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database

# -------------------------------
# Install PHP dependencies
# -------------------------------
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# -------------------------------
# Laravel cache (optional)
# -------------------------------
RUN php artisan config:cache && php artisan route:cache

# -------------------------------
# Expose port 80
# -------------------------------
EXPOSE 80

# -------------------------------
# Start Apache server
# -------------------------------
CMD ["apache2-foreground"]
