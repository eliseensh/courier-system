# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# -------------------------------
# Install system dependencies (PHP + Node.js)
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
    nodejs \
    npm \
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
# Copy Laravel project
# -------------------------------
COPY . /var/www/html

# -------------------------------
# Fix permissions
# -------------------------------
RUN mkdir -p storage/framework/{cache,sessions,views} \
    storage/logs storage/app/public bootstrap/cache database \
 && touch database/database.sqlite \
 && chmod -R 775 storage bootstrap/cache database \
 && chown -R www-data:www-data storage bootstrap/cache database /var/www/html/public

# -------------------------------
# Configure Apache DocumentRoot
# -------------------------------
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && chown -R www-data:www-data /var/www/html/public

# -------------------------------
# Install PHP dependencies (no dev)
# -------------------------------
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# -------------------------------
# Install Node dependencies & build assets (Linux-compatible)
# -------------------------------
# Skip platform-specific packages (like lightningcss-win32)
RUN npm install --no-optional --legacy-peer-deps && npm run build

# -------------------------------
# Expose port 80
# -------------------------------
EXPOSE 80

# -------------------------------
# Start Laravel at runtime
# -------------------------------
ENTRYPOINT ["sh", "-c", "php artisan key:generate && php artisan migrate --force && php artisan storage:link && php artisan config:cache && php artisan route:cache && apache2-foreground"]
