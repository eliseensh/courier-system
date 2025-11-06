# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring gd \
    && a2enmod rewrite

# Copy composer files first for caching
COPY composer.json composer.lock ./

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Copy the rest of the application
COPY . .

# Set proper permissions for Laravel folders
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Generate application key (optional, if key not already in .env)
# RUN php artisan key:generate

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
