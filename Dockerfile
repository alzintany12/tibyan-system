# ================================
# Production-ready Dockerfile for Laravel (PHP 8.2 + Apache)
# ================================
FROM php:8.2-apache

ARG APP_ENV=production
ENV APP_ENV=${APP_ENV}

WORKDIR /var/www/html

# ----------------
# System packages & PHP extensions
# ----------------
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    libzip-dev \
    procps \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite headers

# Set DocumentRoot to public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
 && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copy composer binary from official composer image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy only composer files first (to use docker cache)
COPY composer.json composer.lock /var/www/html/

# Install PHP dependencies (production - no dev)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts --no-progress

# Copy application source
COPY . /var/www/html

# Remove any prebuilt bootstrap cache (prevents Sail/other dev providers being loaded)
RUN rm -f bootstrap/cache/services.php bootstrap/cache/packages.php bootstrap/cache/config.php || true

# Ensure correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && mkdir -p storage/logs

# Clear possible caches (safe-guard)
RUN php artisan config:clear || true \
 && php artisan cache:clear || true \
 && php artisan route:clear || true \
 && php artisan view:clear || true

# Optimize autoload
RUN composer dump-autoload --optimize

# Add entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
