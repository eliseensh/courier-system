FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip curl libzip-dev libonig-dev libpng-dev libjpeg-dev libfreetype6-dev zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip mbstring gd \
    && a2enmod rewrite headers expires \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && php -r "unlink('composer-setup.php');"

# Copy all application files first (artisan must exist)
COPY . /var/www/html

# Ensure Laravel directories exist
RUN mkdir -p bootstrap/cache storage/framework/{sessions,views,cache} storage/logs \
 && touch bootstrap/cache/services.php bootstrap/cache/packages.php \
 && chown -R www-data:www-data bootstrap/cache storage \
 && chmod -R 775 bootstrap/cache storage

# Set environment variables for composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/tmp

# Run composer install
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress

# Final permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]
