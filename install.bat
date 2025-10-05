@echo off
echo ===============================================
echo     منظومة التبيان - سكريبت التثبيت
echo ===============================================
echo.

echo [1/8] فحص متطلبات PHP...
php --version >nul 2>&1
if errorlevel 1 (
    echo خطأ: PHP غير مثبت أو غير موجود في PATH
    echo يرجى تثبيت PHP 8.1 أو أحدث
    pause
    exit /b 1
)

echo [2/8] فحص Composer...
composer --version >nul 2>&1
if errorlevel 1 (
    echo خطأ: Composer غير مثبت
    echo يرجى تحميل وتثبيت Composer من https://getcomposer.org
    pause
    exit /b 1
)

echo [3/8] تثبيت تبعيات PHP...
composer install --no-dev --optimize-autoloader

echo [4/8] إنشاء ملف البيئة...
if not exist .env (
    copy .env.example .env
    echo تم إنشاء ملف .env - يرجى تحرير إعدادات قاعدة البيانات
)

echo [5/8] توليد مفتاح التطبيق...
php artisan key:generate

echo [6/8] إنشاء قاعدة البيانات SQLite...
if not exist database\tibyan.sqlite (
    echo. > database\tibyan.sqlite
)

echo [7/8] تشغيل ترحيلات قاعدة البيانات...
php artisan migrate --seed --force

echo [8/8] تنظيف وتحسين النظام...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo ===============================================
echo        تم التثبيت بنجاح!
echo ===============================================
echo.
echo لتشغيل النظام:
echo   php artisan serve
echo.
echo ثم افتح المتصفح على: http://localhost:8000
echo.
echo بيانات الدخول الافتراضية:
echo   البريد: admin@tibyan.com
echo   كلمة المرور: admin123
echo.
echo ===============================================
pause