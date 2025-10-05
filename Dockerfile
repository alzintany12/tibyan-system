# ================================
# Dockerfile متقدم لمشروع Laravel
# ================================

# استخدم PHP 8.2 مع Apache
FROM php:8.2-apache

# تعيين مجلد العمل
WORKDIR /var/www/html

# ================================
# تثبيت الاعتمادات الأساسية
# ================================
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# تمكين mod_rewrite للـ Apache
RUN a2enmod rewrite

# ضبط DocumentRoot على مجلد public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# السماح بـ .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# نسخ Composer من الصورة الرسمية
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ================================
# نسخ المشروع وتثبيت Composer
# ================================
COPY . .

# تثبيت الاعتمادات PHP (Composer) بدون dev وتعجيل التحميل
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-scripts

# ================================
# ضبط الصلاحيات اللازمة لمجلدات Laravel
# ================================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache public

# تعيين Apache للعمل على المنفذ 80
EXPOSE 80

# تشغيل Apache في الوضع الأمامي
CMD ["apache2-foreground"]
