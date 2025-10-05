#!/bin/bash

echo "==============================================="
echo "     منظومة التبيان - سكريبت التثبيت"
echo "==============================================="
echo

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

# Check PHP
echo "[1/8] فحص متطلبات PHP..."
if ! command -v php &> /dev/null; then
    print_error "PHP غير مثبت أو غير موجود في PATH"
    echo "يرجى تثبيت PHP 8.1 أو أحدث"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
print_status "تم العثور على PHP $PHP_VERSION"

# Check Composer
echo "[2/8] فحص Composer..."
if ! command -v composer &> /dev/null; then
    print_error "Composer غير مثبت"
    echo "يرجى تحميل وتثبيت Composer من https://getcomposer.org"
    exit 1
fi
print_status "تم العثور على Composer"

# Install PHP dependencies
echo "[3/8] تثبيت تبعيات PHP..."
composer install --no-dev --optimize-autoloader
if [ $? -eq 0 ]; then
    print_status "تم تثبيت التبعيات بنجاح"
else
    print_error "فشل في تثبيت التبعيات"
    exit 1
fi

# Create environment file
echo "[4/8] إعداد ملف البيئة..."
if [ ! -f .env ]; then
    cp .env.example .env
    print_status "تم إنشاء ملف .env"
    print_warning "يرجى تحرير إعدادات قاعدة البيانات في ملف .env"
else
    print_status "ملف .env موجود بالفعل"
fi

# Generate application key
echo "[5/8] توليد مفتاح التطبيق..."
php artisan key:generate --force
print_status "تم توليد مفتاح التطبيق"

# Create SQLite database
echo "[6/8] إنشاء قاعدة البيانات SQLite..."
mkdir -p database
if [ ! -f database/tibyan.sqlite ]; then
    touch database/tibyan.sqlite
    print_status "تم إنشاء قاعدة البيانات SQLite"
else
    print_status "قاعدة البيانات SQLite موجودة بالفعل"
fi

# Run migrations
echo "[7/8] تشغيل ترحيلات قاعدة البيانات..."
php artisan migrate --seed --force
if [ $? -eq 0 ]; then
    print_status "تم تشغيل الترحيلات بنجاح"
else
    print_error "فشل في تشغيل الترحيلات"
    exit 1
fi

# Optimize application
echo "[8/8] تحسين النظام..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_status "تم تحسين النظام"

echo
echo "==============================================="
echo "        تم التثبيت بنجاح!"
echo "==============================================="
echo
echo "لتشغيل النظام:"
echo "  php artisan serve"
echo
echo "ثم افتح المتصفح على: http://localhost:8000"
echo
echo "بيانات الدخول الافتراضية:"
echo "  البريد: admin@tibyan.com"
echo "  كلمة المرور: admin123"
echo
echo "==============================================="